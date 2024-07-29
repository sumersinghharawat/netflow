import { convertTolocal, consoleLog, logger, formatLargeNumber, successMessage, convertSnakeCaseToTitleCase, errorMessage } from "../../helper/index.js";
import { getModuleStatus } from "../../utils/index.js";

import HomeService from "../../services/homeService.js";
import EwalletService from "../../services/ewalletService.js";
import utilityService from "../../services/utilityService.js";

import { Notification, SubscriptionConfig } from "../../models/association.js";

export const getDashboardUserProfile = async (req, res, next) => {
    try {
        const userId                   = req.auth.user.id;
        const user                     = req.auth.user.username;
        const moduleStatus             = await getModuleStatus({attributes:["ecomStatus","productStatus","subscriptionStatus"]});
        const [ dashboardDetails, payoutOverview, { replica, leadCapture }, storeLinks ] = await Promise.all([
            HomeService.getDashboardUserDetails(userId, moduleStatus),
            EwalletService.getPayoutOverview(req, res, next),
            HomeService.getReplicaAndLeadCaptureLinks(user),
            HomeService.getUpgradeRenewLinks(req)
        ]);
        // const dashboardDetails         = await HomeService.getDashboardUserDetails(userId, moduleStatus);
        // const payoutOverview           = await EwalletService.getPayoutOverview(req, res, next);
        // const { replica, leadCapture } = await HomeService.getReplicaAndLeadCaptureLinks(user);
        // const storeLinks               = await HomeService.getUpgradeRenewLinks(req);
        // const newMembers               = dashboardDetails.downline ? dashboardDetails.downline.slice(0, 4) : [];
        
        // calculate payout doughnut
        Object.entries(payoutOverview).forEach(([key, value]) => {
            payoutOverview[key] = value;
        });
        let pending = 0, approved = 0, paid = 0;
        const total = Number(payoutOverview.payoutRequested) + Number(payoutOverview.payoutApproved) + Number(payoutOverview.payoutPaid);
        if (total>0) {
            pending = (Number(payoutOverview.payoutRequested) * 100)/total;
            approved = (Number(payoutOverview.payoutApproved) * 100)/total;
            paid = (Number(payoutOverview.payoutApproved) * 100)/total;
        }
        const payoutDoughnut = { pending: pending, approved: approved };
        
        // get packageName, package validity percentage based on productStatus and ecomStatus
        let packageName     = null;
        let packageValidity = null;
        if (moduleStatus.productStatus) {
            packageName = moduleStatus.ecomStatus
                ? dashboardDetails.OcProduct.model
                : dashboardDetails.Package.name;

            const productValidityPeriod = moduleStatus.ecomStatus
                ? dashboardDetails.OcProduct.subscriptionPeriod
                : dashboardDetails.Package.validity;
            if(moduleStatus.subscriptionStatus) {
                packageValidity = await HomeService.formatProductValidity(productValidityPeriod, dashboardDetails.productValidity);
            }
        } else {
            if(moduleStatus.subscriptionStatus) {
                let { subscriptionPeriod } = await SubscriptionConfig.findOne({attributes:["subscriptionPeriod"]});
                packageValidity = await HomeService.formatProductValidity(subscriptionPeriod, dashboardDetails.productValidity);
            }
        }

        let data = {
            userProfile: {
                username       : (dashboardDetails.username).toUpperCase(),
                fullname       : dashboardDetails.UserDetail.name + " " + dashboardDetails.UserDetail.secondName,
                sponsorName    : (dashboardDetails.sponsor.username).toUpperCase(),
                packageName    : packageName,
                rankName       : dashboardDetails.Rank ? dashboardDetails.Rank.name : "",
                image          : dashboardDetails.UserDetail ? dashboardDetails.UserDetail.image : null,
                personalPv     : dashboardDetails.personalPv ?? 0,
                groupPv        : dashboardDetails.groupPv ?? 0,
                upgradeLink    : storeLinks.upgradeLink?? null,
                renewLink      : storeLinks.renewLink?? null,
                kycStatus      : dashboardDetails.UserDetail ? dashboardDetails.UserDetail.kycStatus : null,
                productValidity: packageValidity,
            },
            replicaLink: replica,
            leadCaptureLink: leadCapture,
            payoutOverview: payoutOverview,
            payoutDoughnut: payoutDoughnut,
        };
        const response =  await successMessage({ data });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getDashboardUserProfile",error);
        return next(error);
    }
};

