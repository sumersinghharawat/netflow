import crypto from "crypto";
import { consoleLog, errorMessage, logger, successMessage } from "../helper/index.js";
import Language from "../models/language.js";
import StringValidator from "../models/stringValidator.js";
import { Activity, CompanyProfile, Country, State, User } from "../models/association.js";
import CommonMailSetting from "../models/commonMailSetting.js";
import PasswordReset from "../models/passwordReset.js";
import Placeholder from "../models/placeholder.js";
import { replacePlaceholder } from "../utils/common.js";
import { encrypt } from "../utils/crypto.js";
class utilityService {
    async updateCurrency(req, res, next) {
        try {
            let { currencyId } = req.body;
            const response = await User.update(
                { defaultCurrency: currencyId },
                { where: { id: req.auth.user.id } }
            );
            if (!response[0]) return await errorMessage({ code: 1086, statusCode: 404 });
            return await successMessage({ data: [] });
        } catch (error) {
            next(error);
        }
    }

    async updateLanguage(req, res, next) {
        try {
            let { langId } = req.body;
            const response = await User.update(
                { defaultLang: langId },
                { where: { id: req.auth.user.id } }
            );

            if (!response[0]) return await errorMessage({ code: 1086, statusCode: 404 });
            return await successMessage({ data: [] });
        } catch (error) {
            next(error);
        }
    }
    async insertUserActivity({ userId, userType, data, description, ip, activity, transaction }) {
        const options = transaction ? { transaction } : {};
        return await Activity.create({
            userId, activity, ip, userType, data, description
        }, options);
    }
    async getAllCountriesAndStates(req, res, next) {
        try {
            const CountriesAndStates = await Country.findAll({
                where: { status: 1 },
                attributes: ["id", "name", "phone_code"],
                include: [{ model: State, attributes: ["id", "name", "code", ["country_id", "countryId"]] }],
                order: [["name", "ASC"]],
            });
            return CountriesAndStates;
        } catch (error) {
            logger.error("ERROR FROM getAllCountriesAndStates", error);
            return next(error);
        }
    }
    async getDefaultLanguage(req, res, next) {
        try {
            let defaultLanguage = await Language.findOne({ where: { default: 1 } });
            return defaultLanguage;
        } catch (error) {
            logger.error("ERROR FROM getDefaultLanguage", error);
            next(error);
        }
    }

    async subtractDates(toDate, timeFrame) {
        try {
            let fromDate = new Date(toDate);

            switch (timeFrame) {
                case "week":
                    fromDate = new Date(fromDate.setDate(fromDate.getDate() - 7));
                    break;
                case "month":
                    fromDate = new Date(fromDate.setMonth(fromDate.getMonth() - 1));
                    break;
                case "year":
                    fromDate = new Date(fromDate.setFullYear(fromDate.getFullYear() - 1));
                    break;
                default:
                    logger.warn(`INVALID TIME FRAME: ${timeFrame}`);
                    throw new Error("Invalid time frame.");
                    break;
            };
            // logger.warn(`subtractDates: fromDate ${fromDate.toISOString()} toDate ${toDate.toISOString()}`)
            return fromDate;
        } catch (error) {
            logger.error("ERROR FROM subtractDates", error);
            throw error;
        }
    }

    async getGraphDate(timeFrame) {
        const toDate = new Date();
        let fromDate,dateFormat;
        
        switch (timeFrame) {
            case "day":
                fromDate = new Date(toDate.setDate(toDate.getDate() - 7));
                dateFormat = "%a";
                break;
            case "month":
            case null:
                fromDate = new Date(toDate.setFullYear(toDate.getFullYear() - 1));
                dateFormat = "%b %y";
                break;

            case "year":
                fromDate = new Date(toDate.setFullYear(toDate.getFullYear() - 5));
                dateFormat = "%Y";
                break;

            default:
                throw new Error(`INVALID TIME FRAME ${timeFrame}`);
        };
        return { fromDate, dateFormat };
    }

