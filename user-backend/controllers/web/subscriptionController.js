import { Op } from "sequelize";
import axios from "axios";
import _ from "lodash";
import { consoleLog, convertTolocal, errorMessage, logger, subscriptionWebHookEvents, successMessage } from "../../helper/index.js";
import { getModuleStatus, verifyTransactionPassword } from "../../utils/index.js";

import commissionService from "../../services/commissionService.js";
import HomeService from "../../services/homeService.js";
import SubscriptionService from "../../services/subscriptionService.js";
import utilityService from "../../services/utilityService.js";
import PaymentService from "../../services/paymentService.js";

import PaypalProduct from "../../models/paypalProduct.js";
import StripeProduct from "../../models/stripeProduct.js";
import { Package, PackageValidityExtendHistory, PaymentGatewayConfig, SubscriptionConfig, User, UserDetail } from "../../models/association.js";
import { sequelize } from "../../config/db.js";
import ProfileService from "../../services/profileService.js";
import PaypalSubscription from "../../models/paypalSubscription.js";
import paymentService from "../../services/paymentService.js";

export const getSubscriptionDetails = async (req, res, next) => {
    try {
        const userId             = req.auth.user.id;
        const moduleStatus       = await getModuleStatus({attributes:["subscriptionStatus","productStatus"]});
        let autoRenewalDetails   = null;
        const subscriptionConfig = await SubscriptionConfig.findOne({});

        if (!moduleStatus.subscriptionStatus || !moduleStatus.productStatus) {
            let response = await errorMessage({ code: 1057 });
            return res.status(422).json(response);
        }

        const user = await User.findOne({
            attributes: ["productValidity", "autoRenewalStatus"],
            where: { id: userId, productId: { [Op.ne]: " " } },
            include: [
                {
                    model: Package,
                    attributes: ["id", "name", "price", "validity", "pairValue"],
                    where: { active: 1 }
                },
                {
                    model: UserDetail,
                    attributes: ["id", "image"]
                },
                {
                    model: PaypalSubscription,
                    attributes: ["subscriptionId"],
                    where: { status: 1 },
                    required: false
                }
            ],
            // raw: true
        });

        const expiryDate = new Date(user.productValidity);
        const { purchaseDate, daysLeft } = await SubscriptionService.getProductExpiryDetails({ userId, expiryDate })
        logger.info("purchaseDate", purchaseDate, "expiryDate", expiryDate, "daysLeft", daysLeft)

        const renewalPrice = subscriptionConfig.basedOn === "amount_based"
            ? subscriptionConfig.fixedAmount
            : user.Package.price;

        // get stripe/paypal details
        if (user["autoRenewalStatus"]) {
            let paymentMethods = await PaymentGatewayConfig.findAll({
                attributes: ["name", "status"],
                where: { membershipRenewal: 1, status: 1, name: { [Op.in]: ["Paypal", "Stripe"] } },
                raw: true
            });
            if (paymentMethods.find((method) => method.name === "Paypal")) {
                autoRenewalDetails = await PaypalProduct.findOne({ where: { productId: user.Package.id } });
            } else if (paymentMethods.find((method) => method.name === "Stripe")) {
                autoRenewalDetails = await StripeProduct.findOne({});
            }
        };

        // find package validity percentage for icon border
        let packageValidity;
        const productValidityPeriod = user.Package.validity;
        if (moduleStatus.productStatus) {
            packageValidity = await HomeService.formatProductValidity(productValidityPeriod, expiryDate)
        } else {
            packageValidity = await HomeService.formatProductValidity(subscriptionConfig.subscriptionPeriod, expiryDate);
        }

        const data = {
            packageId          : user.Package.id,
            packageName        : user.Package.name,
            pairValue          : user.Package.pairValue,
            image              : user.UserDetail.image,
            daysLeft           : daysLeft,
            purchaseDate       : purchaseDate,
            renewalPrice       : renewalPrice,
            autoRenewalDetails : autoRenewalDetails,
            autoRenewalStatus  : user.autoRenewalStatus,
            productValidity    : packageValidity,
            subscriptionId     : user.PaypalSubscription?.subscriptionId ?? null
        };
        let response = await successMessage({ data: data });
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getSubscriptionDetails", error);
        return next(error);
    }
};

