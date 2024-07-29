import {Op} from "sequelize";
import { consoleLog, errorMessage, logger, successMessage } from "../../helper/index.js";
import { getModuleStatus, usernameToid } from "../../utils/index.js";
import { uploadFile } from "../../utils/fileUpload.js";
import fs from "fs";
import EpinService from "../../services/epinService.js";
import InternalCartService from "../../services/internalCartService.js";
import PaymentService from "../../services/paymentService.js";
import { PaymentGatewayDetail, PaymentReceipt, PinNumber } from "../../models/association.js";
import registerService from "../../services/registerService.js"
import path from "path";

export const getPaymentMethods = async (req, res, next) => {
    try {
        const action   = req.query.action;
        const data     = {
            methods : [],
            epins : []
        };
        const paymentMethods = await PaymentService.getPaymentMethods(action);
        Object.entries(paymentMethods).map(([key, value]) => {
            data["methods"][key] = {
                id: value.id,
                code: value.name,
                title: value.slug,
                logo: value.logo,
                gateway: !!value.PaymentGatewayDetail
            };
            switch (value.slug) {
                case "e-pin":
                    data["methods"][key].icon = "fa-solid fa-bolt";
                    break;
                case "e-wallet":
                    data["methods"][key].icon = "fa-solid fa-wallet";
                    break;
                case "free-joining":
                    data["methods"][key].icon = "fa-regular fa-id-card";
                    break;
                case "bank-transfer":
                    data["methods"][key].icon = "fa-solid fa-paper-plane";
                    break;
                case "stripe":
                    data["methods"][key].icon = "fa-brands fa-cc-stripe";
                    break;
                case "paypal":
                    data["methods"][key].icon = "fa-brands fa-paypal";
                    break;
                default:
                    break;
            }
        });
        let validEpins = [];
        let paypalPlanId;
        if (paymentMethods.filter( gateway => gateway.slug === "e-pin").length > 0) {
            validEpins = await registerService.getValidEpins(req.auth.user.id);
        }
        data["epins"]      = validEpins;
        if ((action === "membership_renewal") && (paymentMethods.filter( gateway => gateway.slug === "paypal").length > 0)) {
            paypalPlanId = await PaymentService.getPaypalPlanId(req.auth.user.id);
        }
        data["paypalPlanId"] = paypalPlanId;
        const response  = await successMessage({data});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getPaymentMethods",error);
        return next(error);
    }
};

 export const checkEpinValidity = async (req,res,next) => {
    try {
        const userId        = req.auth.user.id;
        const paymentMethod = req.body.paymentMethod;
        const epinArr       = req.body.epinData; // ["9TS8H2AIJC","WytObGbq73ZHE"]
        // const totalCart     = req.body.totalCart;
        const totalCart     = await InternalCartService.getTotalCart(userId);
        let totalCartLeft   = Number(totalCart);
        

        await EpinService.updateExpiredEpinStatus(userId);

        const epinData = await PinNumber.findAll({
            attributes:["numbers","balanceAmount","status"],
            where:{
                allocatedUser: userId,
                numbers:{[Op.in]:epinArr}
            },
            raw:true
        });

        Object.entries(epinData).map(([key,epin]) => {
            totalCartLeft -= Number(epin.balanceAmount);
            epin["totalCartLeft"] = totalCartLeft;
        });

        const data = {
            totalCartLeft : totalCartLeft>=0 ? totalCartLeft : 0,
            epins:epinData
        };
        return res.status(200).json(data);
    } catch (error) {
        logger.error("ERROR FROM checkEpinValidity",error);
        next(error);
    }
};

export const uploadBankReceipt = async (req,res,next) => {
    let response;
    try {
        response = await uploadFile(req, res);
        logger.debug("BANK UPLOAD DETAILS",response);
    } catch (error) {
        logger.error("ERROR FROM uploadBankReceipt")
        response = await errorMessage({ code: error.error, statusCode: 422 });
        return res.status(response.code).json(response.data);
    }
    try {
        let bankReceipt = response.file[0].path;
        let receipt;
        if (response.data.type == "repurchase") {
            let { id: userId } = await usernameToid(response.data.username)
            receipt = await PaymentService.insertIntoCartPaymentReceipt({
                userId: userId,
                receipt: bankReceipt
            })
        } else if (response.data.type == "renewal" || response.data.type == "upgrade") {
            // let { id: userId } = await usernameToid(response.data.username)
            const userId = req.auth.user.id;
            receipt = await PaymentService.insertIntoPaymentReceipt({
                userId: userId,
                receipt: bankReceipt,
                username:response.data.username,
                type: response.data.type
            })
        } 
        else {
            receipt = await PaymentService.insertIntoPaymentReceipt({
                receipt: bankReceipt,
                username: response.data.username,
                type: response.data.type
            });
        }
        logger.debug("receipt",receipt)
        if (!receipt) {
            const response = await errorMessage({ code: 1099, statusCode: 422 });
            return res.status(response.code).json(response.data);

        }

        response = await successMessage({ data: {message: response.message, file: response.file[0]}});
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);
    }
};

export const removeBankReceipt = async (req,res,next) => {
    try {
        let file = req.body.filepath;
        let type = req.body.type;
        
        // varies depending on how app is run
        const __dirname     = path.dirname(new URL(import.meta.url).pathname);
        const filePath = path.join("/app/uploads/",type, file);
        const imagePath = process.env.IMAGE_URL + 'uploads/' + type + "/" + file;
        logger.info("\nfilePath",filePath,"\nimagePath",imagePath)
        if (fs.existsSync(filePath)) {
            fs.unlinkSync(filePath);
            console.log("Old image deleted successfully.");

            const result = await PaymentReceipt.destroy({ where: { receipt: imagePath } });
            const response = await successMessage({ data: "File deleted successfully." });
            return res.status(response.code).json(response.data);

        } else {
            console.log("Old image does not exist.");
            const response = await errorMessage({ code: 1032, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }
    } catch (error) {
        logger.error("ERROR FROM removeBankReceipt",error);
        return next(error);
    }
};

export const getPaymentGatewayKey = async (req,res,next) => {
    const paymentMethod = req.query.paymentMethod;
    
    const publicKey = await PaymentGatewayDetail.findOne({attributes:["publicKey"],where:{paymentGatewayId:paymentMethod},raw:true});
    return res.status(200).json(publicKey);
};







