import moment from "moment";
import { Op, Sequelize } from "sequelize";
import { sequelize } from "../config/db.js";
import { convertTolocal, convertToUTC, consoleLog, convertSnakeCaseToTitleCase, errorMessage, logger, formatLargeNumber, successMessage } from "../helper/index.js";
import { usernameToid, getConfig, generateTransactionId, getModuleStatus } from "../utils/index.js";
import utilityService from "./utilityService.js";
import { 
    AggregateUserCommissionAndIncome, 
    AmountPaid, 
    Compensation, 
    EwalletCommissionHistory, 
    EwalletPurchaseHistory,
    EwalletTransferHistory,
    LegAmount, 
    PayoutReleaseRequest, 
    PinNumber, 
    PurchaseWalletHistory, 
    User, 
    UserBalanceAmount, 
    FundTransferDetail, 
    UserDetail,
    Transaction, 
} from "../models/association.js";

class EwalletService {
    async getPayoutOverview(req, res, next, toDate=null, timeFrame=null) {
        try {
            const userId = req.auth.user.id;
            let fromDate;
            let createdAt = { [Op.not]: null };
            if (timeFrame) {
                fromDate = await utilityService.subtractDates(toDate, timeFrame);
                createdAt = { [Op.between]: [fromDate, toDate] };
            }

            const payoutDetails = await AmountPaid.findAll({
                attributes: ["user_id",
                    [Sequelize.fn("SUM", Sequelize.literal("CASE when type='released' and status=1 THEN amount ELSE 0 END")), "payoutPaid"],
                    [Sequelize.fn("SUM", Sequelize.literal("CASE when type='released' and status=0 THEN amount ELSE 0 END")), "payoutApproved"],
                    // [Sequelize.fn("SUM", Sequelize.literal("CASE when type='rejected' THEN amount ELSE 0 END")), "payoutRejected"]

                ],
                where: { userId: userId, createdAt: createdAt },
                raw: true
            });
            const payoutRequests = await PayoutReleaseRequest.findAll({
                attributes: [
                    "user_id",
                    [Sequelize.fn("SUM", Sequelize.literal("CASE when status=0 THEN amount ELSE 0 END")), "payoutRequested"],
                    [Sequelize.fn("SUM", Sequelize.literal("CASE when status=2 THEN amount ELSE 0 END")), "payoutRejected"]
                ],
                where: { userId: userId, createdAt:createdAt },
                raw: true
            });
            return {
                payoutRequested: Number(payoutRequests[0].payoutRequested) || 0,
                payoutApproved : Number(payoutDetails[0].payoutApproved) || 0,
                payoutPaid     : Number(payoutDetails[0].payoutPaid) || 0,
                payoutRejected : Number(payoutRequests[0].payoutRejected) || 0,
            };
        } catch (error) {
            logger.error("ERROR FROM getPayoutOverview",error);
            return next(error);
        }
    }
    
