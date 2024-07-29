import { Op } from "sequelize";
import { sequelize } from "../../config/db.js";
import { consoleLog, convertTolocal, errorMessage, logger, formatLargeNumber, successMessage } from "../../helper/index.js";
import { getModuleStatus, verifyTransactionPassword } from "../../utils/index.js";
import EwalletService from "../../services/ewalletService.js";
import PaymentService from "../../services/paymentService.js";
import PayoutService from "../../services/payoutService.js";
import utilityService from "../../services/utilityService.js";
import mailService from "../../services/mailService.js";

import { AmountPaid, CompanyProfile, Configuration, CurrencyDetail, PaymentGatewayConfig, PaymentGatewayDetail, PayoutConfiguration, PayoutReleaseRequest, SignupSettings, User, UserBalanceAmount, UserDetail } from "../../models/association.js";

export const getPayoutDetails = async (req, res, next) => {
    try {
        const userId       = req.auth.user.id;
        const page         = parseInt(req.query.page) || 1;
        const pageSize     = parseInt(req.query.pageSize) || 10;
        const offset       = (page - 1) * pageSize;
        const statusList   = JSON.parse(req.query.status) || [];
        let amountPaids = []; let payoutReleaseRequests = [];
        
        if (statusList.includes("paid") || statusList.includes("approved")) {
            let whereClause = { userId: userId, status: { [Op.in]: [] } }
            if (statusList.includes("approved")) {
                whereClause.status[Op.in].push(0)
            }
            if (statusList.includes("paid")) {
                whereClause.status[Op.in].push(1)
            }

            let payoutData = await AmountPaid.findAll({
                attributes: ["updatedAt", "amount", "paymentMethod","status"],
                where: whereClause,
                include: [{ model: PaymentGatewayConfig, attributes: ["slug"] }],
                offset:offset,
                order: [["createdAt", 'DESC']],
                limit: pageSize,

                // raw: true
            });
            if (payoutData) {
                amountPaids = payoutData.map(row => ({
                    updatedAt: convertTolocal(row.updatedAt),
                    amount: row.amount,
                    paymentMethod: row.PaymentGatewayConfig.slug,
                    status: row.status == 0 ? "Approved" : "Paid"
                }))
            }

        }
        if (statusList.includes("requested") || statusList.includes("rejected")) {
            let whereClause = { userId: userId, status: { [Op.in]: [] } }
            if (statusList.includes("requested")) {
                whereClause.status[Op.in].push(0)
            }
            if (statusList.includes("rejected")) {
                whereClause.status[Op.in].push(2)
            }
            let payoutData = await PayoutReleaseRequest.findAll({
                attributes: ["updatedAt", "amount", "paymentMethod","status"],
                where: whereClause,
                include: [{ model: PaymentGatewayConfig, attributes: ["slug"] }],
                offset: offset,
                limit: pageSize,
                order: [["createdAt", 'DESC']],
                // raw: true
            });
            if (payoutData) {
                payoutReleaseRequests = payoutData.map(row => ({
                    updatedAt: convertTolocal(row.updatedAt),
                    amount: row.amount,
                    paymentMethod: row.PaymentGatewayConfig.slug,
                    status: row.status == 0 ? "Requested" : "Rejected"
                }))
            }
            
        }
        const result = [...amountPaids,...payoutReleaseRequests];

        const totalCount 	= result.length;
		const totalPages 	= Math.ceil(totalCount / pageSize);
		const currentPage 	= Math.floor(offset / pageSize) + 1;
        const data = {
            payoutDetails : {
                totalCount,
                totalPages,
                currentPage,
                data: result

            }
        };
        const response =  await successMessage({ data: data });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getPayoutDetails",error);
        return next(error);
    }
};

