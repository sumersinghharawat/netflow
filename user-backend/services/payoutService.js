
import { Op } from "sequelize";
import {randomUUID} from "crypto";
import { consoleLog, errorMessage, logger } from "../helper/index.js";
import ewalletService from "./ewalletService.js";
import { EwalletPurchaseHistory, Notification, PaymentGatewayConfig, PaymentGatewayDetail, PayoutReleaseRequest, User, UserBalanceAmount } from "../models/association.js";

class PayoutService{
    constructor() {
        this.configurations = null;
        this.paymentMethod = null;
    }
    // async initialize() {
    //     this.configurations = await Configuration.findOne({})
    // }

    async cancelPayoutRequest(req, res, next, transaction, payoutIdArr, payoutRequests) {
        const userId            = req.auth.user.id;
        let purchaseHistoryData = [];
        const userBalance       = await UserBalanceAmount.findOne({ where: { userId: userId } });
        let totalReturnAmount   = 0;

        for (const payout of payoutRequests) {
            const returnAmount = parseFloat(payout.amount) + parseFloat(payout.payoutFee);
            totalReturnAmount += returnAmount;

            purchaseHistoryData.push({
                userId: userId,
                referenceId: payout.id,
                ewalletType: "payout",
                amount: returnAmount,
                balanceAmount: userBalance.balanceAmount + totalReturnAmount,
                amountType: "payout_delete",
                type: "credit",
                transactionFee: 0,
            });
        }

        await Promise.all([
            PayoutReleaseRequest.update(
                { status: 2 },
                { where: { id: { [Op.in]: payoutIdArr } }, transaction }),
                ewalletService.addUserBalance({ user: userBalance, addition: totalReturnAmount, type:"balanceAmount", transaction }),
            EwalletPurchaseHistory.bulkCreate(purchaseHistoryData, { transaction })
        ]);
        return true;
    }

    async calculateAvailablePayoutAmount(payoutConfiguration,payoutUserDetails){
        const userBalance = Number(payoutUserDetails.UserBalanceAmount.balanceAmount);
        const feeAmount   = Number(payoutConfiguration.feeAmount);
        const feeMode     = payoutConfiguration.feeMode;
        
        let availablePayoutAmount;

        if (feeMode=="percentage") {
            availablePayoutAmount = userBalance - (userBalance * feeAmount)/100;
        } else {
            availablePayoutAmount = userBalance - feeAmount;
        }
        // logger.info(`available withdrawal amount: ${availablePayoutAmount} user balance: ${userBalance} fee mode: ${feeMode}`);
        return availablePayoutAmount;
    }

    async payoutRequestTransaction(user, transaction, paymentId, payoutAmount, feeAmount, totalAmount, remainingBalance, payoutConfiguration) {
        // payoutAmount            = Number(payoutAmount);
        const userId            = user.id;
        const username          = user.username;
        const currentDate       = new Date();
        const userBalance       = await UserBalanceAmount.findOne({ where: { userId: userId } });
        const { id : adminId }  = await User.findOne({where:{userType:"admin"},raw:true},{transaction});
        
        let notificationData = { 
            "type": "payout_request", 
            "title": "payout_request_title", 
            "request_id": userId, 
            "amount": payoutAmount, 
            "count": 0, 
            "user_id": userId, 
            "username": username, 
            "icon": '<i class="bx bx-money"></i>'
          };
        
        const result = await Promise.all([
            PayoutReleaseRequest.create({
                userId: userId,
                amount: payoutAmount,
                balanceAmount: payoutAmount,
                status: 0,
                readStatus: 0,
                payoutFee: feeAmount,
                paymentMethod: paymentId,
            }, { transaction }),
            ewalletService.reduceUserbalance({ user: userBalance, deduction: totalAmount, type:"balanceAmount", transaction }),
            Notification.create({
                id: randomUUID(),
                type: "PayoutRequestNotification",
                notifiableType: "App\\Models\\User",
                notifiableId: adminId,
                data: JSON.stringify(notificationData)
            }, { transaction })
        ]);
        const payoutRequestId = result[0]["dataValues"]["id"];

        await EwalletPurchaseHistory.create({
            userId: userId,
            referenceId: payoutRequestId,
            ewalletType: "payout",
            amount: payoutAmount,
            balance: remainingBalance,
            amountType: "payout_request",
            type: "debit",
            // transactionFee: feeAmount,
            dateAdded: currentDate
        }, { transaction });

        if (feeAmount > 0) {
            await EwalletPurchaseHistory.create({
                userId: userId,
                referenceId: payoutRequestId,
                ewalletType: "payout",
                amount: feeAmount,
                balance: remainingBalance,
                amountType: "payout_fee",
                type: "debit",
                dateAdded: currentDate
            }, { transaction });
        } 
        return true;
    }

    async getPayoutTilePercentages(payoutOverviewNew,payoutOverviewOld) {
        const {
            payoutRequested,
            payoutApproved,
            payoutPaid,
            payoutRejected
        } = payoutOverviewNew;

        const {
            payoutRequested: payoutRequestedOld,
            payoutApproved: payoutApprovedOld,
            payoutPaid: payoutPaidOld,
            payoutRejected: payoutRejectedOld
        } = payoutOverviewOld;

        let payoutRequestedPercentage = 
            payoutRequestedOld==0
                ? payoutRequested - payoutRequestedOld
                : ((payoutRequested - payoutRequestedOld)/payoutRequestedOld) * 100;

        let payoutApprovedPercentage = 
            payoutApprovedOld==0
                ? payoutApproved - payoutApprovedOld
                : ((payoutApproved - payoutApprovedOld)/payoutApprovedOld) * 100;

        let payoutPaidPercentage = 
            payoutPaidOld==0
                ? payoutPaid - payoutPaidOld
                : ((payoutPaid - payoutPaidOld)/payoutPaidOld) * 100;
                
        let payoutRejectedPercentage = 
            payoutRejectedOld==0
                ? payoutRejected - payoutRejectedOld
                : ((payoutRejected - payoutRejectedOld)/payoutRejectedOld) * 100  ;
                
        const payoutRequestedSign = payoutRequested > payoutRequestedOld ? "up" : "down";
        const payoutApprovedSign  = payoutApproved > payoutApprovedOld ? "up" : "down";
        const payoutPaidSign      = payoutPaid > payoutPaidOld ? "up" : "down";
        const payoutRejectedSign  = payoutRejected > payoutRejectedOld ? "up" : "down";
        
        return {
            payoutRequestedPercentage,
            payoutRequestedSign,
            payoutApprovedPercentage,
            payoutApprovedSign,
            payoutPaidPercentage,
            payoutPaidSign,
            payoutRejectedPercentage,
            payoutRejectedSign
        };
    
    }
}
export default new PayoutService;