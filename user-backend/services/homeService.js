import { Op, Sequelize } from "sequelize";
import { consoleLog, convertTolocal, logger, formatLargeNumber, successMessage, convertToUTC } from "../helper/index.js";
import { getModuleStatus } from "../utils/index.js";
import utilityService from "./utilityService.js";
import { AggregateUserCommissionAndIncome, AmountPaid, Configuration, EwalletPurchaseHistory, LegAmount, OcProduct, Package, PayoutReleaseRequest, Rank, SponsorTreepath, User, UserBalanceAmount, UserDetail } from "../models/association.js";
import { makeHash } from "../helper/utility.js";
import { sequelize } from "../config/db.js";
import _ from "lodash";

class HomeService {

    async getDashboardUserDetails(userId, moduleStatus) {
        try {
            // default clause if productStatus = 0
            // let productClause = {};
            let productClause = { model: User, as:"father", attributes: [], required: false };
            if (moduleStatus.productStatus) {
                productClause = moduleStatus.ecomStatus
                    ? { model: OcProduct, attributes: ["productId", "model", "subscriptionPeriod"], required: false }
                    : { model: Package, attributes: ["id", "name", "validity"], required: false };
            }

            const dashboardDetails = await User.findOne({
                attributes: ["id", "username","personalPv", "groupPv", "productId", "productValidity"],
                where: { id: userId },
                include: [
                    { model: User, as: "sponsor", attributes: ["username"] },
                    { model: UserDetail, attributes: ["name","secondName","image","kycStatus"] },
                    {
                        model: Rank,
                        as: "Rank",
                        attributes: ["name"],
                        required: false
                    }, 
                    productClause,
                ],
            });
            return dashboardDetails;

        } catch (error) {
            logger.error("ERROR FROM getDashboardUserDetails");
            throw error;
        }
    }

    async getTopEarners(req, res, next) {
        try {
            const userId = req.auth.user.id;
            const prefix = req.prefix;
            const query  = `SELECT users.id, users.username, aggregate.amount_payable as balanceAmount,
                                CONCAT(details.name, ' ', details.second_name) AS name, details.gender, details.image
                            FROM ${prefix}_users users
                            JOIN ${prefix}_treepaths AS tp ON tp.descendant = users.id
                            LEFT JOIN ${prefix}_aggregate_user_commission_and_incomes AS aggregate ON aggregate.user_id = users.id
                            JOIN ${prefix}_user_details AS details ON details.user_id = users.id
                            WHERE tp.ancestor = :userId AND tp.ancestor != tp.descendant
                            ORDER BY balanceAmount DESC LIMIT 4`;
            const topEarnerData = await sequelize.query(query,{
                                    replacements: {
                                        userId
                                    },
                                    type: sequelize.QueryTypes.SELECT
                                });
            return topEarnerData;
        } catch (error) {
            logger.error("ERROR FROM getTopEarners",error);
            return next(error);
        }
    }

    async getTopRecruiters(req, res, next) {
        try {
            const userId = req.auth.user.id;
            const prefix = req.prefix;
            const query =`WITH SponsorDescendantCounts AS (
                SELECT
                    COUNT(t.descendant) AS count,
                    ANY_VALUE(t.descendant) AS descendant_id,
                    ANY_VALUE(u.username) AS username,
                ANY_VALUE(u.id) AS id
                FROM
                    ${prefix}_treepaths AS t
                JOIN
                    ${prefix}_sponsor_treepaths AS st ON st.ancestor = t.descendant AND st.depth = 1
                JOIN
                    ${prefix}_users AS u ON u.id = st.ancestor
                WHERE
                    t.ancestor = ${userId} AND t.descendant != ${userId}
                GROUP BY
                    st.ancestor
            )
            SELECT
                count,
                descendant_id AS id,
                username,
                name,
                second_name AS secondName,
                gender,
                image
            FROM
                SponsorDescendantCounts
            JOIN ${prefix}_user_details ON ${prefix}_user_details.user_id = SponsorDescendantCounts.id
            ORDER BY
                count DESC
            LIMIT 5;`;

            const topRecruiterData = await sequelize.query(query, {
                type: sequelize.QueryTypes.SELECT,
            })
            return topRecruiterData;
        } catch (error) {
            logger.error("ERROR FROM getTopRecruiters");
            throw error;
        }
    }