    async getUserBalanceAndEwallet(userId, toDate = null, timeFrame = null) {
        try {
            let fromDate;
            let createdAt = { [Op.not]: null };
            
            if (timeFrame) {
                fromDate = await utilityService.subtractDates(toDate, timeFrame);
                createdAt = { [Op.between]: [fromDate, toDate] };
                // console.log("from getUserBalanceAndEwallet",createdAt)
            }

            const result = await Promise.all([
                EwalletCommissionHistory.findOne({
                    attributes: [[Sequelize.fn("SUM", Sequelize.col("amount")), "commission"]],
                    where: { userId: userId, createdAt: createdAt },
                    raw: true
                }),
                PurchaseWalletHistory.findOne({
                    attributes: [[Sequelize.fn("SUM", Sequelize.col("purchase_wallet")), "purchaseWalletTotal"]],
                    where: { userId: userId, createdAt: createdAt },
                    raw: true
                }),
                EwalletTransferHistory.findOne({
                    attributes: [
                        [Sequelize.fn("SUM", Sequelize.literal("CASE WHEN type = 'credit' THEN amount ELSE 0 END")), "transferCredit"],
                        [Sequelize.fn("SUM", Sequelize.literal("CASE WHEN type = 'debit' THEN amount ELSE 0 END")), "transferDebit"],
                        [Sequelize.fn("SUM", Sequelize.literal("CASE WHEN type = 'debit' THEN transaction_fee ELSE 0 END")), "transferDebitFee"],
                    ],
                    where: { userId: userId, dateAdded: createdAt },
                    raw: true
                }),
                EwalletPurchaseHistory.findOne({
                    attributes: [
                        [Sequelize.fn("SUM", Sequelize.literal("CASE WHEN type = 'credit' THEN amount ELSE 0 END")), "purchaseCredit"],
                        [Sequelize.fn("SUM", Sequelize.literal("CASE WHEN type = 'debit' THEN amount ELSE 0 END")), "purchaseDebit"]
                    ],
                    where: { userId: userId, dateAdded: createdAt },
                    raw: true
                }),
                UserBalanceAmount.findOne({
                    attributes:["balanceAmount"],
                    where:{userId:userId},
                    raw:true
                })
            ]);
            let ewallets = {};
            // ewallettransferhistory total credit and total debit
            result.forEach((ewallet) => {
                Object.entries(ewallet).forEach(([key, value]) => {
                    ewallets[key] = Number(value) || 0;
                });
              });

            // const userBalance = ewallets.commission + ewallets.transferCredit + ewallets.purchaseCredit - ewallets.purchaseDebit - ewallets.transferDebit - ewallets.purchaseWalletTotal;

            const userBalanceAndEwallets = {
                userBalance: ewallets.balanceAmount,
                purchaseWallet: ewallets.purchaseWalletTotal,
                ewalletCommission: ewallets.commission,
                ewalletTransferCredit: ewallets.transferCredit,
                ewalletTransferDebit: ewallets.transferDebit,
                ewalletTransferDebitFee: ewallets.transferDebitFee,
                ewalletPurchaseCredit: ewallets.purchaseCredit,
                ewalletPurchaseDebit: ewallets.purchaseDebit
            };

            return userBalanceAndEwallets;
        } catch (error) {
            logger.error("ERROR FROM getUserBalanceAndEwallet");
            throw error;
        }
    }

    async getEpinBalance(req, res, next) {
        try {
            const userId = req.auth.user.id;

            const result = await PinNumber
                .scope("isActivePin", "isNotExpired")
                .findOne({
                    attributes: [[Sequelize.fn("sum", Sequelize.col("balance_amount")), "amount"]],
                    where: { allocated_user: userId },
                    // group:,
                    raw: true
                });

            const epinBalance = {
                amount: parseFloat(result["amount"]) || 0
            };
            return epinBalance;
        } catch (error) {
            logger.error("ERROR FROM getEpinBalance",error);
            return next(error);
        }
    }

