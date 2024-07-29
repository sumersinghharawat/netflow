import { Op } from "sequelize";
import randomatic from "randomatic";
import { randomUUID } from "crypto";
import { sequelize } from "../config/db.js";
import { consoleLog, convertToUTC, convertTolocal, errorMessage, logger, formatLargeNumber, successMessage } from "../helper/index.js";
import ewalletService from "./ewalletService.js";
import utilityService from "./utilityService.js";
import { Activity, EpinTransferHistory, EwalletPurchaseHistory, Notification, PinAmountDetail, PinConfig, PinNumber, PinRequest, PinUsed, User, UserBalanceAmount, UserDetail } from "../models/association.js";


class EpinService {
    async getEpinTiles(req, res, next) {
        try {
            const userId    = req.auth.user.id;
            const epinTiles = await User.findOne({
                attributes: [],
                include: [
                    {
                        model: PinNumber,
                        attributes:["id","numbers","balanceAmount"],
                        as: "allocatedUser",
                        where: {status: "active"}
                    },
                    {
                        model: PinRequest,
                        attributes:["id","status"],
                        // where: {status: 1}
                    }
                ],
                where: { id: userId }
            });
            return epinTiles;
        } catch (error) {
            logger.error("ERROR FROM getEpinTiles",error);
            return next(error);
        }
    }

    async getEpinList({userId, epinStatus, epins, amounts, direction, offset, pageSize}) {
        const pinData = await PinNumber.findAndCountAll({
            attributes: ["numbers", "amount", "balanceAmount", "status", "expiryDate", "purchaseStatus"],
            where: {
                allocatedUser: userId,
                status: epinStatus ? { [Op.in]: epinStatus } : { [Op.not]: null },
                numbers: epins ? { [Op.in]: [epins] } : { [Op.not]: null },
            },
            include: [
                { model: PinAmountDetail, attributes: ["amount"], where: { id: amounts ?? { [Op.not]: null } } },
                { model: EpinTransferHistory, attributes: ["toUser"], required: false }
            ],
            order: [["expiryDate", direction]],
            offset: offset,
            limit: pageSize,
        });
        const rowData = pinData.rows.map(row => ({
            numbers: row.numbers,
            amount: row.amount,
            balanceAmount: row.balanceAmount,
            status: row.status,
            expiryDate: convertTolocal(row.expiryDate),
            purchaseStatus: (!row.EpinTransferHistory && row.status==="active") ? row.purchaseStatus : 0
        }))
        return { count: pinData.count, rowData };
    }

    async getPendingEpinRequest(req, res, next, userId, offset, pageSize) {
        try {

            const { count, rows } = await PinRequest.findAndCountAll({
                attributes: ["id", "requestedPinCount", "requestedDate", "expiryDate", "pinAmount"],
                where: { status: 1, userId: userId },
                raw: true,
                offset: offset,
                // order: [["expiryDate", direction]],
                limit: pageSize
            });
            const result = rows.map(row => ({
                ...row,
                requestedDate: convertTolocal(row.requestedDate),
                expiryDate: convertTolocal(row.expiryDate)
            }));
            return { count, result };
        } catch (error) {
            logger.error("ERROR FROM getPendingEpinRequest",error);
            return next(error);
        }
    }

    async getEpinTransferHistory(req, res, next, userId, offset, pageSize) {
        try {

            const { count, rows } = await EpinTransferHistory.findAndCountAll({
                attributes: ["date"],
                where: { [Op.or]: { toUser: userId, fromUser: userId } },
                include: [
                    {
                        model: PinNumber,
                        attributes: ["numbers", "amount", "allocatedUser", "generatedUser"]
                    },
                    {
                        model: User,
                        as: "toUserId",
                        attributes: ["username"],
                        include: [{ model: UserDetail, attributes: ["name", "secondName"] }]
                    },
                    {
                        model: User,
                        as: "fromUserId",
                        attributes: ["username"],
                        include: [{ model: UserDetail, attributes: ["name", "secondName"] }]
                    }
                ],
                offset: offset,
                limit: pageSize,
                raw: true
            });
            const data = rows.map(row => ({
                name: row["PinNumber.allocatedUser"] == userId ? row["fromUserId.UserDetail.name"] : row["toUserId.UserDetail.name"],
                secondName: row["PinNumber.allocatedUser"] == userId ? row["fromUserId.UserDetail.second_name"] : row["toUserId.UserDetail.second_name"],
                username: row["PinNumber.allocatedUser"] == userId ? row["fromUserId.username"] : row["toUserId.username"],
                epin: row["PinNumber.numbers"],
                amount: row["PinNumber.amount"],
                transferredDate: convertTolocal(row["date"]),
                action: row["PinNumber.allocatedUser"] == userId ? "Received" : "Transferred",
            }))

            return { count, data };
        } catch (error) {
            logger.error("ERROR FROM getEpinTransferHistory",error);
            return next(error);
        }
    }