    async getGraphDict(toDate, timeFrame) {
        const graphData = {};
        let fromDate;
        switch (timeFrame) {
            case "day":
                fromDate = new Date(toDate.setDate(toDate.getDate() - 7));
                for (let i = 0; i < 7; i++) {
                    toDate.setDate(toDate.getDate() + 1);
                    const day = toDate.toLocaleString("default", { weekday: "long" }).substring(0, 3);
                    const year = toDate.getFullYear();
                    const key = `${day}`;
                    graphData[key] = 0;

                };
                break;

            case "month":
            case null:
                fromDate = new Date(toDate.setFullYear(toDate.getFullYear() - 1));
                for (let i = 0; i < 12; i++) {
                    toDate.setMonth(toDate.getMonth() + 1);
                    const month = toDate.toLocaleString("default", { month: "long" }).substring(0, 3);
                    const year = toDate.getFullYear().toString().substring(2, 4);
                    const key = `${month} ${year}`;
                    graphData[key] = 0;

                };
                break;

            case "year":
                fromDate = new Date(toDate.setFullYear(toDate.getFullYear() - 5));

                for (let i = 0; i < 6; i++) {
                    // const month = toDate.toLocaleString("default", { month: "long" });
                    const year = toDate.getFullYear();
                    const key = `${year}`;
                    graphData[key] = 0;

                    toDate.setFullYear(toDate.getFullYear() + 1);
                };
                break;

            default:
                throw new Error(`INVALID TIME FRAME ${timeFrame}`);
        };
        return { fromDate, graphData };
    }

    async setGraphDict(timeFrame, downlines, graphData) {
        switch (timeFrame) {
            case "day":
                downlines.forEach((user) => {
                    const dateOfJoining = new Date(user["sponsorDescendantUser.dateOfJoining"]);
                    // console.log(dateOfJoining)
                    const day = dateOfJoining.toLocaleString("default", { weekday: "long" }).substring(0, 3);
                    const key = `${day}`;
                    if (key in graphData) {
                        graphData[key] = (graphData[key] || 0) + 1;
                    }
                });
                break;

            case "month":
            case null:
                downlines.forEach((user) => {
                    const dateOfJoining = new Date(user["sponsorDescendantUser.dateOfJoining"]);
                    const month = dateOfJoining.toLocaleString("default", { month: "long" }).substring(0, 3);
                    const year = dateOfJoining.getFullYear().toString().substring(2, 4);
                    const key = `${month} ${year}`;

                    if (key in graphData) {
                        graphData[key] = (graphData[key] || 0) + 1;
                    }
                });
                break;

            case "year":
                downlines.forEach((user) => {
                    const dateOfJoining = new Date(user["sponsorDescendantUser.dateOfJoining"]);
                    const year = dateOfJoining.getFullYear();
                    const key = `${year}`;

                    if (key in graphData) {
                        graphData[key] = (graphData[key] || 0) + 1;
                    }
                });
                break;

            default:
                break;
        }
        return graphData;
    }

    async getRandomString(userId) {
        const stringCheck = await StringValidator.findOne({ where: { userId, status: 1 } });
        if (stringCheck) return stringCheck.string;
        const randomString = `${crypto.randomBytes(17).toString("hex")}${Date.now()}`;
        await StringValidator.create({
            userId: userId,
            string: randomString,
            status: 1
        });
        return randomString;
    }
    async getCompanyProfile() {
        const data = await CompanyProfile.findOne();
        data.logo = (data.logo) ? data.logo : `${process.env.ADMIN_URL}assets/images/logo-dark.png`;
        return data;
    }

