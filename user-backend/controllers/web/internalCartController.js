import { sequelize } from "../../config/db.js";
import { consoleLog, errorMessage, logger, successMessage } from "../../helper/index.js";
import { getModuleStatus, verifyTransactionPassword } from "../../utils/index.js";

import CommissionService from "../../services/commissionService.js";
import InternalCartService from "../../services/internalCartService.js";

import { Address, Cart, CompanyProfile, Order, OrderDetail, Package, PaymentGatewayConfig, RepurchaseCategory } from "../../models/association.js";
import utilityService from "../../services/utilityService.js";

export const getRepurchaseProducts = async (req, res, next) => {
    try {
        const moduleStatus = await getModuleStatus({attributes:["repurchaseStatus","productStatus"]});

        if (!(moduleStatus.repurchaseStatus || moduleStatus.productStatus)) {
            let response = await errorMessage({ code: 1057 });
            return res.status(422).json(response);
        };

        const productList = await Package.findAll({
            attributes: ["id", "name", "price", "productId", "pairValue", "description", "image"],
            where: { type: "repurchase", active: 1 },
            include: [
                {
                    model: RepurchaseCategory,
                    attributes: ["name"]
                }
            ],
            raw: true
        });
        productList.forEach( item => {
            item.category = item["RepurchaseCategory.name"]
        })

        const response = await successMessage({data: productList});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getRepurchaseProducts",error);
        return next(error);
    }
};

export const getRepurchaseProductDetail =async (req,res,next) => {
    try {
        const productId = req.query.id;
        const productDetail = await Package.findOne({
            attributes: ["id", "name", "price", "productId", "pairValue", "description", "image"],
            where: { type: "repurchase", active: 1, id: productId },
            include: [
                {
                    model: RepurchaseCategory,
                    attributes: ["name"]
                }
            ],
            raw: true
        });
        const response = await successMessage({data: productDetail});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getRepurchaseProductDetail",error);
        return next(error);   
    }
}

export const getCart = async (req,res,next) => {
    try {
        const userId   = req.auth.user.id;
        const cartList = await Cart.findAll({
            where: { userId: userId },
            include: [
                {
                    model: Package,
                    attributes: ["id", "name", "price", "image"],
                    where: { active: 1 }
                }
            ],
            raw: true
        });
        cartList.forEach( item => {
            item.packageId = item["Package.id"];
            item.name = item["Package.name"];
            item.price = item["Package.price"];
            item.image = item["Package.image"];
            delete item["Package.id"];
            delete item["Package.name"];
            delete item["Package.price"];
            delete item["Package.image"];
        })
        const response = await successMessage({data: cartList});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getCart",error);
        return next(error);
    }
};

export const addToCart = async (req,res,next) => {
    try {
        const userId    = req.auth.user.id;
        const packageId = req.body.packageId;
        const quantity  = 1;

        const currentCart = await Cart.findOne({ where: { userId: userId, packageId: packageId } });
        if (currentCart) {
            await currentCart.increment('quantity', {'by': quantity});
        } else {
            await Cart.create({
                userId: userId,
                packageId: packageId,
                quantity: quantity
            });
        };
        const response = await successMessage({ data: 'Added to cart.'})
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM addToCart",error);
        return next(error);
    }
};

export const getRepurchaseReport = async (req,res,next) => {
    try {
        const userId     = req.auth.user.id;
        const page       = parseInt(req.query.page) || 1;
		const pageSize   = parseInt(req.query.perPage) || 10; // length
		const offset     = (page - 1) * pageSize;
		const direction  = req.query.direction || "DESC";
        const moduleStatus = await getModuleStatus({attributes:["repurchaseStatus","productStatus"]});
        if (!(moduleStatus.repurchaseStatus || moduleStatus.productStatus)) {
            let response = await errorMessage({ code: 1057 });
            return res.status(422).json(response);
        };
        const data = await InternalCartService.getReportData(userId,pageSize,offset,direction);

        const response = await successMessage({data: data});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getRepurchaseReport",error);
        return next(error);
    }
};