    async ewalletStatement(req, res, next) {
        try {
            const page = parseInt(req.query.page) || 1;
            const pageSize = parseInt(req.query.perPage) || 10;
            const offset = (page - 1) * pageSize;
            const prefix = `${req.prefix}_`;
            const { id } = req.auth.user;
            const query = `
				WITH total_count AS (
				SELECT 
					COUNT(*) AS total 
				FROM 
					(
					(
						SELECT 
						ewalletTransferHistories.id, 
						ewalletTransferHistories.user_id, 
						ewalletTransferHistories.fund_transfer_id, 
						ewalletTransferHistories.amount, 
						ewalletTransferHistories.balance, 
						ewalletTransferHistories.amount_type, 
						ewalletTransferHistories.type, 
						ewalletTransferHistories.date_added, 
						ewalletTransferHistories.created_at, 
						ewalletTransferHistories.updated_at, 
						'fund_transfer' AS ewallet_type, 
						NULL AS purchase_wallet, 
						transaction_fee, 
						fromUser.username AS from_user,
                        toUser.username AS to_user
						FROM 
						${prefix}ewallet_transfer_histories AS ewalletTransferHistories 
						LEFT JOIN ${prefix}fund_transfer_details AS userTransDetails ON userTransDetails.id = ewalletTransferHistories.fund_transfer_id 
						LEFT JOIN ${prefix}users AS fromUser ON fromUser.id = userTransDetails.from_id 
                        LEFT JOIN ${prefix}users AS toUser ON toUser.id = userTransDetails.to_id 
						WHERE 
						EXISTS (
							SELECT 
							* 
							FROM 
							${prefix}users 
							WHERE 
							ewalletTransferHistories.user_id = ${prefix}users.id
							AND id = :userId
						)
					) 
					UNION 
					(
						SELECT 
						EwalletCommissionHistories.id, 
						EwalletCommissionHistories.user_id, 
						EwalletCommissionHistories.leg_amount_id AS reference_id, 
						EwalletCommissionHistories.amount, 
						EwalletCommissionHistories.balance, 
						EwalletCommissionHistories.amount_type, 
						'credit' AS type, 
						EwalletCommissionHistories.date_added, 
						EwalletCommissionHistories.created_at, 
						EwalletCommissionHistories.updated_at, 
						'commission' AS ewallet_type, 
						purchase_wallet, 
						NULL AS transaction_fee, 
						fromUser.username AS from_user,
                        NULL AS to_user
						FROM 
						${prefix}ewallet_commission_histories AS EwalletCommissionHistories
						LEFT JOIN ${prefix}users AS fromUser ON fromUser.id = EwalletCommissionHistories.from_id 
						WHERE 
						EXISTS (
							SELECT 
							* 
							FROM 
							${prefix}users
							WHERE 
							EwalletCommissionHistories.user_id = ${prefix}users.id 
							AND id = :userId
						)
					)
                    UNION
                    (
                        SELECT
                        EwalletPurchaseHistories.id,
                        EwalletPurchaseHistories.user_id,
                        EwalletPurchaseHistories.reference_id as reference_id,
                        EwalletPurchaseHistories.amount,
                        EwalletPurchaseHistories.balance,
                        EwalletPurchaseHistories.amount_type,
                        EwalletPurchaseHistories.type,
                        EwalletPurchaseHistories.date_added,
                        EwalletPurchaseHistories.created_at,
                        EwalletPurchaseHistories.updated_at,
                        EwalletPurchaseHistories.ewallet_type AS ewallet_type,
                        NULL AS purchase_wallet,
                        EwalletPurchaseHistories.transaction_fee as transaction_fee,
                        fromUser.username AS from_user,
                        NULL AS to_user
						FROM 
                        ${prefix}ewallet_purchase_histories AS EwalletPurchaseHistories
						LEFT JOIN ${prefix}users AS fromUser ON fromUser.id = EwalletPurchaseHistories.user_id
						WHERE 
                        EXISTS (
							SELECT 
							* 
							FROM 
							${prefix}users 
							WHERE 
							EwalletPurchaseHistories.user_id = ${prefix}users.id
							AND id = :userId
						)
                    )
					) AS subquery
				),
				paginated_data AS (
				SELECT 
					* 
				FROM 
					(
					(
						SELECT 
						ewalletTransferHistories.id, 
						ewalletTransferHistories.user_id, 
						ewalletTransferHistories.fund_transfer_id, 
						ewalletTransferHistories.amount, 
						ewalletTransferHistories.balance, 
						ewalletTransferHistories.amount_type, 
						ewalletTransferHistories.type, 
						ewalletTransferHistories.date_added, 
						ewalletTransferHistories.created_at, 
						ewalletTransferHistories.updated_at, 
						'fund_transfer' AS ewallet_type, 
						NULL AS purchase_wallet, 
						transaction_fee, 
						fromUser.username AS from_user,
                        toUser.username AS to_user
						FROM 
						${prefix}ewallet_transfer_histories AS ewalletTransferHistories 
						LEFT JOIN ${prefix}fund_transfer_details AS userTransDetails ON userTransDetails.id = ewalletTransferHistories.fund_transfer_id 
						LEFT JOIN ${prefix}users AS fromUser ON fromUser.id = userTransDetails.from_id
                        LEFT JOIN ${prefix}users AS toUser ON toUser.id = userTransDetails.to_id 
						WHERE 
						EXISTS (
							SELECT 
							* 
							FROM 
							${prefix}users 
							WHERE 
							ewalletTransferHistories.user_id = ${prefix}users.id
							AND id = :userId
						)
					) 
					UNION 
					(
						SELECT 
						EwalletCommissionHistories.id, 
						EwalletCommissionHistories.user_id, 
						EwalletCommissionHistories.leg_amount_id AS reference_id, 
						EwalletCommissionHistories.amount, 
						EwalletCommissionHistories.balance, 
						EwalletCommissionHistories.amount_type, 
						'credit' AS type, 
						EwalletCommissionHistories.date_added, 
						EwalletCommissionHistories.created_at, 
						EwalletCommissionHistories.updated_at, 
						'commission' AS ewallet_type, 
						purchase_wallet, 
						NULL AS transaction_fee, 
						fromUser.username AS from_user,
                        NULL AS to_user
						FROM 
						${prefix}ewallet_commission_histories AS EwalletCommissionHistories
						LEFT JOIN ${prefix}users AS fromUser ON fromUser.id = EwalletCommissionHistories.from_id 
						WHERE 
						EXISTS (
							SELECT 
							* 
							FROM 
							${prefix}users
							WHERE 
							EwalletCommissionHistories.user_id = ${prefix}users.id 
							AND id = :userId
						)
					)
                    UNION
                    (
                        SELECT
                        EwalletPurchaseHistories.id,
                        EwalletPurchaseHistories.user_id,
                        EwalletPurchaseHistories.reference_id as reference_id,
                        EwalletPurchaseHistories.amount,
                        EwalletPurchaseHistories.balance,
                        EwalletPurchaseHistories.amount_type,
                        EwalletPurchaseHistories.type,
                        EwalletPurchaseHistories.date_added,
                        EwalletPurchaseHistories.created_at,
                        EwalletPurchaseHistories.updated_at,
                        EwalletPurchaseHistories.ewallet_type AS ewallet_type,
                        NULL AS purchase_wallet,
                        EwalletPurchaseHistories.transaction_fee as transaction_fee,
                        fromUser.username AS from_user,
                        NULL AS to_user
						FROM 
                        ${prefix}ewallet_purchase_histories AS EwalletPurchaseHistories
						LEFT JOIN ${prefix}users AS fromUser ON fromUser.id = EwalletPurchaseHistories.user_id
						WHERE 
                        EXISTS (
							SELECT 
							* 
							FROM 
							${prefix}users 
							WHERE 
							EwalletPurchaseHistories.user_id = ${prefix}users.id
							AND id = :userId
						)
                    )
					) AS subquery
					ORDER BY 
					date_added DESC 
					LIMIT :limit OFFSET :offset
				)
				SELECT 
				paginated_data.*,
				total_count.total 
				FROM 
				paginated_data, 
				total_count;
				`;
            const statement = await sequelize.query(query,
                {
                    replacements: {
                        userId: id,
                        limit: pageSize,
                        offset,
                    },
                    type: sequelize.QueryTypes.SELECT,
                }
            );
            let rows = statement.map((row) => ({
                    id            : row.id,
                    userId        : row.user_id,
                    fundTransferId: row.fund_transfer_id,
                    amount        : row.amount,
                    balance       : row.balance,
                    amountType    : row.amount_type,
                    type          : row.type,
                    dateAdded     : convertTolocal(row.date_added),
                    ewalletType   : row.ewallet_type,
                    purchaseWallet: row.purchase_wallet,
                    transactionFee: row.transaction_fee,
                    fromUser      : row.from_user?.toUpperCase(),
                    toUser        : row.to_user?.toUpperCase(),
                    total         : row.total
            }));
            const totalCount = statement.length ? statement[0].total : [];
            const totalPages = Math.ceil(totalCount / pageSize);
            const currentPage = Math.floor(offset / pageSize) + 1;
            const response = {
                totalCount,
                totalPages,
                currentPage,
                data: rows,
            };
            return await successMessage({ data: response });
        } catch (error) {
            return next(error);
        }
    }

