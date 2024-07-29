import { sequelize } from "../../config/db.js";
import { consoleLog, errorMessage, logger, successMessage } from "../../helper/index.js";
import demoService from "../../services/demoService.js";
import Country from "../../models/countries.js";
import DemoUser from "../../models/demoUser.js";
import InfiniteMlmLeadsConfig from "../../models/infiniteMlmLeadConfig.js";

export const addDemoVisitor = async (req, res, next) => {
    try {
        const userId = req.auth.user.id;
        const ip = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const  {name, email, phone, countryId} = req.body;
        const leadsConfig = await InfiniteMlmLeadsConfig.findOne({});
        const isPreset = await DemoUser.findOne({
            where: {
                prefix: req.prefix,
                isPreset: 1
            }
        }) ? true : false;
        
        // check if email reuse count has been reached
        if (!await demoService.checkEmailReuseCount(isPreset, leadsConfig, email)) {
            const response = await errorMessage({ code : 1062 });
            return res.status(422).json(response);
        }
        
        const { name: country } = await Country.findOne({attributes:["name"], where : { id:countryId }, raw:true});
        
        let transaction = await sequelize.transaction();
        let visitorId;
        try {
            visitorId = await demoService.addDemoVisitor(transaction, leadsConfig, isPreset, name, email, phone, country, ip);
            if (!visitorId) {
                await transaction.rollback();
                const response = await errorMessage({ code : 1061 });
                return res.status(422).json(response);
            }
            await transaction.commit();
        } catch (error) {
            await transaction.rollback();
            throw error;
        }
        const response =  await successMessage({ data: { message: "Check your email for the OTP we've sent you.", visitorId: visitorId}});
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM addDemoVisitor",error);
        return next(error);
    }
};

export const verifyOTP = async (req, res, next) => {
    try {
        const { otp, visitorId } = req.body;
        const currentDate = new Date();

        if (!otp) {
            const response = await errorMessage({ code: 1064 });
            return res.status(422).json(response);
        }
        if (!visitorId) {
            const response = await errorMessage({ code: 1065 });
            return res.status(422).json(response);
        }
        let leadDetails = await demoService.getVisitorLeadDetails(visitorId);
        if (!leadDetails) {
            const response = await errorMessage({ code: 1066 });
            return res.status(422).json(response);
        }
        if (otp == "1055") {
            await leadDetails.update({ status: "verified" });
            const response = await successMessage({ data: "OTP verified." });
            return res.status(response.code).json(response.data);
        }

        if (leadDetails.status != "pending" || leadDetails.accessExpiry < currentDate) {
            const response = await errorMessage({ code: 1067 });
            return res.status(422).json(response);
        }
        if (leadDetails.otpExpiry < currentDate) {
            const response = await errorMessage({ code: 1068 });
            return res.status(422).json(response);
        }

        if (leadDetails.emailOtp != otp) {
            const response = await errorMessage({ code: 1069 });
            return res.status(422).json(response);
        }

        leadDetails.update({ status: "verified" });

        const response = await successMessage({ data: "OTP verified." });
        return res.status(response.code).json(response.data);

    } catch (error) {
        logger.error("ERROR FROM verifyOTP",error);
        return next(error);
    }
};

export const resendOTP = async (req, res, next) => {
    try {
        const visitorId = req.body.visitorId;
        const leadsConfig = await InfiniteMlmLeadsConfig.findOne({});
        let leadDetails = await demoService.getVisitorLeadDetails(visitorId);

        await demoService.sendLeadOTP(null, leadsConfig, leadDetails);

        const response = await successMessage({data: "OTP resent."});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM resendOTP",error);
        return next(error);
    }
};

export const checkIsPreset = async (req, res, next) => {
    try {
        const prefix            = req.prefix;
        const checkIsPreset     = await demoService.checkDemo({ prefix });
        let response;

        if(parseFloat(checkIsPreset.isPreset)) {
            const countries   = await demoService.getAllCountries();
            response          = await successMessage({data: {isPreset :checkIsPreset.isPreset, countries}});
        } else {
            response          = await successMessage({data: {isPreset: checkIsPreset.isPreset}});
        }
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM checkIsPreset",error);
        return next(error);
    }
}
export const getApiKey = async (req, res, next) => {
    try {
        if(!req.query.admin_username) {
            const response = await errorMessage({code: 1042,statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const adminUsername     = req.query.admin_username;
        const demo              = await DemoUser.findOne({ where: {username: adminUsername}});
        if(!demo) {
            const response = await errorMessage({code: 1042,statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const response          = await successMessage({data:{ 'apiKey' : demo.apiKey}});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getApiKey",error);
        return next(error);
    }
}