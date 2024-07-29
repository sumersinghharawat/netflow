import { consoleLog, errorMessage, logger, successMessage } from "../../helper/index.js";
import { Configuration, Document, FAQ, News, User } from "../../models/association.js";
import crmService from "../../services/crmService.js";
import { makeHash } from "../../helper/utility.js";
import utilityService from "../../services/utilityService.js";
import usernameToid from "../../utils/usernameToid.js";
import Language from "../../models/language.js";

export const getAllNews = async (req, res, next) => {
    try {
        const news = await News.findAll({ raw: true });
        const response = await successMessage({ data: news});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getAllNews",error);
        return next(error);
    }
};

export const getNewsArticle = async (req, res, next) => {
    try {
        if(!req.query.newsId) {
            const response = await errorMessage({ code: 1108, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const newsId = req.query.newsId;
        const article = await News.findOne({ where: { id: newsId } });
        if(!article) {
            const response = await errorMessage({ code: 1108, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const response = await successMessage({ data: article});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getNewsAsticle",error);
        return next(error);
    }
};

export const getFAQs = async (req, res, next) => {
    try {
        const faqs = await FAQ.findAll({ where: {status: 1}});
        const response = await successMessage({ data: faqs});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getFAQs",error);
        return next(error);
    }
};

export const getDownloads = async (req,res,next) => {
    try {
        const downloads = await Document.findAll({});
        const response = await successMessage({ data: downloads});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getDownloads",error);
        return next(error);
    }
};

export const getLeads = async (req, res, next) => {
    try {
        const userId    = req.auth.user.id;
        const leads  = await crmService.getCRMLeads({req, userId});
        const countries = await utilityService.getAllCountriesAndStates();
        const response = await successMessage({data: {leads, countries}})
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getLeads",error);
        return next(error);
    }
}
export const updateLead = async (req, res, next) => {
    try {
        const userId        = req.auth.user.id;
        const leadId        = req.params.id;
        const updateData    = req.body;
        const checkLead     = await crmService.checkLead({ userId, leadId});
        if(!checkLead) {
            const response  = await errorMessage({ code: 1111, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        await crmService.updateLead({ leadId, updateData});
        const response = await successMessage({data: "Lead_Updated_Successfully"});
        return res.status(response.code).json(response.data);
        
    } catch (error) {
        logger.error("ERROR FROM updateLead",error);
        return next(error);
    }
}

export const searchLead = async (req, res, next) => {
    try {
        const userId    = req.auth.user.id;
        const query     = req.query.name ? req.query.name : '';
        const response  = await crmService.searchLead({ userId, query });
        res.status(response.code).json(response.data)
    } catch (error) {
        logger.error("ERROR FROM searchLead",error);
        return next(error);
    }
}

export const addLcpLead = async(req, res, next) => {
    try {
        const referralId    = req.query.referralId;
        const queryHash     = req.query.hash;
        // const { replicaHashKey } = await Configuration.findOne({ attributes: ["replicaHashKey"] });
        const replicaHashKey = process.env.HASH_KEY
        const generatedHash = await makeHash({ param: referralId, hashKey: replicaHashKey });
        if(queryHash != generatedHash) {
            const response = await errorMessage({code: 1112, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const user        = await usernameToid(referralId);
        await crmService.addCRMLead({ userId: user.id, crmData: req.body });
        const response = await successMessage({data: "Lead Created Successfully"});
        res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM addLcpLead",error);
        return next(error);
    }
}

export const getCompanyData = async (req, res, next) => {
    try {
        const referralId    = req.query.referralId;
        const queryHash     = req.query.hash;
        // const { replicaHashKey } = await Configuration.findOne({ attributes: ["replicaHashKey"] });
        const replicaHashKey = process.env.HASH_KEY
        const generatedHash = await makeHash({ param: referralId, hashKey: replicaHashKey });
        if(queryHash != generatedHash) {
            const response = await errorMessage({code: 1112, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const user            = await usernameToid(referralId);
        const companyProfile  = await utilityService.getCompanyProfile();
        const countries       = await utilityService.getAllCountriesAndStates();
        const defaultLang = await Language.findOne({
            attributes: ["code"],
            include: {
                model: User,
                attributes: [],
                where: { id: user.id },
            }
        })
        const response = await successMessage({ data: { companyProfile, countries, defaultLang } });
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getCompanyData",error);
        return next(error);
    }
}

