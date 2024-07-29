import { Op } from "sequelize";
import { sequelize } from "../../config/db.js";
import { consoleLog, convertToUTC, errorMessage, logger, formatLargeNumber, successMessage } from "../../helper/index.js";
import { usernameToid, getConfig, generateTransactionId, verifyTransactionPassword, getConfiguration } from "../../utils/index.js";
import ewalletService from "../../services/ewalletService.js";
import utilityService from "../../services/utilityService.js";
import { UserBalanceAmount } from "../../models/association.js";

export const getEwalletTiles = async (req, res, next) => {
	try {
		const userId                      = req.auth.user.id;
		const currentDate                 = new Date();
		const fromDate                    = await utilityService.subtractDates(currentDate, "month");
		
		// get totals from userbalanceamount, ewallet history tables etc
		const userBalanceAndEwalletsTotal = await ewalletService.getUserBalanceAndEwallet(userId);
		const userBalanceAndEwalletsNew   = await ewalletService.getUserBalanceAndEwallet(userId,currentDate,"month");
		const userBalanceAndEwalletsOld   = await ewalletService.getUserBalanceAndEwallet(userId,fromDate,"month");
		let tilePercentages               = await ewalletService.getEwalletTilePercentages(userBalanceAndEwalletsNew,userBalanceAndEwalletsOld);
		
		// get sum of pin amounts
		const getEpinBalance              = await ewalletService.getEpinBalance(req, res, next);

		// sums of payouts requested, approved, released, rejected
		const payoutOverview           = await ewalletService.getPayoutOverview(req, res, next);
		Object.entries(payoutOverview).forEach(([key, value]) => {
            payoutOverview[key] = value;
        });

		// ewallet doughnut
		const spent 		= parseFloat(userBalanceAndEwalletsTotal.ewalletPurchaseDebit) + parseFloat(userBalanceAndEwalletsTotal.ewalletTransferDebit) + parseFloat(userBalanceAndEwalletsTotal.ewalletTransferDebitFee);
		const balance 		= parseFloat(userBalanceAndEwalletsTotal.userBalance);
		const spentRatio 	= Math.round(spent/(spent + balance) * 100);
		const balanceRatio 	= Math.round(balance/(spent + balance) * 100);
		const data = {
			ewalletBalance   : userBalanceAndEwalletsTotal.userBalance,
			purchaseWallet   : userBalanceAndEwalletsTotal.purchaseWallet,
			creditedAmount   : userBalanceAndEwalletsTotal.ewalletCommission + userBalanceAndEwalletsTotal.ewalletTransferCredit,
			creditPercentage : formatLargeNumber(tilePercentages.totalCreditPercentage),
			creditSign       : tilePercentages.creditSign,
			debitedAmount    : userBalanceAndEwalletsTotal.ewalletPurchaseDebit + userBalanceAndEwalletsTotal.ewalletTransferDebit + userBalanceAndEwalletsTotal.ewalletTransferDebitFee,
			debitPercentage  : formatLargeNumber(tilePercentages.totalDebitPercentage),
			debitSign        : tilePercentages.debitSign,
			epinBalance      : getEpinBalance.amount,
			payoutOverview   : payoutOverview,
			spentRatio       : spentRatio,
			balanceRatio     : balanceRatio,
			spent            : spent,
			balance          : balance
		};
		const response = await successMessage({ data: data });
		return res.status(response.code).json(response.data);
	} catch (error) {
		// logger.error("ERROR FROM getEwalletTiles",error);
		console.log(error)
		return next(error);
	}
};

export const ewalletStatement = async (req, res, next) => {
    try {
        const response = await ewalletService.ewalletStatement(req, res, next);
        return res.status(200).json(response.data);
    } catch (error) {
        return next(error);
    }
};

export const ewalletTransferHistory = async (req, res, next) => {
    try {
        const response = await ewalletService.ewalletTransferHistory(req, res, next);
        return res.status(response.code).json(response.data);
    } catch (error) {
        return next(error);
    }
};

export const getPurchaseWallet = async (req, res, next) => {
	try {
		const response = await ewalletService.getPurchaseWallet(req, res, next);
		return res.status(response.code).json(response.data);
	} catch (error) {
		return next(error);
	}
};

export const getMyEarnings = async (req, res, next) => {
	try {
		const page 		= parseInt(req.query.page) || 1;
		const pageSize 	= parseInt(req.query.perPage) || 10; // length
		const offset 	= (page - 1) * pageSize;
		const startDate = req.query.startDate || false;
		const endDate   = req.query.endDate || false;
		const direction = req.query.direction || "DESC";
		const types     = (req.query.type && req.query.type != "") ? req.query.type.split(',') : [];
		const userId    = req.auth.user.id;
		
		let whereClause = {};
		whereClause.userId = userId;
		if(startDate || endDate) {
			const formattedStartDate = new Date(convertToUTC(startDate));
			const formattedEndDate = new Date(convertToUTC(endDate));
			if((formattedStartDate && formattedEndDate)) {
				whereClause.createdAt = { [Op.between]: [formattedStartDate, formattedEndDate] };
			}
			if(formattedStartDate && !formattedEndDate) {
				whereClause.createdAt = { [Op.gte]: formattedStartDate };
			}
			// if(formattedStartDate.getTime() === formattedEndDate.getTime()) {
			// 	whereClause.createdAt = { [Op.gte]: formattedStartDate };
			// }
			if(!formattedStartDate && formattedEndDate) {
				whereClause.createdAt = { [Op.lte]: formattedEndDate };
			}
		}

		const dropdownList = await ewalletService.getAmountTypes(null);
		const dropdown = dropdownList.map(commission => ({
			label: commission,
			value: commission
		}));

		const amountTypes = await ewalletService.getAmountTypes(types);
		whereClause.amountType = amountTypes ?? { [Op.not]: null };

		const { count, data } = await ewalletService.getMyEarnings(req, res, next, whereClause, direction, pageSize, offset);
		
		const response =  await successMessage({ data: {
			totalCount: count,
			totalPages: Math.ceil(count / pageSize),
			currentPage: page,
			dropdown: dropdown,
			data: data,
		}});

		return res.status(response.code).json(response.data);

	} catch (error) {
		return next(error);
	}
};