	async ewalletTransferHistory(req, res, next) {
		const page 		= parseInt(req.query.page) || 1;
		const pageSize 	= parseInt(req.query.perPage) || 10;
		const offset 	= (page - 1) * pageSize;
        const startDate = req.query.startDate || false;
        const endDate   = req.query.endDate || false;
        const type      = (req.query.type && req.query.type != "") ? req.query.type.split(','):[];
		const { id, username } 	= req.auth.user;
        let whereClause = {};
        whereClause.userId = id;

        if(startDate || endDate) {
            const formattedStartDate = new Date(convertToUTC(startDate));
            const formattedEndDate = new Date(convertToUTC(endDate));
            
            if((formattedStartDate && formattedEndDate)) {
                whereClause.createdAt = { [Sequelize.Op.between]: [formattedStartDate, formattedEndDate] };
            }
            if(formattedStartDate && !formattedEndDate) {
                whereClause.createdAt = { [Sequelize.Op.gte]: formattedStartDate };
            }
            // if(formattedStartDate.getTime() === formattedEndDate.getTime()) {
            //     whereClause.createdAt = { [Sequelize.Op.gte]: formattedStartDate };
            // }
            if(!formattedStartDate && formattedEndDate) {
                whereClause.createdAt = { [Sequelize.Op.lte]: formattedEndDate };
            }
        }
        whereClause.amountType = { [Sequelize.Op.in]: ['admin_credit', 'admin_debit','user_credit', 'user_debit', 'admin_user_credit', 'admin_user_debit']};

        if(type.length) whereClause.type = { [Sequelize.Op.in]:type};
        try {
            const transactionData = await EwalletTransferHistory.findAndCountAll({
                offset,
                limit: pageSize,
                where: whereClause,
                order: [["createdAt", "DESC"]],
                include:[ 
                    { 
                        model: User, 
                        attributes: ["username"],
                        include: [
                            { model: UserDetail, attributes: ["image", "name", "secondName"]}
                        ] 
                    },
                    { 
                        model: FundTransferDetail, 
                        attributes:[],
                        as: "TransferDetails", 
                        include: [ 
                            { model: User, as: "FromUser", attributes:["id", "username"]},
                            { model: User, as: "ToUser", attributes:["id", "username"]},
                        ],
                    },
                    { model: Transaction, attributes: ["transactionId"]}
                ],
                raw:true
            });

            const data = transactionData.rows.map(item => ({
                amount: item.amount,
                balance: parseFloat(item.balance),
                dateAdded: convertTolocal(item.dateAdded),
                transactionFee: item.transactionFee,
                fromUser: item["TransferDetails.FromUser.username"]?.toUpperCase(),
                toUser: item["TransferDetails.ToUser.username"]?.toUpperCase(),
                ewalletType: "fund_transfer",
                amountType: item['amountType'],
                type: item.type
            }));
            // type: item["TransferDetails.FromUser.username"] === username ? "debit" : "credit"
            const totalCount 	= transactionData.count;
			const totalPages 	= Math.ceil(totalCount / pageSize);
			const currentPage 	= Math.floor(offset / pageSize) + 1;
            const response      = {
                totalCount,
                totalPages,
                currentPage,
                data
            };
            return await successMessage({ data: response });
        } catch (error) {
            return next(error);
        }
	}

