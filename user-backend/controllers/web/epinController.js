import { Op } from "sequelize";
import { sequelize } from "../../config/db.js";
import { consoleLog, convertToUTC, errorMessage, logger, formatLargeNumber, successMessage } from "../../helper/index.js";
import { verifyTransactionPassword, usernameToid } from "../../utils/index.js";

import EpinService from "../../services/epinService.js";
import ewalletService from "../../services/ewalletService.js";
import utilityService from "../../services/utilityService.js";

import { EpinTransferHistory, PinAmountDetail, PinNumber,  User,  UserBalanceAmount } from "../../models/association.js";


export const getEpinTiles = async (req,res,next) => {
	try {
		const userId = req.auth.user.id;
		await EpinService.updateExpiredEpinStatus(userId);

		const epinTileData = await EpinService.getEpinTiles(req,res,next);

		// calculate total epinBalance
		let epinBalance = 0;
			epinTileData?.allocatedUser?.forEach((epin) => {
				epinBalance += epin.dataValues.balanceAmount;
			});
		

		// count of active epins
		const epinCount = epinTileData?.allocatedUser?.length || 0;
		
		// count of pending epin requests
		const pendingRequestCount = epinTileData?.PinRequests?.filter(entry => entry.status === 1).length || 0;
		epinBalance = epinBalance;

		const epinTiles = { epinCount, epinBalance, pendingRequestCount };
		
		// pass epinAmounts for epin purchase
		const epinAmounts = await EpinService.getEpinAmounts();

		// list of epins available for epin-transfer
		const epinTransferList = epinTileData?.allocatedUser?.map(epin => ({label: epin.numbers, value: epin.numbers})) || null;
		// const epinTransferList = [];

		const { ewalletBalance } = await ewalletService.getUserBalance(userId);

		const response =  await successMessage({ data: {epinTiles, epinAmounts, epinTransferList, ewalletBalance  }  });
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM getEpinTiles",error);
		return next(error);
	}
};

export const getEpinList = async (req, res, next) => {
	try {
		const page       = parseInt(req.query.page) || 1;
		const pageSize   = parseInt(req.query.perPage) || 10; // length
		const offset     = (page - 1) * pageSize;
		const direction  = req.query.direction || "DESC";
		const epinStatus = (req.query.status && req.query.status != "") ? req.query.status.split(",") : null;
		const epins      = (req.query.epins && req.query.epins != "") ? req.query.epins.split(",") : null;			
		const amounts    = (req.query.amounts && req.query.amounts != "") ? req.query.amounts.split(",") : null;			
		const userId     = req.auth.user.id;
		
		await EpinService.updateExpiredEpinStatus(userId);

		const { count, rowData } = await EpinService.getEpinList({userId, epinStatus, epins, amounts, direction, offset, pageSize});


		const data = {
			totalCount: count,
			totalPages: Math.ceil(count / pageSize),
			currentPage: page,
			data: rowData
		};

		const response =  await successMessage({ data: data });
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM getEpinList",error);
		return next(error);
	}
};

export const getEpinPartials = async (req, res, next) => {
	try {
		const status = [
			{ "label": "active", "value": "active" },
			{ "label": "expired", "value": "expired" },
			{ "label": "blocked", "value": "blocked" },
			{ "label": "deleted", "value": "deleted" },
			{ "label": "used", "value": "used" }
		];
		const amounts = await EpinService.getEpinAmounts();
		const response = await successMessage({ data: { amounts, status } });
        return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM getEpinPartials",error);
		return next(error);
	}
}

export const getPendingEpinRequest = async (req, res, next) => {
	try {
		const page      = parseInt(req.query.page) || 1;
		const pageSize  = parseInt(req.query.perPage) || 10; // length
		const offset    = (page - 1) * pageSize;
		const direction = req.query.direction || "DESC";
		const userId    = req.auth.user.id;

		// await EpinService.updateExpiredEpinStatus(userId);

		const { count, result } = await EpinService.getPendingEpinRequest(req, res, next, userId, offset, pageSize);

		const data = {
			totalCount: count,
			totalPages: Math.ceil(count / pageSize),
			currentPage: page,
			data: result
		};
		const response =  await successMessage({ data: data });
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM getPendingEpinRequest",error);
		return next(error);
	}
};

export const getEpinTransferHistory = async (req, res, next) => {
	try {
		const page      = parseInt(req.query.page) || 1;
		const pageSize  = parseInt(req.query.perPage) || 10; // length
		const offset    = (page - 1) * pageSize;
		const direction = req.query.direction || "DESC";
		const userId    = req.auth.user.id;

		const { count, data } = await EpinService.getEpinTransferHistory(req, res, next, userId, offset, pageSize);

		const result = {
			totalCount: count,
			totalPages: Math.ceil(count / pageSize),
			currentPage: page,
			data: data
		};
		const response =  await successMessage({ data: result });
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM getEpinTransferHistory",error);
		return next(error);
	}
};

