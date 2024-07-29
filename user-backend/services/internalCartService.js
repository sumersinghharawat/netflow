import { Op } from "sequelize";
import randomatic from "randomatic";
import { sequelize } from "../config/db.js";
import { consoleLog, convertTolocal, errorMessage, logger } from "../helper/index.js";
import PaymentService from "./paymentService.js";
import  { Address, Cart, CartPaymentReceipt, Order, OrderDetail, Package, PaymentGatewayConfig, UserBalanceAmount } from "../models/association.js";

class InternalCartService {
    constructor() {
        this.moduleStatus = null;
        this.totalAmount = null;
        this.totalpv = null;
        // this.transaction = null;
        this.paymentMethod = null;
        this.userId = null;
    }

    async getTotalCart(userId) {
        let totalCart   = 0;
        const cartItems = await Cart.findAll({
            where:{userId:userId},
            include:[{
                model:Package,
                attributes:["price"]
            }],
            raw:true
        });

        Object.entries(cartItems).map(([key,item]) => {
            totalCart += item["quantity"] * item["Package.price"];
        });
        return totalCart;
    }

    async getReportData(userId,pageSize,offset,direction) {
        const {count, rows} = await Order.findAndCountAll({
            attributes:["id","invoiceNo","totalAmount","orderDate", "orderStatus"],
            include:[
                {
                    model: PaymentGatewayConfig,
                    attributes:["id","name"]
                }
            ],
            where:{ userId: userId },
            order:[["orderDate",direction]],
            offset: offset,
            limit: pageSize
        });
        const data = rows.map((row) => ({
            id: row.id,
            invoiceNo: row.invoiceNo,
            totalAmount: row.totalAmount,
            paymentMethod: row.PaymentGatewayConfig.name,
            orderDate: row.orderDate,
            status : row.orderStatus
        }));
        const totalCount  = count;
        const totalPages  = Math.ceil(totalCount / pageSize);
        const currentPage = Math.floor(offset / pageSize) + 1;
        return {
            totalCount,
            totalPages,
            currentPage,
            data,
    };
    }

    async getReportInvoice(orderId) {
        const orderDetailData = await OrderDetail.findAll({
            attributes: ["id","quantity","amount"],
            include: [
                {
                    model: Package,
                    attributes: ["id","name"]
                }
            ],
            where: { orderId: orderId }
        })
        // logger.debug("orderDetailData",orderDetailData);
        const orderDetails = orderDetailData.map(row => ({
            package: row.Package.name,
            quantity: row.quantity,
            amount: row.amount
        }))
        const orderData = await Order.findOne({
            attributes:["id","invoiceNo","totalAmount","orderDate"],
            include: [
                {
                    model: Address,
                    attributes:["id","name","address","zip","city","mobile"]
                },
                {
                    model: PaymentGatewayConfig,
                    attributes:["id","name"]
                }
            ],
            where: { id: orderId }
        })
        // logger.debug("orderData",orderData);

        return {
            invoiceNo: orderData.invoiceNo,
            date: convertTolocal(orderData.orderDate),
            clientInfo: {
                name: orderData.Address.name,
                address: orderData.Address.address,
                city: orderData.Address.city,
                mobile: orderData.Address.mobile,
                zip: orderData.Address.zip
            },
            paymentDetails: {
                paymentMethod: orderData.PaymentGatewayConfig.name
            },
            items: orderDetails,
            grandTotal: orderData.totalAmount
        };
    }

    async repurchasePaymentService (req,res,next,transaction,cartData,checkPaymentMethod,repurchaseData,totalAmount,totalpv) {
        this.userId        = req.auth.user.id;
        this.totalAmount   = totalAmount;
        this.totalpv       = totalpv;
        this.paymentMethod = checkPaymentMethod["id"];
        let orderId;
        try {
        
            switch (checkPaymentMethod["slug"]) {
                case "bank-transfer":
                    orderId = await this.bankTransferPayment(req, res, next, transaction, cartData, repurchaseData);
                    break;
                case "purchase-wallet":
                    // orderId = await this.purchaseWalletPayment();
                    break;
                case "e-wallet":
                    orderId = await this.ewalletPayment(req, res, next, transaction, cartData, repurchaseData);
                    break;
                case "free-joining":
                    orderId = await this.freeJoin(req, res, next, transaction, cartData, repurchaseData);
                    break;
                case "e-pin":
                    orderId = await this.epinPayment(req, res, next, transaction, cartData, repurchaseData);
                    break;
                case "stripe":
                    await this.stripePayment(req, res, next, transaction, cartData, repurchaseData);
                    break;
                case "paypal":
                    break;
                default:
                    const response = await errorMessage({ code: 1036, statusCode: 422 });
                    return res.status(response.code).json(response.data);
            };

            // if purchase wallet
            // if userpurchasewallet<totalAmount return error else set ispurchasewallet=true
            // if ispurchasewallet
            // create transaction id, purchasewallethistory, deduct from purchase wallet

            if (orderId) {
                return orderId;
            }
            return false;
        } catch (error) {
            logger.warn("ERROR FROM repurchasePaymentService");
            throw error;
        }
    }