    async getPurchaseWallet(req, res, next) {
        try {
            const page 		= parseInt(req.query.page) || 1;
			const pageSize 	= parseInt(req.query.perPage) || 10; // length
			const offset 	= (page - 1) * pageSize;
            const userId   = req.auth.user.id;

            const { count, rows } = await PurchaseWalletHistory.findAndCountAll({
                attributes:{
                    include: [["created_at","dateAdded"]],
                    exclude: ["tds"]
                },
                include: [{ model: User, as: "purchaseWalletFromUser", attributes: ["username"] }],
                where: { userId: userId },
                raw:true,
                order: [["id", "DESC"]],
                offset: offset,
                limit: pageSize
            });
            const data = rows.map( row => ({
                amount      : row.purchaseWallet,
                fromUser    : row["purchaseWalletFromUser.username"]?.toUpperCase(),
                ewalletType : "commission",
                date        : convertTolocal(row.date),
                dateAdded   : convertTolocal(row.dateAdded),
                amountType  : row.amountType,
                balance     : row.balance,
                type        : row.type
            }));
            const response = {
                totalCount: count,
                totalPages: Math.ceil(count / pageSize),
                currentPage: page,
                data,
            };

            return await successMessage({ data: response });
        } catch (error) {
            logger.error("ERROR FROM getPurchaseWallet",error);
            return next(error);
        }
    }