    async getPackageOverview(userId,moduleStatus) {
        try {
            let packageOverviewData, packageOverview;
           
            if (moduleStatus.ecomStatus) {
                packageOverviewData = await OcProduct.findAll({
                    attributes: [
                        ["product_id","id"], ["model","name"], "image",
                        // coalesce returns count of users if available. if null, it returns 0
                        [
                            Sequelize.fn("COALESCE", Sequelize.fn("COUNT", Sequelize.col("Users.id")), 0),
                            "count"
                        ]
                    ],
                    where: { packageType: "registration" },
                    include: [
                        {
                            model: User,
                            attributes: [],
                            where: { sponsorId: userId },
                            required: false // LEFT JOIN
                        }
                    ],
                    group: ["OcProduct.product_id"],
                    raw: true
                });
                // logger.debug("packageOverviewData",packageOverviewData)

            } else {
                packageOverviewData = await Package.findAll({
                    attributes: [
                        "id", "name", "image",
                        // coalesce returns count of users if available. if null, it returns 0
                        [Sequelize.fn("COALESCE", Sequelize.fn("COUNT", Sequelize.col("Users.id")), 0),"count"]
                    ],
                    where: { type: "registration" },
                    include: [
                        {
                            model: User,
                            attributes: [],
                            where: { sponsorId: userId },
                            required: false // LEFT JOIN
                        }
                    ],
                    group: ["Package.id"],
                    raw: true
                });
                // packageOverview = packageOverviewData;
            }
            
            return packageOverviewData;
        } catch (error) {
            logger.error("ERROR FROM getPackageOverview");
            throw error;
        }
    }

    async getRankOverview(userId,moduleStatus) {
        try {

            const rankOverviewData = await Rank.findAll({
                attributes: [ "id", "name", "image",
                [Sequelize.fn("COALESCE", Sequelize.fn("COUNT", Sequelize.col("Users.id")), 0),"count"]
                ],
                include: [
                    {
                        model: User,
                        attributes: [],
                        where: { sponsorId: userId },
                        required: false // LEFT JOIN
                    }
                ],
                group: ["Rank.id"],
                raw: true
            });
            // logger.debug("rankOverviewData",rankOverviewData)

            return rankOverviewData;
        } catch (error) {
            logger.error("ERROR FROM getRankOverview",error);
            return next(error);
        }
    }

    async getRanks() {
        try {
            return await Rank.findAll({
                attributes: ["id","name","image"],
            });
        } catch (error) {
            logger.error("ERROR FROM getRank",error);
            return next(error);
        }
    }

    async getCurrentRank(userId) {
        try {
            const { userRankId: userRank } = await User.findOne({
                attributes: ["userRankId"],
                where: { id: userId }
            });
            return userRank;
        } catch (error) {
            logger.error("ERROR FROM getRank",error);
            return next(error);
        }
    }

    async getNewMembers(userId){

        const newMemberData = await User.findAll({
            attributes: ["id", "username", "dateOfJoining"],
            where: { sponsorId: userId },
            include: [{ model: UserDetail, attributes: ["name", "secondName", "gender", "image"] }],
            order: [["dateOfJoining","DESC"]],
            limit: 5,
            raw: true
        });
        const newMembers = newMemberData.map((member) => ({
            id            : member["id"],
            username      : member["username"],
            dateOfJoining : convertTolocal(member.dateOfJoining),
            name          : member["UserDetail.name"],
            secondName    : member["UserDetail.secondName"],
            gender        : member["UserDetail.gender"],
            image         : member["UserDetail.image"],
        }));
        return newMembers;
    }

    async getEarnings(userId) {
        const earningsData = await AggregateUserCommissionAndIncome.findAll({
            attributes: ["amountType", "amountPayable"],
            where: { userId: userId },
            raw: true,
        });

        let earnings = earningsData.map(entry => ({
            amountType: entry.amountType,
            amount: entry.amountPayable
        }));

        return earnings;

    }

    async getExpenses(userId) {
        try {
            const expensesData = await EwalletPurchaseHistory.findAll({
                attributes: [
                    "id",
                    ["amount_type", "amountType"],
                    "amount",
                ],
                include: [
                    {
                        model: PayoutReleaseRequest,
                        attributes: ["id", "amount", "status"],
                        required: false,
                        where: Sequelize.literal("`EwalletPurchaseHistory`.`amount_type` = \'payout_request\'"),
                    },
                ],
                where: {
                    userId: userId,
                },
            });
            return expensesData;
        } catch (error) {
            logger.error("ERROR FROM getExpenses");
            throw error;
        }
    }