export const getDashboardTiles = async (req, res, next) => {
    try {
        const userId          = req.auth.user.id;
        // tiles at the top
        const creditAndDebit  = await EwalletService.getUserBalanceAndEwallet(userId);
        const ewallet         = creditAndDebit.userBalance;
        const commission      = creditAndDebit.ewalletCommission;
        const totalCredit     = creditAndDebit.ewalletCommission + creditAndDebit.ewalletTransferCredit + creditAndDebit.ewalletPurchaseCredit; // + creditAndDebit.purchaseWallet; -> included in ewalletCommission
        const totalDebit      = creditAndDebit.ewalletTransferDebit + creditAndDebit.ewalletPurchaseDebit;

        const tiles = {
            ewallet,
            commission,
            commissionPercentage: 0,
            commissionSign: "up",
            totalCredit,
            totalCreditPercentage: 0,
            creditSign: "up",
            totalDebit,
            totalDebitPercentage: 0,
            debitSign: "up"

        };

        const response =  await successMessage({ data: tiles });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getDashboardTiles",error);
        return next(error);
    }
};

export const getDashboardDetails = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const topEarners = await HomeService.getTopEarners(req, res, next);
        const [newMembers, earnings,ranks,currentRank] = await Promise.all([
            HomeService.getNewMembers(userId),
            HomeService.getEarnings(userId),
            HomeService.getRanks(),
            HomeService.getCurrentRank(userId)
        ])
        const data = {
            newMembers,
            topEarners,
            earnings,
            ranks,
            currentRank
        }
        const response =  await successMessage({ data: data });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getDashboardDetails",error);
        return next(error);
    }
};

export const getTopRecruiters = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const topRecruiters = await HomeService.getTopRecruiters(req, res, next);

        const response =  await successMessage({ data: topRecruiters });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getTopRecruiters",error);
        return next(error);
    }
};

export const getPackageOverview = async (req,res,next) => {
    try {
        const userId       = req.auth.user.id;
        const moduleStatus = await getModuleStatus({attributes:["productStatus","ecomStatus"]});

        if (!moduleStatus.ecomStatus && !moduleStatus.productStatus) {
            const response = await errorMessage({code: 1092, statusCode:422});
            return res.status(response.code).json(response.data);
        }

        const packageOverview = await HomeService.getPackageOverview(userId,moduleStatus)

        const response =  await successMessage({ data: packageOverview });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getPackageOverview",error);
        return next(error);
    }
};

export const getRankOverview = async (req,res,next) => {
    try {
        const userId       = req.auth.user.id;
        const moduleStatus = await getModuleStatus({attributes:["rankStatus"]});

        if (!moduleStatus.rankStatus) {
            const response = await errorMessage({code: 1020, statusCode:422});
            return res.status(response.code).json(response.data);
        }

        const rankOverview = await HomeService.getRankOverview(userId,moduleStatus)

        const response =  await successMessage({ data: rankOverview });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getRankOverview",error);
        return next(error);
    }
};