    async getMyEarnings(req, res, next, whereClause, direction, pageSize, offset) {
        try {
            const { count, rows } = await LegAmount.findAndCountAll({
                attributes: ["fromId","amountType", "totalAmount", "amountPayable", "tds", "serviceCharge", "createdAt"],
                where: whereClause,
                include:[{
                    model:User,
                    attributes:["username"]
                }],
                order: [["createdAt", direction]],
                limit: pageSize,
                offset: offset,
            });
            const data = rows.map(row => ({
                fromId        : row.fromId,
                fromUser      : row.User?.username,
                amountType    : convertSnakeCaseToTitleCase(row.amountType),
                ewalletType   : "commission",
                totalAmount   : row.totalAmount,
                amountPayable : row.amountPayable,
                tds           : row.tds ? row.tds.toString() : "0.00",
                serviceCharge : row.serviceCharge ? row.serviceCharge.toString() : "0.00",
                dateAdded     : convertTolocal(row.createdAt),
            }))
            logger.info("data",data)
            return {count,data};
        } catch (error) {
            logger.error("ERROR FROM getMyEarnings",error);
            return next(error);
        }
    }

    async getAmountTypes(commissionType) {
        
        const compensation = await Compensation.findOne({});
        const moduleStatus = await getModuleStatus({attributes:["mlmPlan"]});

        // gets the list of permitted commission types
        let commissionLookup=[];
        if (compensation.sponsorCommission) commissionLookup.push("level_commission");
        if (compensation.referralCommission) commissionLookup.push("referral");
        if (compensation.planCommission && moduleStatus.mlmPlan === "Binary") commissionLookup.push("leg");
        if (compensation.rankCommission) commissionLookup.push("rank_bonus");
        if (compensation.matchingBonus) commissionLookup.push("matching_bonus");
        if (compensation.poolBonus) commissionLookup.push("pool_bonus");
        if (compensation.fastStartBonus) commissionLookup.push("fast_start_bonus");
        if (compensation.performanceBonus) commissionLookup.push("vacation_fund", "car_fund", "house_fund", "education_fund");

        if (!commissionType) return commissionLookup;
        if (commissionType.length==0) commissionType = commissionLookup;

        const commissionMapping = {
            level_commission: ["level_commission", "repurchase_level_commission", "upgrade_level_commission", "xup_repurchase_level_commission", "xup_upgrade_level_commission"],
            referral: ["referral"],
            rank_bonus: ["rank_bonus"],
            leg: ["leg", "repurchase_leg", "upgrade_leg"],
            matching_bonus: ["matching_bonus", "matching_bonus_purchase", "matching_bonus_upgrade"],
            pool_bonus: ["pool_bonus"],
            fast_start_bonus: ["fast_start_bonus"],
            vacation_fund: ["vacation_fund"],
            education_fund: ["education_fund"],
            car_fund: ["car_fund"],
            house_fund: ["house_fund"]
        };
        // adds to amountTypes the relevant values from commissionMapping based on commissionType
        const amountTypes = commissionType.reduce((acc, commission) => {
            acc.push(...(commissionMapping[commission] || []));
            return acc;
        }, []);
    
        return amountTypes;
    }

    async addToFundTransferHistory({ from, to, amount, amountType, transFee, notes, transactionId, transaction }) {
        const options = transaction ? { transaction } : {};
        return await FundTransferDetail.create({
                                    fromId  : from,
                                    toId    : to,
                                    amount  : parseFloat(amount),
                                    amountType,
                                    transFee,
                                    notes,
                                    transactionId
                                }, options);
       
    }

    async getUserBalance(userId) {
        const userData = await UserBalanceAmount.findOne({
            where: { userId },
            attributes: ["id", "balanceAmount", "purchaseWallet"],
        });
        return {
            "ewalletBalance"        : userData.balanceAmount,
            "purchaseWalletBalance" : userData.purchaseWallet
        };
    }
    