    async getMailOptions({ mailSettings, mailDetails, email, userId, type, toData, authUser }) {
        const placeholders = await Placeholder.findAll();
        const details = await CommonMailSetting.findOne({
            attributes: ["subject", "mailContent"],
            where: {
                mailType: type
            }
        });
        if(!details) {
            throw new Error('mail content not found for this type');
        }
    
        let content = details.mailContent;
        let subject = details.subject;
        let from    = `${mailSettings.fromName} < ${mailSettings.fromEmail} >`;
        let to      = `${toData?.name} < ${email} >`;
        const company = await this.getCompanyProfile();
        let placeholderData = {};

        switch (type) {
            case "forgot_password":
                placeholderData['link'] = mailDetails.link;
                content = await replacePlaceholder({ inputString: content, placeholderData });
                break;
            case "payout_request":
                const admin = await User.findOne({attributes:["username", "email"],where:{userType:"admin"}});
                const auth  = await User.findByPk(authUser.id);
                from    = `${auth.username} < ${auth.email} >`;
                to      = `${admin?.username} < ${admin.email} >`;
                for (const placeholder of placeholders) {
                    switch (placeholder.name) {
                        case 'adminUserName':
                            placeholderData[placeholder.placeholder] = admin.username;
                            break;
                        case 'username':
                            placeholderData[placeholder.placeholder] = authUser.username;
                            break;
                        case 'payoutAmount':
                            placeholderData[placeholder.placeholder] = mailDetails.payoutAmount;
                            break;
                        default:
                            break;
                    }
                }
                content = await replacePlaceholder({ inputString: content, placeholderData });
                break;
            case "registration":
                for (const placeholder of placeholders) {
                    switch (placeholder.name) {
                        case 'name':
                            placeholderData[placeholder.placeholder] = toData.name;
                            break;
                        case 'fullName':
                            placeholderData[placeholder.placeholder] = toData.fullName;
                            break;
                        case 'companyName':
                            placeholderData[placeholder.placeholder] = company.name;
                            break;
                        default:
                            break;
                    }
                }
                content = await replacePlaceholder({ inputString: content, placeholderData });
                subject = await replacePlaceholder({ inputString: subject, placeholderData });
                break;
            case "send_tranpass":
                for (const placeholder of placeholders) {
                    switch (placeholder.name) {
                        case 'name':
                            placeholderData[placeholder.placeholder] = toData.name;
                            break;
                        case 'password':
                            placeholderData[placeholder.placeholder] = mailDetails.newPassword;
                            break;
                        default:
                            break;
                    }
                }
                content = await replacePlaceholder({ inputString: content, placeholderData });
                break;
            case "change_password":
                for (const placeholder of placeholders) {
                    switch (placeholder.name) {
                        case 'full_Name':
                            placeholderData[placeholder.placeholder] = toData.name;
                            break;
                        case 'newPassword':
                            placeholderData[placeholder.placeholder] = mailDetails.newPassword;
                            break;
                        default:
                            break;
                    }
                }
                content = await replacePlaceholder({ inputString: content, placeholderData });
                break;
            case "registration_email_verification":
                for (const placeholder of placeholders) {
                    switch (placeholder.name) {
                        case 'full_Name':
                            placeholderData[placeholder.placeholder] = toData.fullName;
                            break;
                        case 'companyName':
                            placeholderData[placeholder.placeholder] = company.name;
                            break;
                        default:
                            break;
                    }
                }
                const encryptedUsername = await encrypt(authUser.username);
                if (process.env.DEMO_STATUS == "yes") {
                    const admin = await User.findOne({ attributes: ["username", "email"], where: { userType: "admin" } });
                    const encryptedAdminUsername = await encrypt(admin.username);
                    placeholderData["link"] = `${process.env.SITE_URL}/confirm_email/${encryptedUsername}/${encryptedAdminUsername}`;
                } else {
                    placeholderData["link"] = `${process.env.SITE_URL}/confirm_email/${encryptedUsername}`;
                }

                content = await replacePlaceholder({ inputString: content, placeholderData });
                logger.info("content",content)

                break;
            default:
                break;
        }

        let mailOptions = {
            from,
            to,
            subject: subject,
            template: 'email',
            context: {
                subject: subject,
                content: content,
                logo: company.logo,
                companyName: company.name,
                address: company.address
            }
        };
        return mailOptions;
    }
}

export default new utilityService;