    async getEpinAmounts() {
        return await PinAmountDetail.findAll({ attributes: [["id", "value"], ["amount", "label"]], order: [["label", "ASC"]] });
    }

    async epinPurchaseTransaction(transaction, epinCount, charset, length, userId, userBalance, totalAmount, expiryDate, amountList) {
        try {
		    const pinNumbers = await this.generatePinNumbers(epinCount, charset, length, userId, expiryDate, amountList);

			await Promise.all([
				PinNumber.bulkCreate(pinNumbers, { transaction }),
				ewalletService.reduceUserbalance({
					user: userBalance,
					deduction: totalAmount,
					type: "balanceAmount",
					transaction: transaction
				}),
				ewalletService.addToEwalletPurchaseHistory({
					userId: userId,
					referenceId: null,
					ewalletType: "pin_purchase",
					amount: totalAmount,
					balance: userBalance.balanceAmount - totalAmount,
					amountType: "pin_purchase",
					type: "debit",
					dateAdded: new Date(),
					transaction
				}),
			]);

			await transaction.commit();
        } catch (error) {
            throw error;
        }
    }

    async getPinConfig() {
        const pinConfig = await PinConfig.findOne({});
        let charset;

        switch (pinConfig.characterSet) {
            case "alphabet":
                charset = "A";
                break;
            case "numeric":
                charset = "0";
                break;
            case "alphanumeric":
            default:
                charset = "Aa0";
        }

        return { charset, length: pinConfig.length, maxCount: pinConfig.maxCount };
    }

    async epinRequestTransaction(userId, username, epinCount, epinData, expiryDate, ip) {
        let transaction = await sequelize.transaction();

        try {
            const currentDate = new Date();
            
            const pinRequestData = epinData.map(epin => ({
                userId: userId,
                requestedPinCount: epinCount,
                allotedPinCount: 0,
                requestedDate: currentDate,
                expiryDate: expiryDate,
                status: parseInt(1),
                pinAmount: epin.amount
            }));

            const notificationData = epinData.map(epin => (
                {
                    id: randomUUID(),
                    type: "Epin Request Notification",
                    notifiableType: 'App\\Models\\User',
                    notifiableId: 1,
                    data: JSON.stringify({
                        "type": "epin_request",
                        "title": "epin_request_title",
                        "request_id": userId,
                        "amount": epin.amount,
                        "count": epinCount,
                        "user_id": userId,
                        "username": username,
                        "icon": "<i class='bx bx-bookmark-plus'></i>"
                    })
                }
                ));
            const activityData = {
                pin_count: epinCount,
                amount: epinData,
                expiry_date: expiryDate,
            };
            await Promise.all([
                PinRequest.bulkCreate( pinRequestData, { transaction }),
                Notification.bulkCreate( notificationData, { transaction }),
            ]);

            await transaction.commit();

            await utilityService.insertUserActivity({
                userId: userId,
                userType: "user",
                data: JSON.stringify(activityData),
                description: "Epin requested using ewallet",
                ip: ip,
                activity: "Epin requested",
                // transaction: None
            });
            return "Epin Request Successful.";
        } catch (error) {
            console.log(error);
            await transaction.rollback();
            return await errorMessage({ code: 1045, statusCode: 422 });
        }
    }

    async epinTransfer(req, res, next, epinData, toUser) {
        const ip               = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const epin             = req.body.epin;
        const toUsername       = req.body.toUsername;
        const userId           = req.auth.user.id;
        const transferFromUser = req.auth.user.username;
        const epinIds          = epinData.map(epin => epin.id);
        const currentDate      = new Date();
        let transaction;

        try {
            const epinTransferData = epinData.map((epin) => ({
                toUser: toUser.id,
                fromUser: userId,
                epinId: epin.id,
                ip: ip,
                doneBy: userId,
                activity: "Epin transferred",
                date: currentDate
            }));

            transaction = await sequelize.transaction();
            await Promise.all([
                PinNumber.update({ allocatedUser: toUser.id }, { where: { id: { [Op.in]: epinIds } }, transaction }),
                EpinTransferHistory.bulkCreate(epinTransferData, { transaction }),
            ]);
            await transaction.commit();
            return await successMessage({ data: "Epin transfer successfully." });

        } catch (error) {
            logger.warn("EPIN TRANSFER TRANSACTION ERROR");
            if (transaction) { await transaction.rollback(); }
            throw error;
        }
    }

