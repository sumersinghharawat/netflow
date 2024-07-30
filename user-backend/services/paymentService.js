import { Op } from "sequelize";
import { Stripe } from "stripe";
import axios from "axios";
import { consoleLog, errorMessage, logger } from "../helper/index.js";
import { generateTransactionId, getModuleStatus } from "../utils/index.js";
import EpinService from "./epinService.js";
import EwalletService from "./ewalletService.js";
import StripePaymentDetail from "../models/stripePaymentDetail.js";
import {
  CartPaymentReceipt,
  EwalletPaymentDetail,
  Package,
  PaymentGatewayConfig,
  PaymentGatewayDetail,
  PaymentReceipt,
  PaypalHistory,
  PaypalProduct,
  PinNumber,
  User,
} from "../models/association.js";
class PaymentService {
  async checkPaymentMethod(req, res, next, userDetails, paymentMethod) {
    switch (paymentMethod) {
      case "Bank Transfer":
        if (!(userDetails.ifsc || userDetails.accountNumber)) {
          return await errorMessage({ code: 1073, statusCode: 422 });
        }
        break;
      case "Stripe":
        if (!userDetails.stripe) {
          return await errorMessage({ code: 1074, statusCode: 422 });
        } else {
          let checkStripe = await this.verifyStripeAccount(userDetails.stripe);
          if (!checkStripe) {
            return await errorMessage({ code: 1077, statusCode: 422 });
          }
        }
        break;
      case "Paypal":
        if (!methodDetails.paypal) {
          return await errorMessage({ code: 1075, statusCode: 422 });
        }
        break;
      default:
        logger.warn(`INVALID PAYMENT METHOD ${paymentMethod}`);
        return await errorMessage({ code: 1036, statusCode: 422 });
        break;
    }
    return true;
  }

  async verifyStripeAccount(stripeId) {
    try {
      const authKeys = await this.getPaymentKeys("Stripe");
      const stripe = new Stripe(authKeys.secretKey);
      const stripeAccount = await stripe.accounts.retrieve(stripeId);
      if (stripeAccount.payouts_enabled == true) {
        if (
          stripeAccount.capabilities.card_payments == "active" &&
          stripeAccount.capabilities.transfers == "active"
        ) {
          return true;
        } else return false;
      } else return false;
    } catch (error) {
      logger.error("ERROR FROM verifyStripeAccount", error);
    }
  }

  async createStripeCharge(stripeToken, amount, description) {
    try {
      const currentDate = new Date();
      const authKeys = await this.getPaymentKeys("Stripe");
      const stripe = new Stripe(authKeys.secretKey);
      const result = await stripe.charges.create({
        source: stripeToken,
        amount: amount,
        currency: "usd",
        description: `${description} at ${currentDate}`,
      });
      return result;
    } catch (error) {
      logger.error("ERROR FROM createStripeCharge", error);
    }
  }

  async getPaymentKeys(name) {
    let authKeys = await PaymentGatewayConfig.findOne({
      attributes: ["id"],
      where: { name: name, status: 1 },
      include: [
        { model: PaymentGatewayDetail, attributes: ["publicKey", "secretKey"] },
      ],
      raw: true,
    });
    console.log("AUTHKEYS", authKeys);
    return {
      id: authKeys.id,
      publicKey: authKeys["PaymentGatewayDetail.publicKey"],
      secretKey: authKeys["PaymentGatewayDetail.secretKey"],
    };
  }

  async getPaymentMethods(action) {
    let whereCondition = { [action]: 1, status: 1 };

    const paymentMethods = await PaymentGatewayConfig.findAll({
      where: whereCondition,
      include: [
        {
          model: PaymentGatewayDetail,
          attributes: ["id"],
          required: true,
        },
      ],
    });

    return paymentMethods;
  }

  async insertIntoStripePaymentDetail(
    userId,
    chargeId,
    productId,
    orderId,
    totalAmount,
    type,
    paymentMethod,
    stripeResponse,
    transaction
  ) {
    const options = transaction ? { transaction } : {};
    const result = StripePaymentDetail.create(
      {
        userId: userId,
        chargeId: chargeId,
        productId: productId,
        orderId: orderId,
        totalAmount: totalAmount,
        type: type,
        paymentMethod: paymentMethod,
        stripeResponse: JSON.stringify(stripeResponse),
      },
      options
    );
  }