export const getPayoutRequestDetails = async (req,res,next) => {
    try {
        const userId                = req.auth.user.id;
        const payoutConfiguration   = await PayoutConfiguration.findOne({raw:true});
        const payoutOverview        = await EwalletService.getPayoutOverview(req, res, next);
        let payoutUserDetails       = await User.findOne({
            attributes: ["defaultCurrency"],
            where: { id: userId },
            include: [
                {model: UserDetail,
                attributes: ["userId"],
                include: [{ model: PaymentGatewayConfig, attributes: ["name"] }]},
                {model: UserBalanceAmount,
                    attributes: ["balanceAmount"]}
                ],
        });

        const availablePayoutAmount = await PayoutService.calculateAvailablePayoutAmount(payoutConfiguration,payoutUserDetails);

        const defaultCurrency = payoutUserDetails.defaultCurrency;
        let currencyDetails;
        if (defaultCurrency) {
            currencyDetails = await CurrencyDetail.findOne({ attributes: ["code","symbolLeft"], where: { id: defaultCurrency }, raw: true });
        } else {
            currencyDetails = await CurrencyDetail.findOne({ attributes: ["code","symbolLeft"], where: { default: 1 }, raw: true });
        }
        const data = {
            defaultCurrency       : `${currencyDetails.code} (${currencyDetails.symbolLeft})`,
            ewalletBalance        : payoutUserDetails.UserBalanceAmount.balanceAmount,
            requestInProgress     : parseFloat(payoutOverview.payoutRequested) + parseFloat(payoutOverview.payoutApproved),
            totalPaid             : payoutOverview.payoutPaid,
            payoutMethod          : payoutUserDetails.UserDetail.PaymentGatewayConfig.name,
            minPayoutAmount       : payoutConfiguration.minPayout,
            maxPayoutAmount       : payoutConfiguration.maxPayout,
            availablePayoutAmount : parseFloat(availablePayoutAmount) < 0 ? 0 : parseFloat(availablePayoutAmount),
            requestValidity       : payoutConfiguration.requestValidity,
            payoutFee             : payoutConfiguration.feeAmount,
            payoutFeeMode         : payoutConfiguration.feeMode
        };

        const response =  await successMessage({ data: data });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getPayoutRequestDetails",error);
        return next(error);
    }
};