export const epinPurchase = async (req, res, next) => {
	try {
		const epinCount                     = req.body.epinCount;
		const expiryDate                    = convertToUTC(req.body.expiryDate);
		const amountCodes                   = req.body.amountCode;
		const ip                            = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
		const userId                        = req.auth.user.id;
		const { charset, length, maxCount } = await EpinService.getPinConfig();
		const checkPassword                 = await verifyTransactionPassword(req, res, next);
		
		if (!checkPassword) {
			const response = await errorMessage({ code: 1015, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		if (epinCount*amountCodes.length > maxCount) {
			const response = await errorMessage({ code: 429, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		
		let userBalance                     = await UserBalanceAmount.findOne({where:{userId:userId}}); 
		const amountList                    = await EpinService.getPinAmount(amountCodes);
		const activityData = {
			pin_count: epinCount,
			amount: amountList,
			expiry_date: expiryDate,
		};
		let totalAmount = 0;
		amountList.forEach((row) => {
			totalAmount += row.amount * epinCount;
		});
		
		if (parseFloat(userBalance.balanceAmount) < (totalAmount)) {
			const response = await errorMessage({ code: 1014, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		
		let transaction  = await sequelize.transaction();                  
		try {
			await EpinService.epinPurchaseTransaction(transaction, epinCount, charset, length, userId, userBalance, totalAmount, expiryDate, amountList);
		} catch (error) {
			await transaction.rollback();
			logger.info("ERROR IN EPIN PURCHASE TRANSACTION");
			throw error;
		}
		
		await utilityService.insertUserActivity({
			userId: userId,
			userType: "user",
			data: JSON.stringify(activityData),
			description: "Epin purchased using ewallet",
			ip: ip,
			activity: "Epin purchased",
			// transaction: None
		});
		const response =  await successMessage({ data: "Epin purchased successfully." });
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM epinPurchase",error);
		return next(error);
	}
};

export const epinRequest = async (req, res, next) => {
	try {
		const ip          = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const userId      = req.auth.user.id;
        const username    = req.auth.user.username;
        const epinCount   = req.body.epinCount;
        const amountCodes = req.body.amountCode;
        const expiryDate  = convertToUTC(req.body.expiryDate);

        if (epinCount > 49) {
			const response = await errorMessage({ code: 1045, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		const currentDate = new Date();
		if( new Date(expiryDate) <= currentDate){
			const response = await errorMessage({ code: 1103, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}

        const epinData = await PinAmountDetail.findAll({ where: { id: {[Op.in]: amountCodes} } });

        const result = await EpinService.epinRequestTransaction(userId, username, epinCount, epinData, expiryDate, ip);
        const response = await successMessage({ data: result });
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM epinRequest",error);
		return next(error);
	}
};

export const epinTransfer = async (req, res, next) => {
	try {
		const ip               = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
		const epins            = req.body.epin;
		const toUsername       = req.body.toUsername;
		const toUser 		   = await usernameToid(toUsername);
		const userId           = req.auth.user.id;
		const activityData 	   = {
									"epin": epins,
									"user": toUsername
								};
		if (!toUser) {
			const response = await errorMessage({ code: 1011, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		if (userId == toUser.id) {
			const response = await errorMessage({ code: 406, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		const epinData = await PinNumber.findAll({
			attributes: ["id", "numbers"],
			where: { numbers: { [Op.in]: epins }, allocatedUser: userId, purchaseStatus: 1 },
			raw: true
		});
		
		// Verify each epin in epins is present in epinData
		const epinCheck = epins.every(epin => epinData.find(epinData => epinData.numbers == epin));

		if (!epinCheck) {
			const response = await errorMessage({ code: 1016, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		const epinIds 	= epinData.map( item => item.id);
		const transfredEpins = await EpinTransferHistory.findAll({ where: { epinId: { [Op.in]: epinIds}}});
		if(transfredEpins.length) {
			const response = await errorMessage({ code: 1105, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		const response = await EpinService.epinTransfer(req, res, next, epinData, toUser);
		await utilityService.insertUserActivity({
			userId: userId,
			userType: "user",
			data: JSON.stringify(activityData),
			description: "Epin transferred using ewallet",
			ip: ip,
			activity: "Epin transferred",
			// transaction: None
		});

		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM epinTransfer",error);
		return next(error);
	}
};

export const epinRefund = async (req, res, next) => {
	try {
		const ip = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
		const epin = req.body.epin;
		const userId = req.auth.user.id;

		let epinEntry = await PinNumber.findOne({
			where: { numbers: epin, status: "active", allocatedUser: userId }
		});
		if (!epinEntry) {
			const response = await errorMessage({ code: 1016, statusCode: 422 });
			return res.status(response.code).json(response.data);
		};

		const result = await EpinService.epinRefundTransaction(req, res, next, epinEntry, ip);

		const activityData = {
			refund_id: epinEntry.id
		};
		await utilityService.insertUserActivity({
			userId: userId,
			userType: "user",
			data: JSON.stringify(activityData),
			description: "Epin refunded using ewallet",
			ip: ip,
			activity: "Epin refunded",
			// transaction: None
		});
		const response = await successMessage({ data: "Epin refunded successfully." });
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM epinRefund",error);
		return next(error);
	}
};

export const getPurchasedEpinList = async (req, res, next) => {
	try {
		const userId     = req.auth.user.id;
		const data 		 = await EpinService.getPurchasedEpinList({ userId, next });
		if(!data.epinTransferList.length) {
			const response = await errorMessage({ code: 1101, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		const response =  await successMessage({ data: data });
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM getPurchasedEpinList",error);
		return next(error);
	}
}