  async insertIntoPaymentReceipt({
    transaction,
    receipt,
    userId,
    type,
    pendingRegistrationId,
    username,
    orderId,
  }) {
    let options = transaction ? { transaction } : {};
    if (type == "renewal" || type == "upgrade") {
      const result = await PaymentReceipt.create(
        {
          pendingRegistrationId: null,
          receipt: process.env.IMAGE_URL + receipt,
          username: username,
          userId: userId,
          orderId: orderId ?? null,
          type: type,
        },
        options
      );
      return result;
    }

    const checkReceipt = await PaymentReceipt.findOne(
      {
        where: {
          username,
          type,
        },
      },
      options
    );
    // if(type === 'register') {

    if (checkReceipt && !checkReceipt.userId) {
      return checkReceipt.update(
        {
          receipt: process.env.IMAGE_URL + receipt,
        },
        options
      );
    } else {
      if (checkReceipt && checkReceipt.userId) {
        return false;
      }
      const result = await PaymentReceipt.create(
        {
          pendingRegistrationId: pendingRegistrationId ?? null,
          receipt: process.env.IMAGE_URL + receipt,
          username: username ?? null,
          userId: userId,
          orderId: orderId ?? null,
          type: type,
        },
        options
      );
      return result;
    }
    // } else {
    //     const result = await PaymentReceipt.create({
    //         pendingRegistrationId: pendingRegistrationId ?? null,
    //         receipt: process.env.IMAGE_URL+receipt,
    //         username: username ?? null,
    //         userId: userId,
    //         orderId: orderId ?? null,
    //         type: type
    //     }, options);
    //     return result;
    // }
  }

  async ewalletPayment({
    transaction,
    userId,
    userBalance,
    totalAmount,
    action,
  }) {
    // userBalance should be row object, not integer value
    const currentDate = new Date();
    const transactionId = await generateTransactionId();
    const newTransactionId = await EwalletService.addToTransaction({
      transactionId,
      transaction,
    });
    await EwalletPaymentDetail.create(
      {
        userId: userId,
        usedUser: userId,
        amount: totalAmount,
        usedFor: action,
        transactionId: newTransactionId.id,
      },
      { transaction }
    );

    await EwalletService.addToEwalletPurchaseHistory({
      userId: userId,
      referenceId: newTransactionId.id,
      ewalletType: "ewallet_payment",
      amount: totalAmount,
      balance: userBalance.balanceAmount - totalAmount,
      amountType: action,
      type: "debit",
      dateAdded: currentDate,
      transaction,
    });
    await EwalletService.reduceUserbalance({
      user: userBalance,
      deduction: totalAmount,
      type: "balanceAmount",
      transaction,
    });
    return true;
  }

  async epinPayment(res, transaction, userId, epins, totalAmount, action) {
    try {
      await EpinService.updateExpiredEpinStatus(userId);
      const epinDetails = await PinNumber.findAll(
        {
          where: {
            allocatedUser: userId,
            numbers: { [Op.in]: epins },
            status: "active",
          },
          raw: true,
        },
        { transaction }
      );
      logger.debug("epinDetails", epinDetails);
      // find epinbalance after use
      let remainingAmount = parseFloat(totalAmount);
      for (const epinNumber of epins) {
        const epin = epinDetails.find((epin) => epin.numbers == epinNumber);
        const epinId = epin.id;
        let epinAmount = parseFloat(epin.balanceAmount);

        if (remainingAmount >= epinAmount) {
          remainingAmount -= epinAmount;

          await PinNumber.update(
            { status: "used", balanceAmount: 0 },
            { where: { id: epinId }, transaction }
          );

          await EpinService.insertUsedPin(
            epinId,
            userId,
            epinAmount,
            action,
            transaction
          );
        } else {
          const epinBalance = epinAmount - remainingAmount;
          remainingAmount -= epinAmount;
          await PinNumber.update(
            { balanceAmount: epinBalance },
            { where: { id: epinId }, transaction }
          );

          await EpinService.insertUsedPin(
            epinId,
            userId,
            remainingAmount,
            action,
            transaction
          );
        }
      }

      if (parseFloat(remainingAmount) > 0) {
        await transaction.rollback();
        logger.warn("Remaining Amount:", remainingAmount);
        const response = await errorMessage({ code: 1016 });
        return res.status(422).json(response.data);
      }
      return true;
    } catch (error) {
      logger.error("ERROR FROM EPIN PAYMENT", error);
      throw error;
    }
  }
  async updatePaymentReceipt({ transaction, pendingUserId, type, username }) {
    let options = transaction ? { transaction } : {};
    const result = await PaymentReceipt.update(
      { pendingRegistrationsId: pendingUserId },
      { where: { username }, transaction }
    );
    return result.includes(1);
  }