export const payoutRequest = async(req,res,next) => {
    try {
        const userId              = req.auth.user.id;
        const payoutAmount        = Number(req.body.payoutAmount);
        const transactionPassword = req.body.transactionPassword;
        const ip                  = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const activityData = {
            user_id: userId,
            payout_amount:payoutAmount
        };
        
        const checkPassword = await verifyTransactionPassword(req, res, next);
        
        if(!checkPassword) {
            const response =  await errorMessage({ code: 1015, statusCode: 422 });      
            return res.status(response.code).json(response.data);
        }
        
        const payoutConfiguration = await PayoutConfiguration.findOne({raw:true});
        
        // check payoutAmount is within min max limits 
        if (payoutAmount < parseFloat(payoutConfiguration.minPayout)) {
            const response = await errorMessage({ code: 1027, statusCode: 422 });
            return res.status(response.code).json(response.data);
        } else if (payoutAmount > parseFloat(payoutConfiguration.maxPayout)) {
            const response = await errorMessage({ code: 1028, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        const userBalance   = await UserBalanceAmount.findOne({ attributes: ["balanceAmount"], where: { userId: userId } });

        const moduleStatus  = await getModuleStatus({attributes: ["kycStatus"]});
        let userDetails = await UserDetail.findOne({
            where: {userId:userId},
            include: [{ model: PaymentGatewayConfig, attributes: ["id","name"] }]
            
        });

        const kycStatus     = userDetails.kycStatus;
        const paymentMethod = userDetails.PaymentGatewayConfig.name;
        const paymentId     = userDetails.PaymentGatewayConfig.id;
        
        // check if payment method is valid for payout
        const result        = await PaymentService.checkPaymentMethod(req,res,next,userDetails, paymentMethod);
        if (result != true) {
            return res.status(result.code).json(result.data);
        }
        
        if (moduleStatus.kycStatus && !kycStatus) {
            let response = await errorMessage({ code: 1019, statusCode: 422 });
            return res.status(response.code).json(response.data);
        } else {

            let feeAmount = Number(payoutConfiguration["feeAmount"]);
            if (payoutConfiguration.feeMode == "percentage") {
                feeAmount = parseFloat(payoutAmount * feeAmount / 100);
            }
            const totalAmount       = parseFloat(payoutAmount) + parseFloat(feeAmount);
            const remainingBalance  = parseFloat(userBalance.balanceAmount) - parseFloat(totalAmount);
            if (parseFloat(remainingBalance) < 0) {
                let response = await errorMessage({code: 1014, statusCode: 422 });
                return res.status(response.code).json(response.data);
            } 

            // start transaction
            let transaction = await sequelize.transaction();
            try {
                const result = await PayoutService.payoutRequestTransaction(req.auth.user, transaction, paymentId, payoutAmount, feeAmount, totalAmount, remainingBalance, payoutConfiguration);
                if (result == true) { await transaction.commit() };
            } catch (error) {
                logger.warn("TRANSACTION ERROR",error);
                await transaction.rollback();
                throw error;
            }
            await utilityService.insertUserActivity({
                userId: userId,
                userType: "user",
                data: JSON.stringify(activityData),
                description: "Payout requested.",
                ip: ip,
                activity: "payout request",
            });
            
            const companyProfile     = await CompanyProfile.findOne();
            const mailDetails = {
                    payoutAmount
                };
            if(parseInt(payoutConfiguration.mailStatus) && companyProfile.email) {
                const toData = {
                    name    : companyProfile.name,
                    fullName: companyProfile.name,
                    to      : companyProfile.email
                }
                await mailService.sentNotificationMail({mailType:'payout_request', toData, authUser:req.auth.user, mailDetails});
            }
        }
        const response =  await successMessage({ data: "Payout requested successfully." });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM payoutRequest",error);
        return next(error);
    }
};

export const cancelPayoutRequest = async(req,res,next) => {
    try {
        const userId       = req.auth.user.id;
		const ip           = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const payoutIdArr  = req.body.payoutIdArr;
        const activityData = {
            user_id:userId,
            payout_id: payoutIdArr
        };

        const payoutRequests = await PayoutReleaseRequest.findAll({
            // attributes:[],
            where: { id: {[Op.in]: payoutIdArr }, userId: userId, status: 0 },
            raw: true
        });

        if (!payoutRequests.length) return res.status(422).json(await errorMessage({ code: 1051 }));

        let transaction = await sequelize.transaction();
        try{
            // delete the request
            const result = await PayoutService.cancelPayoutRequest(req,res,next,transaction,payoutIdArr,payoutRequests);
            result === true ? await transaction.commit() : await transaction.rollback();
        } catch (error) {
            logger.warn("TRANSACTION ERROR");
            await transaction.rollback();
            throw error;
        }

        utilityService.insertUserActivity({
			userId      : userId,
			userType    : "user",
			data        : JSON.stringify(activityData),
			description : "Payout request deleted.",
			ip          : ip,
			activity    : "request deleted",
			// transaction: None
		});
        const response =  await successMessage({ data: "Payout request cancelled successfully." });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM caoncelPayoutRequest",error);
        return next(error);
    }
};

export const getPayoutTiles = async (req, res, next) => {
    try {
        const currentDate         = new Date();
        const fromDate            = await utilityService.subtractDates(currentDate, "month");
        const payoutOverviewNew   = await EwalletService.getPayoutOverview(req, res, next, currentDate, "month");
        const payoutOverviewOld   = await EwalletService.getPayoutOverview(req, res, next, fromDate, "month");
        const tilePercentages     = await PayoutService.getPayoutTilePercentages(payoutOverviewNew,payoutOverviewOld);
        const payoutOverviewTotal = await EwalletService.getPayoutOverview(req, res, next);
        Object.entries(payoutOverviewTotal).forEach(([key, value]) => {
            payoutOverviewTotal[key] = value;
        });
        const response =  await successMessage({ data: {payoutOverviewTotal, tilePercentages} });
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getPayoutTiles",error);
        return next(error);
    }
}