    async freeJoin(req, res, next, transaction, cartData, repurchaseData) {
        let addressId = repurchaseData.addressId;
        const orderId = await this.insertOrderAndOrderDetail(transaction, cartData, addressId, "0");

        const removeFromCart = await this.removeFromCart(req, res, next, transaction);

        // const result = await CommissionService.callCommission()
        return orderId;
    }

    async epinPayment(req, res, next, transaction, cartData, repurchaseData) {
        let epins = repurchaseData.epins;
        let addressId = repurchaseData.addressId;

        // remove duplicate epins
        epins = epins.filter((value, index, self) => self.indexOf(value) === index);
        logger.info("FILTERED EPINS: ", epins);

        const result = await PaymentService.epinPayment(res, transaction, this.userId, epins, this.totalAmount, "repurchase");

        const orderId = await this.insertOrderAndOrderDetail(transaction, cartData, addressId, "1");
        const removeFromCart = await this.removeFromCart(req, res, next, transaction);

        return orderId;
    }

    async ewalletPayment(req, res, next, transaction, cartData, repurchaseData) {
        let addressId = repurchaseData.addressId;
        const userBalance = await UserBalanceAmount.findOne({ where: { userId: this.userId } });
        if (userBalance.balanceAmount < this.totalAmount) {
            let response = await errorMessage({ code: 1054 });
            return res.status(422).json(response);
        };

        await PaymentService.ewalletPayment({ transaction, userId: this.userId, userBalance, totalAmount: this.totalAmount, action: "repurchase" });

        const orderId = await this.insertOrderAndOrderDetail(transaction, cartData, addressId, "1");

        const removeFromCart = await this.removeFromCart(req, res, next, transaction);

        return orderId;
    }

    async bankTransferPayment(req, res, next, transaction, cartData, repurchaseData) {
        let bankReceipt = repurchaseData.bankReceipt;
        let addressId = repurchaseData.addressId;

        const orderId = await this.insertOrderAndOrderDetail(transaction, cartData, addressId, "0");
        const removeFromCart = await this.removeFromCart(req, res, next, transaction);


        // TODO - update only. create entry with image upload
        const receipt = await CartPaymentReceipt.findOne({ where: { userId: this.userId, orderId: null } });
            if (receipt) {
            await receipt.update({ orderId: orderId }, { transaction })
        } else {
            await transaction.rollback();
            const response = await errorMessage({ code: 1082, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        return null;
    }

    async stripePayment(req, res, next, transaction, cartData, repurchaseData) {
        const stripeToken = repurchaseData["stripeToken"]["id"];
        let addressId = repurchaseData.addressId;
        
        const stripeResponse = await PaymentService.createStripeCharge(stripeToken, this.totalAmount, "Repurchase");
        if (stripeResponse == false) {
            let response = await errorMessage({ code: 429 });
            return res.status(500).json(response);
        }

        const orderId = await this.insertOrderAndOrderDetail(transaction, cartData, addressId, "1");

        const removeFromCart = await this.removeFromCart(req, res, next, transaction);
        const chargeId = stripeResponse["charge_id"];
        const paymentMethod = stripeResponse["payment_method"];

        await PaymentService.insertIntoStripePaymentDetail(this.userId, chargeId, null, orderId, this.totalAmount, "repurchase", paymentMethod, stripeResponse, transaction);

        return orderId;
    }

    async insertOrderAndOrderDetail(transaction, cartData, addressId, orderStatus) {
        try {

            const invoiceNo       = "RPCHSE" + randomatic("Aa0", 10);
            const currentDate     = new Date();
            const userId          = this.userId;
            const orderDetailData = [];

            // const { id: orderAddressId } = await Address.findOne({
            //     attributes: ["id"],
            //     where: {
            //         userId: userId,
            //         isDefault: "1",
            //         deletedAt: null
            //     },
            //     raw: true,
            //     transaction
            // });

            // if (!orderAddressId) {
            //     throw new Error("No Address.");
            // }

            const order = await Order.create({
                invoiceNo: invoiceNo,
                userId: userId,
                orderAddressId: addressId,
                orderDate: currentDate,
                totalAmount: this.totalAmount,
                totalPv: this.totalpv,
                orderStatus: orderStatus,
                paymentMethod: this.paymentMethod,
            }, { transaction });
            const orderId = order["id"];

            for (const item of cartData) {
                orderDetailData.push({
                    orderId: orderId,
                    packageId: item["packageId"],
                    quantity: item["quantity"],
                    amount: item["Package.price"],
                    productPv: item["Package.pairValue"]
                });
            };

            await OrderDetail.bulkCreate(orderDetailData, { transaction });

            return orderId;
        } catch (error) {
            logger.error("ERROR IN insertOrderAndOrderDetail");
            throw error;
        }
    }

    async removeFromCart(req, res, next, transaction) {

        const result = await Cart.destroy({ where: { userId: this.userId }, transaction });
        logger.debug("removeFromCart",result)
        // to prevent double click 
        if (result == 0) {
            logger.info("CART IS EMPTY");
            await transaction.rollback();
            let response = await errorMessage({ code: 1054 });
            return res.status(422).json(response);
        }

    }

}
export default new InternalCartService;