export const getRepurchaseInvoice = async (req,res,next) => {
    try {
        const orderId = req.query.orderId;
        const moduleStatus = await getModuleStatus({attributes:["repurchaseStatus","productStatus"]});
        
        if (!(moduleStatus.repurchaseStatus || moduleStatus.productStatus)) {
            let response = await errorMessage({ code: 1057 });
            return res.status(422).json(response);
        };
        // TODO 
        const data = await InternalCartService.getReportInvoice(orderId);
        // const  aaddress= await InternalCartService.getOrder
        // logger.debug("data",data)
        const response = await successMessage({data: data});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getRepurchaseReport",error);
        return next(error);
    }
};

export const decrementCartItem = async (req,res,next) => {
    try {
        const userId    = req.auth.user.id;
        const packageId = req.body.packageId;
        const quantity  = 1;
        const cart = await Cart.findOne({ where: { userId, packageId } });
        if(!cart) {
            const response = await errorMessage({code: 1026, statusCode:422});
            return res.status(response.code).json(response.data);
        }
        if(!(parseInt(cart.quantity) - 1)){
            await cart.destroy();
        } else {
            await Cart.decrement({ quantity: quantity }, { where: { userId, packageId } });
        }
        const response = await successMessage({ data: 'Updated cart.'})
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM decrementCartItem",error);
        return next(error);
    }
};

export const removeCartItem = async (req,res,next) => {
    try {
        const userId        = req.auth.user.id;
        const packageId     = req.body.packageId;
        let result;
        if (!packageId) {
            let response = await errorMessage({ code: 1050, statusCode: 422 });
            return res.status(response.code).json(response);
        };
        if (packageId == "all") {
            await Cart.destroy({ where: { userId: userId } });
        } else {
            await Cart.destroy({ where:{userId, packageId: parseInt(packageId)}});
        };
        const response = await successMessage({ data: 'Updated cart.'})
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM removeCartItem",error);
        return next(error);
    }
};

export const getAddress = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;

        const userAddress = await Address.findAll({
            where: { userId: userId, deletedAt: null },
            raw: true
        });
        const data = userAddress.map((address) => ({
            ...address,
            isDefault: address.isDefault=="1" ? true : false
        }))
        const response = await successMessage({data: data});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getAddress",error);
        return next(error);
    }
};

export const addAddress = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const {name, address, zipCode, city, phoneNumber} = req.body;
        const addressCheck = await Address.findOne({ where: { userId: userId, deletedAt: null } });
        const isDefault = addressCheck ? "0" : "1";

        await Address.create({
            userId: userId,
            name: name,
            address: address,
            zip: zipCode,
            city: city,
            mobile: phoneNumber,
            isDefault: isDefault,
        });
        return res.status(200).json({status: true, data: "Address added successfully."});
    } catch (error) {
        logger.error("ERROR FROM addAddress",error);
        return next(error);
    }
};

export const changeDefaultAddress = async (req,res,next) => {
    try {
        const userId       = req.auth.user.id;
        const newDefaultId = req.query.newDefaultId;
        let transaction    = await sequelize.transaction();

        try {
            let currentDefaultAddress = await Address.findOne({ where: { isDefault: "1" } }, { transaction });
            const update = await currentDefaultAddress.update({ isDefault: "0" }, { transaction });
            let newDefaultAddress = await Address.findOne({ where: { id: newDefaultId } }, { transaction });
            if (newDefaultAddress) {
                const update = await newDefaultAddress.update({ isDefault: "1" }, { transaction });
            } else {
                let response = await errorMessage({ code: 1023 });
                return res.status(422).json(response);
            }
            
            await transaction.commit();
            return res.status(200).json({status: true, data: "default_address_changed"});
        } catch (error) {
            logger.warn("ERROR IN TRANSACTION");
            await transaction.rollback();
            throw error;        
        }

    } catch (error) {
        logger.error("ERROR FROM changeDefaultAddress",error);
        return next(error);
    }
};