    async getReplicaAndLeadCaptureLinks(user) {
        try {
            let replicaLink, leadCaptureLink;
            // let { replicaHashKey } = await Configuration.findOne({ attributes: ["replicaHashKey"] });
            let replicaHashKey = process.env.HASH_KEY
            if (process.env.DEMO_STATUS == "yes") {
                let { username : admin } = await User.findOne({ attributes: ["username"], where: { userType: "admin" } });
                const hash       = await makeHash({ param: user, hashKey: replicaHashKey});
                replicaLink = `${process.env.SITE_URL}/replica/${user}/${admin}/${hash}`;
                leadCaptureLink = `${process.env.SITE_URL}/lcp/${user}/${admin}/${hash}`;
            } else {
                const hash       = await makeHash({ param: user, hashKey: replicaHashKey});
                replicaLink = `${process.env.SITE_URL}/replica/${user}/${hash}`;
                leadCaptureLink = `${process.env.SITE_URL}/lcp/${user}/${hash}`;
            }
            let replica = [
                {
                    name: "Replica Link",
                    icon: "copy.svg",
                    link: replicaLink,
                },
                {
                    name: "Facebook",
                    icon: "facebook.svg",
                    link: `https://www.facebook.com/sharer/sharer.php?u=${replicaLink}`,
                },
                {   
                    name: "Twitter",
                    icon: "twitter.svg",
                    link: `https://twitter.com/share?url=${replicaLink}`,
                },
                {
                    name: "LinkedIn",
                    icon: "linkedin.svg",
                    link: `http://www.linkedin.com/shareArticle?url=${replicaLink}`,
                },
            ];
            let leadCapture = [
                {
                    name: "Lead Capture Link",
                    icon: "copy.svg",
                    link: leadCaptureLink,
                },
                {
                    name: "Facebook",
                    icon: "facebook.svg",
                    link: `https://www.facebook.com/sharer/sharer.php?u=${leadCaptureLink}`,
                },
                {
                    name: "Twitter",
                    icon: "twitter.svg",
                    link: `https://twitter.com/share?url=${leadCaptureLink}`,
                },
                {
                    name: "LinkedIn",
                    icon: "linkedin.svg",
                    link: `http://www.linkedin.com/shareArticle?url=${leadCaptureLink}`,
                },
            ];
            return {replica, leadCapture};
        } catch (error) {
            logger.error("ERROR FROM getReplicaAndLeadCaptureLinks");
            throw error;
        }
    }

    async getJoiningsGraph(req, res, next, timeFrame=null) {
        try {
            const userId      = req.auth.user.id;
            const currentDate = new Date();
            const toDate      = new Date();
            const prefix      = req.prefix;  
            let {fromDate, dateFormat} = await utilityService.getGraphDate(timeFrame);
            let defaultData = await utilityService.getGraphDict(toDate, timeFrame);
            // get downlines
            let query    = `SELECT DATE_FORMAT(${prefix}_users.date_of_joining, '${dateFormat}') AS month,COALESCE(COUNT(*), 0) AS user_count
                            FROM ${prefix}_users
                            JOIN ${prefix}_treepaths AS downlines ON downlines.descendant = ${prefix}_users.id 
                            AND downlines.ancestor = :userId
                            WHERE ${prefix}_users.date_of_joining >= :fromDate AND ${prefix}_users.date_of_joining <= :currentDate
                            AND downlines.descendant != downlines.ancestor
                            GROUP BY month
                            ORDER BY month`; 
            const downlines = await sequelize.query(query, {
                type: sequelize.QueryTypes.SELECT,
                replacements: {
                    currentDate: currentDate,
                    fromDate: fromDate,
                    userId
                  },
            });
            let graphData = downlines.map( item => {
                return {[item.month]: item.user_count}
            });
            graphData = _.merge({}, ...graphData);
            for(let item in defaultData.graphData){
                if(downlines.some( key => key.month === item)){
                    defaultData.graphData[item] = graphData[item]
                }
            }
            return defaultData.graphData;
        } catch (error) {
            logger.error("ERROR FROM getJoiningsGraph",error);
            return next(error);
        }
    }

    async getUpgradeRenewLinks(req) {
        const userId = req.auth.user.id;
        const moduleStatus = await getModuleStatus({attributes:["productStatus","packageUpgrade","ecomStatus","subscriptionStatus"]});
        let upgradeLink = null, renewLink = null;

        if (moduleStatus.productStatus) {
            let prefix = req.prefix;
            let randomString = await utilityService.getRandomString(userId);
            
            if (moduleStatus.packageUpgrade) {
                if (moduleStatus.ecomStatus) {
                    let title = "upgrade";
                    upgradeLink = `${process.env.STORE_URL}account/login&string=${randomString}&db_prefix=${prefix}&${title}=1`;
                }
            }
            if (moduleStatus.subscriptionStatus) {
                if (moduleStatus.ecomStatus) {
                    let title = "renew";
                    renewLink = `${process.env.STORE_URL}account/login&string=${randomString}&db_prefix=${prefix}&${title}=1`;
                }
            }
        }
        return {upgradeLink, renewLink};
    }

    async formatProductValidity(packageValidityPeriod, productValidityDate) {
        const currentDate = new Date();
        productValidityDate = new Date(productValidityDate);
    
        if (productValidityDate <= currentDate) {
            return { productValidityDate, packageValidityPercentage: 0, colour: "#dc3545" };
        }
        const dateDifference = productValidityDate - currentDate;
        const diffDays = Math.ceil(dateDifference / (1000 * 60 * 60 * 24));
        logger.warn(`Package Validity: ${diffDays} days left.`);
        
        const packageValidityPercentage = (diffDays / (packageValidityPeriod * 30)) * 100;
        const cappedPercentage = Math.min(packageValidityPercentage,100);
        
        let colour = "#dc3545";

        if (cappedPercentage >= 50) {
            colour = "#6610f2";
        } else if (cappedPercentage >= 30) {
            colour = "#0d6efd";
        } else if (cappedPercentage >= 10) {
            colour = "#0dcaf0";
        } 
        return {
            productValidityDate:convertTolocal(productValidityDate),
            packageValidityPercentage: cappedPercentage, 
            colour
        };
    }
}
export default new HomeService;
