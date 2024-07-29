import { sequelize } from "../config/db.js";
import { consoleLog, errorMessage, logger } from "../helper/index.js";
import { getModuleStatus } from "../utils/index.js";
import profileService from "./profileService.js";
import paymentService from "./paymentService.js";
import { PackageUpgradeHistory, PaymentReceipt, RoiOrder, SubscriptionConfig, UpgradeSalesOrder, User, UserBalanceAmount } from "../models/association.js";
import { Op } from "sequelize";


class UpgradeService {
    constructor() {
        this.moduleStatus     = null;
        this.paymentAmount    = null;
        this.pvDifference     = null;
        // this.transaction      = null;
        this.paymentMethod    = null;
        this.userId           = null;
        this.oldProductId     = null;
        this.upgradeProductId = null;
        this.paymentReceipt   = null;
        this.pendingStatus    = null;
    }

    async upgradePayment(req,res,next,transaction,productData,checkPaymentMethod,upgradeData,paymentAmount,pvAmount) {
        try {
            this.userId            = req.auth.user.id;
            this.oldProductId      = upgradeData["oldProductId"];
            this.upgradeProductId  = upgradeData["upgradeProductId"];
            this.paymentMethod     = checkPaymentMethod["id"];
            this.paymentAmount     = paymentAmount;
            this.pvDifference      = pvAmount;
            this.moduleStatus      = await getModuleStatus({attributes:["roiStatus","subscriptionStatus"]});
            // let transaction        = await sequelize.transaction();
            switch (checkPaymentMethod["slug"]) {
                case "e-pin":
                    const epins = upgradeData["epins"];
                    await paymentService.epinPayment(res,transaction,this.userId,epins,this.paymentAmount,"upgrade");
                    break;
                case "e-wallet":
                    const userBalance = await UserBalanceAmount.findOne({ where: { userId: this.userId } },{ transaction });
                    
                    if (userBalance.balanceAmount < this.paymentAmount) {
                        await transaction.rollback();
                        let response = await errorMessage({ code: 1054 });
                        return res.status(422).json(response);
                    };
                    await paymentService.ewalletPayment({transaction, userId: this.userId, userBalance, totalAmount: this.paymentAmount, action: "upgrade"});
                    break;
                case "free-joining":
                    // null
                    break;
                case "bank-transfer":
                    this.paymentReceipt = process.env.IMAGE_URL + 'uploads/upgrade/' + upgradeData["bankReceipt"];
                    this.paymentReceiptId = await PaymentReceipt.findOne({
                        attributes: ["id"],
                        where: {
                            receipt: {[Op.like]: this.paymentReceipt}
                        }
                    });
                    // this.paymentReceipt = await paymentService.insertIntoPaymentReceipt(transaction, bankReceipt, this.userId, "upgrade");
                    break;
                // case "stripe":
                    
                //     break;
                case "paypal":
                    
                    break;
                default:
                    break;
            }

            // kept static since there is no relevant field in any table
            const pendingStatusLookup = {
                "e-pin"         : false,
                "e-wallet"      : false,
                "free-joining"  : true,
                "bank-transfer" : true,
                "stripe"        : false,
                "paypal"        : false
            };
            this.pendingStatus = pendingStatusLookup[checkPaymentMethod["slug"]] || false;
            const user = await User.findOne({ where: { id: this.userId } });
            const upgradeProductData = productData.find((product) => product.id===this.upgradeProductId);
            if (!this.pendingStatus) {
                
                await user.update({ 
                    productId: this.upgradeProductId, 
                    personalPv: user.personalPv + this.pvDifference 
                }, {transaction });
                
                await this.insertIntoUpgradeSalesOrder(transaction);

                if (this.moduleStatus.roiStatus) {
                    const roi   = upgradeProductData.roi;
                    const price = upgradeProductData.price;
                    const days  = upgradeProductData.days;
                    await this.insertIntoRoiOrder(transaction, req.prefix, roi, days, price);
                };

                if (this.moduleStatus.subscriptionStatus) {
                    const subscriptionConfig = await SubscriptionConfig.findOne({});
                    // check currentValidity is a valid date
                    let currentValidity  = new Date(user["productValidity"]);
                    if (isNaN(currentValidity.getTime())) {
                        logger.info("INVALID PRODUCT VALIDITY",currentValidity);
                        await transaction.rollback();
                        let response = await errorMessage({ code: 1013 });
                        return res.status(422).json(response);
                    };
                    await profileService.updatePackageValidity({ transaction, user, currentValidity, productData: upgradeProductData, paymentAmount: this.paymentAmount, paymentMethod: this.paymentMethod, renewalStatus: 1, subscriptionConfig });
                };
            };
            
            // await profileService.insertIntoPackageValidityExtendHistory({
            //     transaction, 
            //     user, 
            //     productData:upgradeProductData, 
            //     paymentAmount:this.paymentAmount, 
            //     paymentMethod:this.paymentMethod, 
            //     renewalStatus: this.pendingStatus ? 0 : 1,
            //     receipt: this.paymentReceiptId?.id ?? null
            // });
            await this.insertIntoPackageUpgradeHistory(transaction);
            
            // await transaction.commit();
            return !this.pendingStatus;
        } catch (error) {
            logger.error("ERROR FROM upgradePayment",error);
            throw(error);
        }
    }

    

    async insertIntoRoiOrder(transaction, prefix, roi, days, price) {
        const dateSubmission = new Date();
        await RoiOrder.create({
            packageId: this.upgradeProductId,
            userId: this.userId,
            amount: price,
            dateSubmission: dateSubmission,
            paymentMethod: this.paymentMethod,
            pendingStatus: 0,
            roi: roi,
            days: days,
        }, {transaction});
    }

    async insertIntoUpgradeSalesOrder(transaction) {
        await UpgradeSalesOrder.create({
            userId: this.userId,
            packageId: this.upgradeProductId,
            amount: this.paymentAmount,
            totalPv: this.pvDifference,
            paymentMethod: this.paymentMethod
        }, { transaction });

        return true;
    }

    async insertIntoPackageUpgradeHistory(transaction) {
        await PackageUpgradeHistory.create({
            userId: this.userId,
            currentPackageId: this.oldProductId,
            newPackageId: this.upgradeProductId,
            pvDifference: this.pvDifference,
            paymentAmount: this.paymentAmount,
            paymentType: this.paymentMethod,
            doneBy: this.userId,
            status: this.pendingStatus ? 0 : 1,
            paymentReceipt: this.paymentReceiptId?.id ?? null,
            description: null,
        }, { transaction });
        return true;
    }


}

export default new UpgradeService;