export const deleteAddress = async(req,res,next) => {
    try {
        const userId        = req.auth.user.id;
        const currentDate   = new Date();
        const addressId     = req.body.addressId;
        let userAddress = await Address.findOne({
            where: { userId: userId, id: addressId },
        });
        if(!userAddress) {
            const response = await errorMessage({code: 1023, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        if(userAddress && parseInt(userAddress.isDefault)) {
            const response = await errorMessage({code: 1113, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        await Address.update({ deletedAt: currentDate }, { where: { id: addressId } });
        return res.status(200).json({status: true, data: "address_deleted"});
    } catch (error) {
        logger.error("ERROR FROM deleteAddress",error);
        return next(error);
    }
};

export const getPurchaseInvoice = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;

        // TODO id is encrypted? verify process.
        let orderId = req.query.orderId;

        const result = await Promise.all([
            Address.findOne({ where: { userId: userId, isDefault: "1" }, raw: true }),
            Order.findOne({
                where: { id: orderId, userId: userId },
                include: [
                    {
                        model: OrderDetail,
                        attributes: ["quantity", "amount"],
                        include: [{ model: Package, attributes: ["name", "price"] }]
                    }
                ],
                raw: true
            }),
            CompanyProfile.findOne({ raw: true })
        ]);
        const response = await successMessage({data: result});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getPurchaseInvoice",error);
        return next(error);
    }
};

export const repurchasePayment = async (req,res,next) => {
    try {
        const userId              = req.auth.user.id;
        const ip                  = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const paymentType         = req.body.paymentType;
        const repurchaseData      = req.body;
        const transactionPassword = req.body.transactionPassword || null;
        let totalpv = 0, totalAmount = 0;
        if (transactionPassword) {
            const checkPassword = await verifyTransactionPassword(req, res, next);
            if (!checkPassword) {
                const response = await errorMessage({ code: 1015, statusCode: 422 });
                return res.status(response.code).json(response.data);
            };
        };

        const cartData = await Cart.findAll({
            where: { userId: userId },
            include:[
                {model: Package,
                attributes: ["name", "price", "pairValue"],
                where:{ active: 1 }}
            ],
            raw: true
        });
        if (cartData.length===0) {
            let response = await errorMessage({ code: 1026 });
            return res.json(response);
        };
        for (const item of cartData) {
            const price    = Number(item["Package.price"]);
            const pv       = Number(item["Package.pairValue"]);
            const quantity = Number(item["quantity"]);

            totalpv += (pv * quantity);
            totalAmount += (price * quantity);
        };
        logger.info(`totalpv ${totalpv} totalAmount ${totalAmount}`);

        const checkPaymentMethod = await PaymentGatewayConfig.findOne({
            attributes: ["id", "name", "slug"],
            where: { id: paymentType, status: 1, repurchase: 1 },
            raw: true
        });

        if (!checkPaymentMethod) {
            const response = await errorMessage({ code: 1036, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        let orderId;
        if (totalAmount) {
            let transaction = await sequelize.transaction();
            try {
                orderId = await InternalCartService.repurchasePaymentService(req, res, next, transaction, cartData, checkPaymentMethod, repurchaseData, totalAmount, totalpv);
                logger.warn(orderId)
                await transaction.commit();
            } catch (error) {
                console.log(error)
                logger.warn("TRANSACTION ROLLBACK");
                await transaction.rollback();
                throw error;
            }
        }
        console.log("repurchasePaymentService orderId",orderId);

        // insert user activity
        const activityData = {
            user_id:userId,
            payment_method:paymentType,
            total_amount:totalAmount
        };
        await utilityService.insertUserActivity({
			userId: userId,
			userType: "user",
			data: JSON.stringify(activityData),
			description: "Product repurchased through internal cart.",
			ip: ip,
			activity: "Product repurchased",
			// transaction: None
		});

        if (orderId) { // should be null for bank transfer
            console.log(`-----COMMISSION CALL ORDERID: ${orderId}-----`);
            const commData = {
                userId: userId,
                // productId: null,
                productPv: totalpv,
                productAmount: totalAmount,
                orderId: orderId,
                // ocOrderId: null,
                sponsorId: null,
                uplineId: null,
                position: null,
            };
            const prefix = req.prefix;
            await CommissionService.commissionCall(prefix, userId, commData, "repurchase");
        };

        return res.status(200).json("repurchaseSuccessful.");
    } catch (error) {
        logger.error("ERROR FROM repurchasePayment",error);
        return next(error);
    }
};