export const fundTransfer = async (req, res, next) => {
	const checkPassword = await verifyTransactionPassword(req, res, next);
	if(!checkPassword) {
		const response =  await errorMessage({ code: 1015, statusCode: 422 });      
		return res.status(response.code).json(response.data);
	} 
	const toUser       = await usernameToid(req.body.username);
	if(!toUser) {
		const response = await errorMessage({ code: 1011, statusCode: 422 });
		return res.status(response.code).json(response.data);
	}
	if(toUser.id === req.auth.user.id) {
		const response = await errorMessage({ code: 1088, statusCode: 422 });
		return res.status(response.code).json(response.data);
	}
	let amount 			= parseFloat(req.body.amount);
	let notes 			= req.body.notes ?? "";
	const configuration = await getConfiguration();
	const transFee      = configuration.transFee;
	const totalAmount   = amount + parseFloat(transFee);
	const userbalance   = await ewalletService.getUserBalance(req.auth.user.id);
	logger.debug(`FUND TRANSFER${amount} FROM ${req.auth.user.id} TO ${toUser.id} TRANS FEE ${transFee}`);
	
	if(parseFloat(userbalance.ewalletBalance) < totalAmount) {
		const response  = await errorMessage({ code: 1014, statusCode: 422});
		return res.status(response.code).json(response.data);
	} 
		
	let dbTransaction;
	try {
		dbTransaction   		= await sequelize.transaction();
		const newTransactionId 	= await generateTransactionId();
		const fromUserBalance 	= await UserBalanceAmount.findOne({where:{userId:req.auth.user.id}}); 
		const receiverBalance   = await UserBalanceAmount.findOne({ where: { userId: toUser.id } });
		const transactionId 	= await ewalletService.addToTransaction({ transactionId: newTransactionId, transaction: dbTransaction });
		const reduce = await ewalletService.reduceUserbalance({ 
							user: fromUserBalance, 
							deduction : totalAmount,
							type: "balanceAmount",
							transaction : dbTransaction
						});
		if(!reduce) {
			await dbTransaction.rollback();
			const response = await errorMessage({ code: 422, statusCode: 1087 });
			return res.status(response.code).json(response.data);
		}
		await ewalletService.addUserBalance({ 
							user: receiverBalance, 
							addition : amount,
							type: "balanceAmount",  
							transaction : dbTransaction
						});
		const fundTransfer = await ewalletService.addToFundTransferHistory({
			from: req.auth.user.id,
			to: toUser.id,
			amount,
			amountType: "user_credit",
			balance: receiverBalance.balanceAmount + amount,
			transFee,
			notes,
			transactionId : transactionId.id,
			transaction : dbTransaction
		});
		await ewalletService.addToEwalletTransferHistory({
			userId: req.auth.user.id,
			amount,
			type: "debit",
			amountType: "user_debit",
			balance: parseFloat(fromUserBalance.balanceAmount) - parseFloat(totalAmount),
			transactionFee: transFee,
			transactionId: transactionId.id,
			fundTransferId: fundTransfer.id,
			transaction : dbTransaction
		});

		await ewalletService.addToEwalletTransferHistory({
			userId: toUser.id,
			amount,
			type: "credit",
			amountType: "user_credit",
			balance: parseFloat(receiverBalance.balanceAmount) + parseFloat(amount),
			transactionFee: transFee,
			fundTransferId: fundTransfer.id,
			transaction : dbTransaction,
			transactionId: transactionId.id,
		});

		let activityData = {
			userId : req.auth.user.id,
			userType: "user",
			description: `${req.auth.user.username} transfered ${amount} to ${toUser.username}`,
			activity: "Fund transfer",
			data: "",
			ip: req.ip,
			transaction : dbTransaction
		};
		await utilityService.insertUserActivity(activityData);
		await dbTransaction.commit();
		const response =  await successMessage({ data: "Fundtransfer completed" });
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM fundTransfer",error);
		await dbTransaction.rollback();
		return next(error);
	}
};

export const getEwalletBalance = async (req, res, next) => {
	const userId 			= req.auth.user.id;
	const ewalletBalance 	= await UserBalanceAmount.findOne({ where:{ userId }});
	const respose 			= await successMessage( {data: ewalletBalance});
	return res.status(respose.code).json(respose.data);
}
