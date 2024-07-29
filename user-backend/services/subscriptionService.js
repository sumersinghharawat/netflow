import axios from "axios";
import { sequelize } from "../config/db.js";
import { errorMessage, logger } from "../helper/index.js";
import paymentService from "./paymentService.js";
import profileService from "./profileService.js";
import { Package, PackageValidityExtendHistory, PaymentGatewayConfig, PaymentReceipt, SubscriptionConfig, User, UserBalanceAmount, UserDetail } from "../models/association.js";
import PaypalSubscription from "../models/paypalSubscription.js";
import PaypalProduct from "../models/paypalProduct.js";
import PaypalHistory from "../models/paypalHistories.js";
import utilityService from "./utilityService.js";
import commissionService from "./commissionService.js";
import { Op } from "sequelize";

class SubscriptionService {

    async getProductExpiryDetails({userId, expiryDate}) {
        const currentDate = new Date();

        // find last purchase date, or date of joining
        let purchaseDate;
        let purchaseData = await PackageValidityExtendHistory.findOne({
            attributes: ["createdAt"],
            where: { userId: userId },
            order: [["created_at", "DESC"]]
        })
        if (!purchaseData) {
            purchaseData = await User.findOne({
                attributes: ["dateOfJoining"],
                where: { id: userId }
            })
            purchaseDate = purchaseData.dateOfJoining;
        } else {
            purchaseDate = purchaseData.createdAt;
        }
        let daysLeft;
        if (expiryDate < currentDate) {
            daysLeft = 0;
        } else {
            const timeDifference = expiryDate.getTime() - currentDate.getTime();
            daysLeft = Math.ceil(timeDifference / (1000 * 3600 * 24));
        }

        return {purchaseDate, daysLeft};
    }

    async renewSubscription(res,next, transaction, userId, productId, checkPaymentMethod, totalAmount, subscriptionConfig, subscriptionData) {
        try {

            const paymentMethod    = checkPaymentMethod["id"]
            let receiptId;

            switch (checkPaymentMethod["slug"]) {
                case "e-pin":
                    const epins = subscriptionData.epins;
                    await paymentService.epinPayment(res,transaction,userId,epins,totalAmount,"package_validity")
                    break;
                case "e-wallet":
                    const userBalance = await UserBalanceAmount.findOne({ where: { userId: userId } })

                    if (userBalance.balanceAmount < totalAmount) {
                        await transaction.rollback();
                        let response = await errorMessage({ code: 1014 });
                        return res.status(422).json(response);
                    };
                    await paymentService.ewalletPayment({transaction, userId, userBalance, totalAmount, action:"package_validity"})
                    break;
                case "free-joining":
                    // null
                    break;
                case "bank-transfer":
                    const bankReceipt = process.env.IMAGE_URL + 'uploads/renewal/' + subscriptionData["bankReceipt"];
                    receiptId = await PaymentReceipt.findOne({
                        attributes: ["id"],
                        where: {
                            receipt: {[Op.like]: bankReceipt}
                        }
                    });
                    break;
                // case "stripe":

                //     break;
                case "paypal":

                    break;
                default:
                    break;
            }
            const pendingStatusLookup = {
                "e-pin"         : false,
                "e-wallet"      : false,
                "free-joining"  : true,
                "bank-transfer" : true,
                "stripe"        : false,
                "paypal"        : false
            };
            this.pendingStatus = pendingStatusLookup[checkPaymentMethod["slug"]] || false;
            const user = await User.findOne({ where: { id: userId } }, { transaction })
            const productData = await Package.findOne({ where: { id: productId } }, { transaction });

            if (!this.pendingStatus) {
                const currentValidity = user["productValidity"];
                await profileService.updatePackageValidity({ transaction, user, currentValidity, productData, paymentAmount: totalAmount, paymentMethod, subscriptionConfig })    
            }
            
            await profileService.insertIntoPackageValidityExtendHistory({
                transaction, 
                user, 
                productData: productData, 
                paymentAmount: totalAmount, 
                paymentMethod: paymentMethod, 
                renewalStatus: this.pendingStatus ? 0 : 1,
                receipt: receiptId?.id ?? null
            });
        
        
            return true;
        } catch (error) {
            logger.error("ERROR FROM renewSubscription",error)
            throw error;
        }
    }

    async insertIntoPaypalSubscription({ transaction, userId, planId, product, subscriptionData, status }) {
        const options = transaction ? { transaction } : {};
        const check = await PaypalSubscription.findOne({
            where: {
                userId: userId,
                planId: planId,
                subscriptionId: subscriptionData.data.subscriptionID,
                status: 1
            }
        });
        if (check) return false;
        
        await PaypalSubscription.create({
            userId: userId,
            productId: product.productId,
            planId: planId,
            subscriptionId: subscriptionData.data.subscriptionID,
            subscriptionData: JSON.stringify(subscriptionData),
            status: 1,
            amount: product.amount
        }, options)
        return true;
    }

    async updateAutoRenewalStatus({transaction, userId, status}) {
        const options = transaction ? { where: { id: userId }, transaction } : { where: { id: userId } }
        return await User.update({ autoRenewalStatus: status }, options);
    }

    async getSubscriptionHistory({userId,pageSize,offset,direction}) {
        const subscriptionData = await PackageValidityExtendHistory.findAll({
            where: {userId: userId},
            offset: offset,
            limit: pageSize,
            direction: direction
        })
        logger.debug("subscriptionData",subscriptionData)
        return subscriptionData;
    }

    async updatePaypalSubscription({ transaction, subscriptionId, status }) {
        const options = transaction ? { where: { subscriptionId: subscriptionId }, transaction } : { where: { subscriptionId: subscriptionId } };
        await PaypalSubscription.update({ status: status }, options);
        return true;
    }

