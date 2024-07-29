import dotenv from "dotenv";
import { checkConnection, sequelize } from "../config/db.js";
import Configuration from "../models/configuration.js";
import { errorMessage } from '../helper/response.js';
import pluralize from "pluralize";
import logger from "../helper/logger.js";

dotenv.config();

export const apiKeyCheck = async (req, res, next) => {
    try {
        const { url } = req;
        logger.info("url",url)
        if (url.startsWith('/node_api/uploads')) {
            return next(); // Pass control to the next middleware or route handler
        }
        const urlExceptions = ["/auth/paypal-webhook","/auth/email-verification"];
        const prefix = process.env.PREFIX;
        const apiKey = req.headers['api-key'];
        req.apiKey = apiKey;
        if (!prefix) {
            const response = await errorMessage({ code: 1097, statusCode: 403 });
            return res.status(500).json(response)
        }
        if (!apiKey && !urlExceptions.includes(url)) {
            const response = await errorMessage({ code: 1000, statusCode: 403 });
            return res.status(500).json(response)
        }
        req.prefix = prefix;
        const exceptions = ["compensation", "oc_product", "roi_order", "oc_session","rank_downline_rank","ticket_status", "ticket_activity", "ticket_priority"];
        
        for (const modelName of Object.keys(sequelize.models)) {
            const model = sequelize.models[modelName];

            model.originalTableName = model.tableName;
            Object.defineProperty(model, 'tableName', {
                get() {
                    const underscoredModelName = underscoreModelName(this.name);
                    let pluralizedModelName;
                    if (!exceptions.includes(underscoredModelName)) {
                        pluralizedModelName = pluralize(underscoredModelName);
                    } else {
                        pluralizedModelName = underscoredModelName
                    }
                    return `${prefix}_${pluralizedModelName}`;
                }
            });
        }
        const projectApiKey = await Configuration.findOne({ attributes: ["apiKey"] });
        if (!projectApiKey.apiKey) {
            const response = await errorMessage({ code: 1096, statusCode: 403 });
            return res.status(500).json(response)
        }
        if (projectApiKey.apiKey !== apiKey && !urlExceptions.includes(url)) {
            const response = await errorMessage({ code: 1001, statusCode: 403 });
            return res.status(500).json(response)
        }
        next();
    } catch (error) {
        logger.error("ERROR FROM checkApiKey",error);
        await checkConnection();
        return next(error);
    }
}

function underscoreModelName(modelName) {
    return modelName.replace(/([a-z])([A-Z])/g, '$1_$2').toLowerCase();
}