export const cancelAutosubscription = async (req, res, next) => {
    try {
        const userId = req.auth.user.id;
        const { mode: testStatus } = await PaymentGatewayConfig.findOne({ attributes: ["id", "mode"], where: { slug: "paypal" } });
        logger.info("test mode",testStatus)
        const url = (testStatus==="test") ? process.env.PAYPAL_TEST_URL : process.env.PAYPAL_LIVE_URL;
        const paypalAuthToken = await PaymentService.getPaypalAuthToken(url);

        const { subscriptionId: subscriptionId } = await PaypalSubscription.findOne({
            attributes: ["subscriptionId"],
            where: { userId: userId, status: 1 }
        });

        let data = JSON.stringify({
            "reason": "Item out of stock"
        });

        let config = {
            method: 'post',
            maxBodyLength: Infinity,
            url: url + '/v1/billing/subscriptions/' + subscriptionId + '/cancel',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + paypalAuthToken
            },
            data: data
        };

        const response = await axios.request(config)
        logger.info("response", response.data)
        // .then((response) => {
        //   console.log(JSON.stringify(response.data));
        // })
        // .catch((error) => {
        //   console.log(error);
        // });

        await SubscriptionService.updateAutoRenewalStatus({ userId, status: 0 });

        let reply = await successMessage({ data: true });
        return res.status(reply.code).json(reply.data);
    } catch (error) {
        logger.error("ERROR FROM changeAutoRenewalStatus", error);
        return next(error);
    }
};