    async checkActiveSubscription({subscriptionId}) {
        const check = await PaypalSubscription.findOne({
            where: { subscriptionId: subscriptionId, status: 1 }
        })
        // returns true/false
        return Boolean(check);
    }

    async findUser(subscriptionId) {
        const user = await User.findOne({
            include: [{
                model: PaypalSubscription,
                attributes: [ "id","userId" ],
                where: { subscriptionId: subscriptionId, status: 1 }
            }]
        })
        logger.debug("user",user)
        return user;
    }

    async paypalWebhookEvent(eventType, subscriptionId, resource) {
        // const events = [
            //     "BILLING.SUBSCRIPTION.ACTIVATED",
            //     "BILLING.SUBSCRIPTION.CANCELLED",
            //     "BILLING.SUBSCRIPTION.CREATED", // 	A subscription is created.
            //     "BILLING.SUBSCRIPTION.EXPIRED",
            //     "BILLING.SUBSCRIPTION.PAYMENT.FAILED",
            //     "BILLING.SUBSCRIPTION.RE-ACTIVATED",
            //     "BILLING.SUBSCRIPTION.SUSPENDED",
            //     "BILLING.SUBSCRIPTION.UPDATED",

            //     "PAYMENT.SALE.COMPLETED", // A payment is made on a subscription.
            //     "PAYMENT.SALE.REFUNDED", //	A merchant refunds a sale.
            //     "PAYMENT.SALE.REVERSED", // A payment is reversed on a subscription.
            //     "BILLING.PLAN.CREATED", // 	A billing plan is created.
            //     "BILLING.PLAN.UPDATED",
            //     "BILLING.PLAN.ACTIVATED",
            //     "BILLING.PLAN.PRICING-CHANGE.ACTIVATED",
            //     "BILLING.PLAN.DEACTIVATED",
            //     "CATALOG.PRODUCT.CREATED", // A product is created.
            //     "CATALOG.PRODUCT.UPDATED",
            // ]
        switch (eventType) {
            case "BILLING.SUBSCRIPTION.CANCELLED":
                await this.updatePaypalSubscription({ subscriptionId, status: 4 })
                break;

            case "BILLING.SUBSCRIPTION.EXPIRED":
                await this.updatePaypalSubscription({ subscriptionId, status: 3 })
                break;

            case "BILLING.SUBSCRIPTION.PAYMENT.FAILED":
                // TODO 
                break;

            case "BILLING.SUBSCRIPTION.RE-ACTIVATED":
                await this.updatePaypalSubscription({ subscriptionId, status: 1 })
                break;

            case "BILLING.SUBSCRIPTION.SUSPENDED":
                await this.updatePaypalSubscription({ subscriptionId, status: 2 })
                break;
            case "PAYMENT.SALE.COMPLETED":
                const user = await this.findUser(subscriptionId)
                let currentValidity = new Date(user["productValidity"])

                const paymentAmount = (subscriptionConfig.basedOn === "amount_based")
                    ? subscriptionConfig.fixedAmount
                    : productData.price;

                logger.debug("user_id", user.id)
                const [subscriptionConfig, productData, paymentMethod] = await Promise.all([
                    SubscriptionConfig.findOne({}),
                    Package.findOne({
                        attributes: ["id", "pairValue", "validity", "price"],
                        include: [{
                            model: PaypalProduct,
                            attributes: ["id"],
                            include: [{
                                model: PaypalSubscription,
                                attributes: ["id"],
                                where: { subscriptionId: subscriptionId }
                            }]
                        }]
                    }),
                    PaymentGatewayConfig.findOne({ attributes: ["id", "slug"], where: { slug: "paypal" } })
                ])
                //INF74912079
                // const paymentAmount = req.body.resource.amount.total

                await profileService.updatePackageValidity({ transaction, user, currentValidity, productData, paymentAmount, paymentMethod: paymentMethod.id, renewalStatus: 1, subscriptionConfig });
                await profileService.insertIntoPackageValidityExtendHistory({
                    transaction, 
                    user, 
                    productData: productData, 
                    paymentAmount: paymentAmount, 
                    paymentMethod: paymentMethod.id, 
                    renewalStatus: 1
                });
                const ip = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
                const activityData = {
                    user_id: user.id,
                    total_amount: paymentAmount,
                    by_using: paymentMethod.slug,
                };
                await utilityService.insertUserActivity({
                    userId: user.id,
                    userType: "user",
                    data: JSON.stringify(activityData),
                    description: `Membership reactivation using ${paymentMethod.slug}`,
                    ip: ip,
                    activity: "Membership reactivation",
                    // transaction: None
                });
                if (process.env.DEMO_STATUS==="no") {
                    const commData = {
                        userId: user.id,
                        productId: productData.id,
                        productPv: productData.pairValue,
                        productAmount: paymentAmount,
                        // orderId: orderId,
                        // ocOrderId: null,
                        sponsorId: null,
                        uplineId: null,
                        position: null,
                    };
                    // const prefix = req.prefix;
                    const prefix = process.env.PREFIX;
                    await commissionService.commissionCall(prefix,user.id, commData, "membership_renewal");
    
                }
                
                break;
            case "BILLING.PLAN.DEACTIVATED":
                await PaypalProduct.update({ status: 0 }, { where: { planId: subscriptionId } });
                break;
            case "BILLING.PLAN.PRICING-CHANGE.ACTIVATED":
                const planData = resource.billing_cycles.find(data => data.tenure_type == "REGULAR")
                await PaypalProduct.update({ amount: parseFloat(planData.fixed_price.value) }, { where: { planId: subscriptionId } })
                break;
            default:
                break;
        }
        return true;
    }

}
export default new SubscriptionService;