export const getDashboardExpenses = async (req,res,next) => {
    try {
        const userId       = req.auth.user.id;
        const expensesData = await HomeService.getExpenses(userId);

        // sum over the entries. include 'payout_request' entries only if PayoutReleaseRequest.status=0
        // this is to avoid rejected payout entries.
        let expensesDict = expensesData.reduce((acc, expense) => {
            const { amountType, amount, PayoutReleaseRequest } = expense;
            if (!PayoutReleaseRequest || PayoutReleaseRequest.status === 0) {
                acc[amountType] = (acc[amountType] || 0) + parseFloat(amount);
            }
            return acc;
        }, {});
        let expenses = Object.entries(expensesDict).map(([amountType, amount]) => ({
            amountType: amountType,
            amount: amount
        }));


        const response =  await successMessage({ data: expenses });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getDashboardExpenses",error);
        return next(error);
    }
};


export const getTiles = async (req, res, next) => {
    try {
        const timeFrame         = req.query.timeFrame || null;
        const currentDate       = new Date();
        const tileAmounts       = await EwalletService.getUserBalanceAndEwallet(userId, currentDate, timeFrame);
        const fromDate          = await utilityService.subtractDates(currentDate, timeFrame);
        const tileAmountsOld    = await EwalletService.getUserBalanceAndEwallet(userId, fromDate, timeFrame);

        // all the maths 
        let tilePercentages = await EwalletService.getEwalletTilePercentages(tileAmounts,tileAmountsOld);
        
        const data = {
            ewallet              : tileAmounts.userBalance,
            commissions          : tileAmounts.ewalletCommission,
            commissionPercentage : tilePercentages.commissionPercentage,
            commissionSign       : tilePercentages.commissionSign,
            totalCredit          : tilePercentages.totalCredit,
            totalCreditPercentage: tilePercentages.totalCreditPercentage,
            creditSign           : tilePercentages.creditSign,
            totalDebit           : tilePercentages.totalDebit,
            totalDebitPercentage : tilePercentages.totalDebitPercentage,
            debitSign            : tilePercentages.debitSign

        };
        const response =  await successMessage({ data: data });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getTiles",error);
        return next(error);
    }
};

export const getGraph = async(req,res,next) => {
    try {
        const timeFrame     = req.query.timeFrame || null;
        const graph         = await HomeService.getJoiningsGraph(req,res,next,timeFrame);
        const response =  await successMessage({ data: graph });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getGraph",error);
        return next(error);
    }
};

export const getNotifications = async (req, res, next) => {
    try {
        const page      = parseInt(req.query.page) || 1;
        const pageSize  = parseInt(req.query.perPage) || 10;
        const offset    = (page - 1) * pageSize;
        const userId    = req.auth.user.id;
        let data        = [];
        const notifications = await Notification.findAndCountAll({
            where:{
                notifiableId: userId,
            },
            order: [["createdAt", "DESC"]],
            offset: offset,
            limit: pageSize
        });

        if (notifications.count) {
            notifications.rows.forEach(notif => {
                const notifData = JSON.parse(notif.data);
                // get only class value of icon string
                const iconMatch = notifData.icon.match(/class="([^"]*)"/);
                const icon = iconMatch ? iconMatch[1] : "";

                let title = convertSnakeCaseToTitleCase(notifData.type);
                let image = title.charAt(0).toUpperCase();
                if (notifData.url) {
                    // Remove the 'url' property if it exists
                    delete notifData.url;
                }
                data.push({
                    ...notifData,
                    icon,
                    title,
                    image,
                    date: convertTolocal(notif.createdAt)
                });
            });
        }

        const totalCount = notifications.count;
            const totalPages = Math.ceil(totalCount / pageSize);
            const currentPage = Math.floor(offset / pageSize) + 1;
            const out = {
                totalCount,
                totalPages,
                currentPage,
                data,
        };
        const response =  await successMessage({ data: out });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getNotifications",error);
        return next(error);
    }
};

export const readNotifications = async (req,res,next) => {
    try {
        const currentDate = new Date();
        await Notification.update({ readAt: currentDate }, { where: { notifiableId: req.auth.user.id, readAt: null } })
        
        const response =  await successMessage({ data: "Marked as read." });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM readNotifications",error);
        return next(error);
    }
}