export const renewSubscription = async (req, res, next) => {
    try {
        const userId              = req.auth.user.id;
        const productId           = req.body.packageId;
        const ip                  = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const subscriptionData    = req.body;
        const paymentMethod       = req.body.paymentMethod;
        const transactionPassword = req.body.transactionPassword;
        const subscriptionConfig  = await SubscriptionConfig.findOne({});
        const moduleStatus        = await getModuleStatus({attributes:["subscriptionStatus"]});
        const currentDate         = new Date();

        if (!moduleStatus.subscriptionStatus) {
            let response = await errorMessage({ code: 1057 });
            return res.status(422).json(response);
        };
        if (transactionPassword) {
            const checkPassword = await verifyTransactionPassword(req, res, next);
            if (!checkPassword) {
                const response = await errorMessage({ code: 1015, statusCode: 422 });
                return res.status(response.code).json(response.data);
            };
        };
        const checkPaymentMethod = await PaymentGatewayConfig.findOne({
            attributes: ["id", "name", "slug"],
            where: { id: paymentMethod, status: 1, membershipRenewal: 1 },
            raw: true
        });
        if (!checkPaymentMethod) {
            const response = await errorMessage({ code: 1036, statusCode: 422 });
            return res.status(response.code).json(response.data);
        };

        const checkPendingStatus = await PackageValidityExtendHistory.findOne({
            where: { userId: userId, renewalStatus: 0 }
        });
        if (checkPendingStatus) {
            const response = await errorMessage({ code: 1122, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        const user = await User.findOne({
            attributes: ["productValidity", "autoRenewalStatus"],
            where: { id: userId },
            include: [
                {
                    model: Package,
                    attributes: ["id", "price", "pairValue"]
                }
            ],
            raw: true
        });
        const productValidity = new Date(user["productValidity"]);
        // logger.info("productValidity",productValidity)
        // checks productValidity is a proper date object
        if (isNaN(productValidity.getTime())) {
            let response = await errorMessage({ code: 1013 });
            return res.status(422).json(response);
        };
        
        const totalAmount = subscriptionConfig.basedOn === "amount_based"
            ? subscriptionConfig.fixedAmount
            : user["Package.price"];

        let transaction = await sequelize.transaction();
        let status;
        try {
            status = await SubscriptionService.renewSubscription(res, next, transaction, userId, productId, checkPaymentMethod, totalAmount, subscriptionConfig, subscriptionData);
        } catch (error) {
            logger.error("ERROR FROM TRANSACTION");
            await transaction.rollback();
            throw error;
        }
        if (status == true) {
            await transaction.commit()
            const activityData = {
                user_id: userId,
                total_amount: totalAmount,
                by_using: checkPaymentMethod["slug"],
            };
            await utilityService.insertUserActivity({
                userId: userId,
                userType: "user",
                data: JSON.stringify(activityData),
                description: `Membership reactivation using ${checkPaymentMethod["slug"]}`,
                ip: ip,
                activity: "Membership reactivation",
                // transaction: None
            });
            const commData = {
                userId: userId,
                productId: productId,
                productPv: user["Package.pairValue"],
                productAmount: totalAmount,
                // orderId: orderId,
                // ocOrderId: null,
                sponsorId: null,
                uplineId: null,
                position: null,
            };
            const prefix = req.prefix;
            await commissionService.commissionCall(prefix, userId, commData, "membership_renewal");

        }
        let response = await successMessage({ data: "membership_renewed_successfully" });
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM renewSubscription", error);
        return next(error);
    }

};

export const paypalAutosubscription = async (req, res, next) => {
    try {
        // logger.debug("req.body",req.body)
        // req.body {
        //     "planId": "P-0C686669RW515132KMVBWDKQ",
        //     "data": {
        //       "orderID": "5KG37465D8302663E",
        //       "subscriptionID": "I-W59RR0145VJW",
        //       "facilitatorAccessToken": "A21AAKiCRRiqYg9j7rzEByK_ChloDzehgN3G8jgnbGUaBVSMuV4YLq_QR9CW3RdZ7uI5n2tTO5wOjCwq222f2rhmpPxLXwGdg",
        //       "paymentSource": "paypal"
        //     }
        //   }
        const userId             = req.auth.user.id;
        const subscriptionData   = req.body;
        const planId             = subscriptionData.planId;
        const subscriptionConfig = await SubscriptionConfig.findOne();
        const product            = await PaypalProduct.findOne({
            where: { planId: planId },
            include: [
                {
                    model: Package,
                    attributes: ["id", "pairValue", "validity"]
                }
            ]
        });
        // logger.debug("product",product)
        if (!product) {
            logger.error("INVALID PLAN ID", planId);
            let response = await errorMessage({ code: 1127 });
            return res.status(422).json(response);
        }

        let transaction = await sequelize.transaction();
        try {
            const user = await User.findOne({ where: { id: userId } }, { transaction });

            const paymentMethod = await PaymentGatewayConfig.findOne({ where: { slug: "paypal" } })

            let currentValidity = new Date(user["productValidity"]);
            await ProfileService.updatePackageValidity({ transaction, user, currentValidity, productData: product.Package, paymentAmount: product.amount, paymentMethod: paymentMethod.id, subscriptionConfig });
            await ProfileService.insertIntoPackageValidityExtendHistory({
                transaction, 
                user, 
                productData: product.Package, 
                paymentAmount: product.amount, 
                paymentMethod: paymentMethod.id, 
                renewalStatus: 1
            });
            await SubscriptionService.updateAutoRenewalStatus({ transaction, userId, status: 1 });
            const subscription = await SubscriptionService.insertIntoPaypalSubscription({ transaction, userId, planId, product, subscriptionData: subscriptionData });
            if (!subscription) {
                await transaction.rollback();
                // TODO send call to paypal to delete whatever happens in this case?
                let response = await errorMessage({ code: 1080 });
                return res.status(422).json(response);
            }
            await transaction.commit();
        } catch (error) {
            await transaction.rollback();
            logger.error("ERROR FROM TRANSACTION");
            throw error;
        }
        let response = await successMessage({ data: true });
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM paypalAutosubscription", error);
        return next(error);
    }
};

export const getSubscriptionReport = async (req, res, next) => {
    try {
        const userId = req.auth.user.id;
		const page       = parseInt(req.query.page) || 1;
        const pageSize   = parseInt(req.query.perPage) || 10; // length
		const offset     = (page - 1) * pageSize;
		const direction  = req.query.direction || "DESC";
        const moduleStatus = await getModuleStatus({attributes:["subscriptionStatus"]})

        if (!moduleStatus.subscriptionStatus) {
            let response = await errorMessage({ code: 1057 });
            return res.status(422).json(response);
        }

        const data = await SubscriptionService.getSubscriptionHistory({userId,pageSize,offset,direction})

        let response = await successMessage({ data: data });
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getSubscriptionReport",error);
        return next(error);
    }
};

export const paypalWebhookEvent = async (req, res, next) => {
    try {
        // logger.info("req.headers",req.headers)
        // logger.info("req.body",req.body)

        const webhookEventId = req.body.id;
        const eventType      = req.body.event_type;
        const subscriptionId = _.includes(["PAYMENT.SALE.COMPLETED", "PAYMENT.SALE.PENDING"], eventType) 
            ? req.body.resource.billing_agreement_id 
            : req.body.resource.id;
        let paypalHistory    = await paymentService.insertPaypalHistory({
            webhookEventId: webhookEventId,
            data: JSON.stringify(req.body),
            eventType: eventType,
            subscriptionId: subscriptionId
        });
        logger.info("eventType", eventType, "subscriptionId", subscriptionId)
        
        const { mode: testStatus } = await PaymentGatewayConfig.findOne({ attributes: ["id", "mode"], where: { slug: "paypal" } });
        const url = (testStatus === "test") 
            ? process.env.PAYPAL_TEST_URL 
            : process.env.PAYPAL_LIVE_URL;
        const paypalAuthToken      = await PaymentService.getPaypalAuthToken(url);
        const verificationResponse = await paymentService.verifyWebhookEventCall(req, url, paypalAuthToken);

        if (verificationResponse.verification_status === "SUCCESS") {
            await paypalHistory.update({ verificationStatus: 1 });
            if (subscriptionWebHookEvents.includes(eventType)) {
                await SubscriptionService.paypalWebhookEvent(eventType, subscriptionId, req.body.resource);
            } else if (true) {

            }
        } else {
            await paypalHistory.update({ verificationStatus: 0 })
        }

        return res.status(200).json({"msg":"done"});
    } catch (error) {
        console.log("ERROR FROM paypalWebhookEvent",error.data)
        logger.error("ERROR FROM paypalWebhookEvent",error);
        return res.status(401).json({"error":error.data})
    }
};