    async reduceUserbalance({ user: userBalance, deduction: totalAmount, type, transaction }) {
        if (parseFloat(userBalance[type]) < parseFloat(totalAmount)) {
            return false;
        }
        return await userBalance.decrement([type], {by: parseFloat(totalAmount), transaction});
    }

    async addUserBalance({ user: userBalance, addition: totalAmount, type, transaction }) {
        return await userBalance.increment([type], {by: parseFloat(totalAmount), transaction});
    }
    
    async addToEwalletTransferHistory({ userId, fundTransferId, amount, balance, amountType, type, transactionFee, transaction, transactionId }){
        const options = transaction ? { transaction } : {};
        return await EwalletTransferHistory.create({
            userId,
            fundTransferId,
            amount,
            balance,
            dateAdded : new Date(),
            amountType,
            type,
            transactionFee,
            transactionId
        }, options);
    }
    async addToTransaction({ transactionId, transaction}) {
        const options = transaction ? { transaction } : {};
        return await Transaction.create({ transactionId}, options);
    }
    async addToEwalletPurchaseHistory({ userId, referenceId,ewalletType, amount, balance, amountType, type, transactionFee, transaction }){
        const options = transaction ? { transaction } : {};
        return await EwalletPurchaseHistory.create({
            userId,
            referenceId,
            ewalletType,
            amount,
            balance,
            amountType,
            type,
            transactionFee,
            dateAdded : new Date(),
        }, options);
    }

    async getEwalletTilePercentages(tileAmounts,tileAmountsOld) {
        const {
            ewalletCommission,
            ewalletTransferCredit,
            ewalletPurchaseCredit,
            ewalletTransferDebit,
            ewalletTransferDebitFee,
            ewalletPurchaseDebit,
            purchaseWallet,
        } = tileAmounts;

        const {
            ewalletCommission       : ewalletCommissionOld,
            ewalletTransferCredit   : ewalletTransferCreditOld,
            ewalletPurchaseCredit   : ewalletPurchaseCreditOld,
            ewalletTransferDebit    : ewalletTransferDebitOld,
            ewalletTransferDebitFee : ewalletTransferDebitFeeOld,
            ewalletPurchaseDebit    : ewalletPurchaseDebitOld,
            purchaseWallet          : purchaseWalletOld,
        } = tileAmountsOld;

        // calculate commission
        const commissionPercentage =
            ewalletCommissionOld === 0
                ? ewalletCommission - ewalletCommissionOld
                : ((ewalletCommission - ewalletCommissionOld) / ewalletCommissionOld) * 100;
        
        // calculate total credit
        const totalCredit           = ewalletCommission + ewalletTransferCredit + ewalletPurchaseCredit // + purchaseWallet; -> included in ewalletCommission
        const totalCreditOld        = ewalletCommissionOld + ewalletTransferCreditOld + ewalletPurchaseCreditOld // + purchaseWalletOld;
        const totalCreditPercentage =
            totalCreditOld === 0
                ? totalCredit - totalCreditOld
                : ((totalCredit - totalCreditOld) / totalCreditOld) * 100;

        // calculate total debit
        const totalDebit           = ewalletTransferDebit + ewalletPurchaseDebit + ewalletTransferDebitFee;
        const totalDebitOld        = ewalletTransferDebitOld + ewalletPurchaseDebitOld + ewalletTransferDebitFeeOld;
        const totalDebitPercentage = totalDebitOld === 0 ? totalDebit - totalDebitOld : ((totalDebit - totalDebitOld) / totalDebitOld) * 100;

        const commissionSign = ewalletCommission - ewalletCommissionOld > 0 ? "up" : "down";
        const creditSign     = totalCredit - totalCreditOld > 0 ? "up" : "down";
        const debitSign      = totalDebit - totalDebitOld > 0 ? "up" : "down";

        return {
            totalCredit           : totalCredit,
            totalDebit            : totalDebit,
            commissionPercentage  : commissionPercentage,
            commissionSign,
            totalCreditPercentage : totalCreditPercentage,
            creditSign,
            totalDebitPercentage  : totalDebitPercentage,
            debitSign,
        };
    }
}
export default new EwalletService();
