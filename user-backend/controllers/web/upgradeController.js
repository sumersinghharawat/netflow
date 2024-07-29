import { Op } from "sequelize";
import { consoleLog, logger, errorMessage, successMessage } from "../../helper/index.js";
import { getConfiguration, getModuleStatus, verifyTransactionPassword } from "../../utils/index.js";

import CommissionService from "../../services/commissionService.js";
import UpgradeService from "../../services/upgradeService.js";
import utilityService from "../../services/utilityService.js";

import { Compensation, Configuration, Package, PackageUpgradeHistory, PaymentGatewayConfig, User, LevelCommissionRegisterPacks, Rank } from "../../models/association.js";
import { sequelize } from "../../config/db.js";
import BinaryBonus from "../../models/binaryBonus.js";
import RankConfiguration from "../../models/rankConfiguration.js";
import _ from "lodash";


export const upgradeUserPackage = async (req,res,next) => {
    try {
        const userId              = req.auth.user.id;
		const ip                  = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const transactionPassword = req.body.transactionPassword || null;
        const paymentMethod       = req.body.paymentMethod;
        const upgradeData         = req.body;
        const oldProductId        = upgradeData["oldProductId"];
        const upgradeProductId    = upgradeData["upgradeProductId"];
        const moduleStatus        = await getModuleStatus({attributes:["packageUpgrade","productStatus"]});

        if (!moduleStatus.packageUpgrade || !moduleStatus.productStatus) {
            let response = await errorMessage({ code: 1067 });
            return res.status(422).json(response);
        }

        const checkPendingUpgrade = await PackageUpgradeHistory.findOne({
            where: { userId:userId, status: 0 }
        })
        if (checkPendingUpgrade) {
            let response = await errorMessage({ code: 1122 });
            return res.status(422).json(response);
        }
        const checkPaymentMethod = await PaymentGatewayConfig.findOne({
            attributes: ["id", "name", "slug"],
            where: { id: paymentMethod, status: 1, upgradation: 1 },
            raw: true
        });
        if (!checkPaymentMethod) {
            const response = await errorMessage({ code: 1036, statusCode: 422 });
            return res.status(response.code).json(response.data);
        };
        if (checkPaymentMethod.slug === 'e-wallet' && transactionPassword) {
            const checkPassword = await verifyTransactionPassword(req, res, next);
            if (!checkPassword) {
                const response = await errorMessage({ code: 1015, statusCode: 422 });
                return res.status(response.code).json(response.data);
            };
        };
        const productData = await Package.findAll({
            where: { id: { [Op.in]: [oldProductId, upgradeProductId] } },
            raw: true
        });
        // calculate amount to be paid, pv difference
        const oldProduct     = productData.find((product) => product.id === oldProductId);
        const upgradeProduct = productData.find((product) => product.id === upgradeProductId);

        const paymentAmount  = upgradeProduct.price - oldProduct.price;
        const pvAmount       = upgradeProduct.pairValue - oldProduct.pairValue;
        // const paymentAmount  = upgradeProduct.price;
        // const pvAmount       = upgradeProduct.pairValue;

        let transaction = await sequelize.transaction();
        let status;
        try {
            status = await UpgradeService.upgradePayment(req,res,next,transaction,productData,checkPaymentMethod,upgradeData,paymentAmount,pvAmount);
            await transaction.commit();
        } catch (error) {
            logger.debug("transaction rollback")
            await transaction.rollback();
            throw error;
        }
        
        if (status) {
            // insert user activity
            const activityData = {
                amount: paymentAmount,
                pair_value: pvAmount,
                product_id: upgradeProductId
            };
            await utilityService.insertUserActivity({
                userId: userId,
                userType: "user",
                data: JSON.stringify(activityData),
                description: "Package upgraded",
                ip: ip,
                activity: "Package upgrade",
                // transaction: None
            });
            const commData = {
                userId: userId,
                productId: upgradeProductId,
                productPv: pvAmount,
                productAmount: paymentAmount,
                // orderId: orderId,
                // ocOrderId: null,
                sponsorId: null,
                uplineId: null,
                position: null,
            };
            const prefix = req.prefix;
            await CommissionService.commissionCall(prefix,userId, commData, "upgrade");

        }
        const response = await successMessage({data:"Package upgraded successfully."});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM upgradeUserPackage",error);
        return next(error);
    }

};