  async insertIntoCartPaymentReceipt({ userId, receipt }) {
    const checkReceipt = await CartPaymentReceipt.findOne({
      where: { userId, orderId: null },
    });
    if (checkReceipt) {
      return await checkReceipt.update({
        image: process.env.IMAGE_URL + receipt,
      });
    } else {
      return await CartPaymentReceipt.create({
        image: process.env.IMAGE_URL + receipt,
        userId: userId,
      });
    }
  }

  async getPaypalPlanId(userId) {
    const paypalPlanDetails = await User.findOne({
      attributes: ["id", "productId"],
      include: [
        {
          model: Package,
          attributes: ["id"],
          include: [
            {
              model: PaypalProduct,
              attributes: ["id", "planId"],
            },
          ],
        },
      ],
      where: { id: userId },
    });
    return paypalPlanDetails.Package?.PaypalProduct?.planId ?? null;
  }

  async getPaypalAuthToken(url) {
    const paypalDetails = await PaymentGatewayDetail.findOne({
      attributes: ["publicKey", "secretKey"],
      include: [
        {
          model: PaymentGatewayConfig,
          attributes: ["id"],
          where: { slug: "paypal" },
        },
      ],
    });
    logger.debug("getPaypalAuthToken clientId, secretKey", paypalDetails);
    const clientID = paypalDetails.publicKey;
    const secretKey = paypalDetails.secretKey;
    // logger.debug("clientID",clientID,"\nsecretKey",secretKey)
    const response = await axios.post(
      url + "v1/oauth2/token",
      new URLSearchParams({
        grant_type: "client_credentials",
      }),
      {
        auth: {
          username: clientID,
          password: secretKey,
        },
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
      }
    );
    // console.log(response.data["access_token"])
    return response.data["access_token"];
  }

  async insertPaypalHistory({
    webhookEventId,
    data,
    eventType,
    subscriptionId,
  }) {
    return await PaypalHistory.create({
      webhookEventId: webhookEventId,
      data: data,
      eventType: eventType,
      subscriptionId: subscriptionId,
    });
  }

  async verifyWebhookEventCall(req, url, paypalAuthToken) {
    const authAlgo = req.headers["paypal-auth-algo"];
    const certUrl = req.headers["paypal-cert-url"];
    const transmissionId = req.headers["paypal-transmission-id"];
    const transmissionSig = req.headers["paypal-transmission-sig"];
    const transmissionTime = req.headers["paypal-transmission-time"];
    const webhookId = process.env.WEBHOOK_ID;

    let data = JSON.stringify({
      webhook_id: webhookId,
      transmission_id: transmissionId,
      transmission_time: transmissionTime,
      cert_url: certUrl,
      auth_algo: authAlgo,
      transmission_sig: transmissionSig,
      webhook_event: req.body,
    });
    let config = {
      method: "post",
      maxBodyLength: Infinity,
      url: url + "v1/notifications/verify-webhook-signature",
      headers: {
        "Content-Type": "application/json",
        "PayPal-Request-Id": "8246eb03-a19c-4046-8ac3-70992bddec4b",
        Authorization: "Bearer " + paypalAuthToken,
      },
      data: data,
    };

    const response = await axios.request(config);
    console.log("JSON.stringify(response.data)", JSON.stringify(response.data));

    return response.data;
  }
  async createStripePaymentIntent(amount, description, userEmail, type) {
    try {
      const authKeys = await this.getPaymentKeys("Stripe");
      const stripe = new Stripe(authKeys.secretKey);
      const paymentIntent = await stripe.paymentIntents.create({
        amount: parseInt(amount * 100),
        currency: "usd",
        payment_method_types: ["card"],
        description: description,
        metadata: {
          email: userEmail ?? "",
          type: type ?? "",
        },
      });
      return { paymentIntent, publicKey: authKeys.publicKey };
    } catch (error) {
      return error;
    }
  }
  async retrievePaymentIntent(id) {
    try {
      const authKeys = await this.getPaymentKeys("Stripe");
      const stripe = new Stripe(authKeys.secretKey);
      return await stripe.paymentIntents.retrieve(id);
    } catch (error) {
      console.log(error);
      return { status: false };
    }
  }
}
export default new PaymentService();