    async epinRefundTransaction(req, res, next, epinEntry, ip) {
        const userId          = req.auth.user.id;
        let userBalanceAmount = await UserBalanceAmount.findOne({ where: { userId: userId } });


        let transaction;
        try {
            transaction = await sequelize.transaction();
            const currentDate = new Date();
            
            await Promise.all([
                PinNumber.update({ status: "deleted" }, { where: { id: epinEntry.id }, transaction }),
                ewalletService.addUserBalance({
                    user: userBalanceAmount,
                    addition: Number(epinEntry.balanceAmount),
                    type: "balanceAmount",
                    transaction: transaction
                }),
                EwalletPurchaseHistory.create({
                    userId: userId,
                    referenceId: null,
                    ewalletType: "pin_purchase",
                    amount: epinEntry.balanceAmount,
                    balance: Number(userBalanceAmount.balanceAmount) + Number(epinEntry.balanceAmount),
                    amountType: "pin_purchase_refund",
                    type: "credit",
                    transactionFee: 0,
                    dateAdded: currentDate
                }, { transaction }),

            ]);
            await transaction.commit();
        } catch (error) {
            await transaction.rollback();
            logger.info("ERROR FROM epinRefundTransaction");
            throw error;
        }
    }

    async updateExpiredEpinStatus(userId) {
        const currentDate = new Date();

        await PinNumber.update(
            { status: "expired" },
            { where: { status: "active", allocatedUser: userId, expiryDate: { [Op.lte]: currentDate } } }
        );

        await PinRequest.update(
            { status: 0 },
            { where: { status: 1, userId: userId, expiryDate: { [Op.lte]: currentDate } } }
        );
    }

    async getPinAmount(amountCodes) {
        return await PinAmountDetail.findAll({ attributes: ["amount"], where: { id: { [Op.in]: amountCodes } }, raw: true });
    }

    async generatePinNumbers(epinCount, charset, length, userId, expiryDate, amountList) {
        const totalCount = epinCount * amountList.length;
        let pinNumbers = [];
        console.log("totalCount",totalCount);
        for (let i=0; i < totalCount; i++) {
            const amountIndex   = i % amountList.length;
            const amount        = amountList[amountIndex].amount;
            const transactionId = randomatic("Aa0", 13);
            const epinNumber    = randomatic(charset, length);
            pinNumbers.push({
                numbers: epinNumber,
                allocDate: new Date(),
                status: "active",
                uploadedDate: new Date(),
                generatedUser: userId,
                allocatedUser: userId,
                expiryDate: expiryDate,
                amount: amount,
                balanceAmount: amount,
                purchaseStatus: 1,
                transactionId: transactionId,
            });
        }
        return pinNumbers;
    }

    async insertUsedPin(epinId, userId, amount, action, transaction) {
        const options = transaction ? { transaction } : {};
        const result = await PinUsed.create({
            epinId: epinId,
            usedBy: userId,
            amount: amount,
            usedFor: action
        }, options);
        return true;
    }

    async getPurchasedEpinList({ userId, next }) {
        let epins =[];
        try {
            const data = await PinNumber.findAll({
                attributes: ["numbers", "amount", "balanceAmount", "status", "expiryDate", "purchaseStatus"],
                where: {
                    status          : 'active',
                    allocatedUser   : userId,
                    purchaseStatus  : 1,
                    expiryDate: {
                        [Op.gte] : new Date()
                    },
                    '$EpinTransferHistory.id$': null,
                },
                include: [{
                    model: EpinTransferHistory,
                    required: false,
                }],
            });
            if(data.length){
                epins = data.map( item => {
                    return {
                        label : item.numbers,
                        value : item.numbers
                    }
                })
            }
            return {epinTransferList: epins};
        } catch (error) {
            logger.error("ERROR FROM getPurchasedEpinList",error);
            return next(error);
        }
    }
}

export default new EpinService;