export const getUpgradePackage = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const moduleStatus = await getModuleStatus({attributes:["packageUpgrade","productStatus", "mlmPlan"]});
        
        if (!moduleStatus.packageUpgrade || !moduleStatus.productStatus) {
            const response = await errorMessage({ code: 1057, statusCode: 422 });
			return res.status(response.code).json(response.data);
        }

        const currentPack = await User.findOne({
            attributes: ["productId"],
            include: [{model:Package, attributes:["id","pairValue"]}],
            where: { id: userId },
            // raw:true
        });
        const upgradePackages = await Package.findAll({
            where: { type: "registration", active: 1 },
            order: [["pair_value","ASC"]],
            include: [
                {
                    model: LevelCommissionRegisterPacks,
                    attributes: ["level", "commission",]
                },
                {
                    model: Rank,
                    attributes: ["name", "color", "image", "status", "commission"],
                    where: { status: 1 },
                    required: false
                }
            ],
        });

        const compensations = await Compensation.findOne();
        const Configuration = await getConfiguration();
        let binaryType, rankConfig;
        if (parseInt(compensations.rankCommission)) {
            rankConfig    = await RankConfiguration.findOne({where:{'slug': 'joiner-package'}});
        }
        if (moduleStatus.mlmPlan==="Binary") {
            binaryType    = await BinaryBonus.findOne();
        }
        // true/false value depending on satisfying the conditions
        let commissions     = {
            sponsorCommission: parseInt(compensations.sponsorCommission) && Configuration.commissionCriteria === 'member_pck',
            rankCommission: (parseInt(compensations.rankCommission) && parseInt(rankConfig.status)) ? true : false,
            planCommission: parseInt(compensations.planCommission) && moduleStatus.mlmPlan === 'Binary',
            referralCommission: parseInt(compensations.referralCommission) && Configuration.sponsorCommissionType === 'sponsor_package'
        };
        // if(parseInt(compensations.planCommission) && moduleStatus.mlmPlan === 'Binary') {
        //     commissions.planCommission = true;
        // }
        // // Level commission
        // if(parseInt(compensations.sponsorCommission) && Configuration.commissionCriteria === 'member_pck') {
        //     commissions.sponsorCommission = true;
        // }
        // if(parseInt(compensations.rankCommission) && parseInt(rankConfig.status)) {
        //     commissions.rankCommission = true;
        // }
        // if(parseInt(compensations.referralCommission) && Configuration.sponsorCommissionType === 'sponsor_package') {
        //     commissions.referralCommission = true;
        // }
        const result = upgradePackages.map((pack) => ({
            pack,
            currentPack: pack.id === currentPack.productId,
            upgradable: pack.id==currentPack.productId 
                ? 1 
                : (pack.pairValue <= currentPack.Package.pairValue ? 0 : 2),
            referralCommission: {
                status : commissions.referralCommission,
                type : Configuration.referralCommissionType,
                value : pack.referralCommission
            },
            levelCommission: {
                status : commissions.sponsorCommission,
                type : Configuration.levelCommissionType,
                value : pack.LevelCommissionRegisterPacks
            },
            binaryCommission:{
                status : commissions.planCommission,
                type : binaryType?.commissionType ?? false,
                value : pack.pairPrice
            },
            rankCommission: {
                status : commissions.rankCommission && !_.isEmpty(pack.Rank) && pack.Rank.status==1,
                type : 'flat',
                value : pack.Rank
            }
        }));
        const response = await successMessage({ data: result })
        return res.status(response.code).json(response.data)
    } catch (error) {
        logger.error("ERROR FROM getUpgradePackage",error);
        return next(error);
    }
};






