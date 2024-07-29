import bcrypt from "bcryptjs";
import crypto from "crypto";
import fs from "fs";
import path from "path";
import { consoleLog, convertTolocal, errorMessage, logger, successMessage } from "../../helper/index.js";
import { getModuleStatus } from "../../utils/index.js";
import { uploadFile } from "../../utils/fileUpload.js";
import profileService from "../../services/profileService.js";
import homeService from "../../services/homeService.js";
import { Configuration, Country, KycCategory, KycDoc, OcCustomer, TransactionPassword, User, UserDetail, State, CustomfieldValue, Rank, RankDetail, Package, PurchaseRank, RankDownlineRank, SubscriptionConfig } from "../../models/association.js";
import { Op } from "sequelize";
import _ from "lodash";
import PasswordPolicy from "../../models/passwordPolicy.js";
import mailService from "../../services/mailService.js";

export const profileView = async (req, res, next) => {
    try {
        const userId         = req.auth.user.id;
        const moduleStatus   = await getModuleStatus({attributes: ["mlmPlan","ecomStatus","subscriptionStatus","productStatus","rankStatus"]});

        const [userData, customFields, payoutDetails, countries] = await Promise.all([
            profileService.getProfileData(userId, moduleStatus),
            profileService.getCustomFields(userId),
            profileService.getPayoutDetails(userId),
            Country.findAll({ where:{status: 1}, include: [{model: State}]})
        ]);

        let data             = {};
        data.payoutDetails   = {};
        data.countries       = countries;
        data.personalDetails = {
            name        : userData.UserDetail.name,
            secondName  : userData.UserDetail.secondName,
            gender      : userData.UserDetail.gender,
            dob         : userData.UserDetail.dob
        };
        data.bankDetails    = {
            bankName    : userData.UserDetail.bank,
            branchName  : userData.UserDetail.branch,
            holderName  : userData.UserDetail.nacctHolder,
            accountNo   : userData.UserDetail.accountNumber,
            ifsc        : userData.UserDetail.ifsc,
            pan         : userData.UserDetail.pan
        };
        data.contactDetails   = {
            address     : userData.UserDetail.address,
            address2    : userData.UserDetail.address2,
            country     : userData.UserDetail.Country,
            state       : userData.UserDetail.State,
            city        : userData.UserDetail.city,
            zipCode     : userData.UserDetail.pin,
            email       : userData.email,
            mobile      : userData.UserDetail.mobile,
            phone       : userData.UserDetail.land_phone
        };
        data.additionalDetails  = customFields;
       
        const selectedPayout = payoutDetails.find( (item) => item.id === userData.UserDetail.payoutType);
        if(selectedPayout.slug === "paypal") {
            data.payoutDetails.id    = selectedPayout.id;
            data.payoutDetails.label = selectedPayout.name;
            data.payoutDetails.value = userData.UserDetail.paypal;
        } else if(selectedPayout.slug === "stripe") {
            data.payoutDetails.id    = selectedPayout.id;
            data.payoutDetails.label = selectedPayout.name;
            data.payoutDetails.value = userData.UserDetail.stripe;
        }
        data.payoutDetails.options = payoutDetails;
        data.payoutDetails.options.forEach( (item, key) => {
            (item.id === userData.UserDetail.payoutType) 
                ? data.payoutDetails.options[key].isSelected = true
                : data.payoutDetails.options[key].isSelected = false;
        });
		let packageData;
		if(moduleStatus.ecomStatus) {
			packageData = {
				id             : userData.OcProduct.productId,
				name           : userData.OcProduct.model,
				productId      : userData.OcProduct.model,
				validity       : userData.OcProduct.validity,
				DataTypesimage : userData.OcProductdays
			};
		}

        // calculate package validity percentage
        let packageValidityDate = userData.productValidity;
        let productValidityPeriod;
        if(moduleStatus.ecomStatus){
            productValidityPeriod = userData.OcProduct.subscriptionPeriod;
        } else {
            if(moduleStatus.subscriptionStatus) {
                if(!moduleStatus.productStatus) {
                    let { subscriptionPeriod } = await SubscriptionConfig.findOne({attributes:["subscriptionPeriod"]});
                    productValidityPeriod = subscriptionPeriod;
                } else {
                    productValidityPeriod = userData.Package.validity;
                }
            }
        }
        
        let packageValidity = await homeService.formatProductValidity(productValidityPeriod, packageValidityDate);

        // Rank details
        let currentRank = null;
        let rankData = [];
        if (moduleStatus.rankStatus) {
            currentRank = await User.findByPk(userId, {
                attributes: ["id", "username"],
                include: [
                    {
                        model: Rank,
                        attributes: ["id", "name", "color", "image", "status"],
                    }
                ],
            });
            rankData = await profileService.getRankForProfile(rankData,moduleStatus)
        }
        
        data.profile    = {
            fullName    : userData.UserDetail.name +" "+ userData.UserDetail.secondName,
            username    : userData.username,
            email       : userData.email,
            sponsor     : userData.sponsor.username.toUpperCase(),
            father      : userData.father.username.toUpperCase(),
            pv          : userData.personalPv,
            gpv         : userData.groupPv,
            kycStatus   : userData.UserDetail.kycStatus,
            avatar      : userData.UserDetail.image,
            productValidity : packageValidity,
            package     : (moduleStatus.ecomStatus) ? packageData : userData.Package,
            autoRenewalStatus : moduleStatus.subscriptionStatus ? userData.autoRenewalStatus : null
        };
        if(moduleStatus.mlmPlan === "Binary") {
            data.profile.leftCarry  = userData.totalLeftCarry;
            data.profile.rightCarry = userData.totalRightCarry;
            data.profile.position   = userData.position;
        }
        if(moduleStatus.rankStatus) {
            data.profile.rankDetails = {
                currentRank: currentRank.Rank,
                rankData
            };
        }
        data.passwordPolicy = await PasswordPolicy.findOne({});
        const response      =  await successMessage({data});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM profileView",error);
        next(error);        
    }
};

export const updatePersonalData = async (req, res, next) => {
    try {
        const moduleStatus  = await getModuleStatus({attributes: ["ecomStatus"]});
        const response      = await profileService.updatePersonalData(req, moduleStatus, next);
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);        
    }
};

export const updateContactDetails = async (req, res, next) => {
    try {
        const moduleStatus  = await getModuleStatus({attributes: ["ecomStatus"]});
        const response      = await profileService.updateContactDetails(req, moduleStatus, next);
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);        
    }
};

export const updateBankDetails = async (req, res, next) => {
    try {
        const response = await profileService.updateBankDetails(req);
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);        
    }
};
export const updatePaymentDetails = async (req, res, next) => {
    try {
        const response = await profileService.updatePaymentDetails(req);
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);        
    }
};
export const updateSettings = async (req, res, next) => {
    try {
        const moduleStatus  = await getModuleStatus({attributes: ["multilangStatus","multiCurrencyStatus","mlmPlan"]});
        const response      = await profileService.updateSettings(req, moduleStatus); 
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);        
    }
};
export const removeAvatar = async (req, res, next) => {
    try {
        const response      = await profileService.removeAvatar(req); 
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);        
    }
};
export const uploadAvatar = async (req, res, next) => {
    let response;
    try {
        response = await uploadFile(req, res);
    } catch (error) {
        logger.error("ERROR FROM uploadAvatar",error);
        response = await errorMessage({ code: error.error, statusCode: 422 });
        return res.status(response.code).json(response.data);      
    }
    try{
        let imagePath = response.file[0].path;
        const result = await profileService.updateuserDetails(req, imagePath);
        if (!result) {
            const response = await errorMessage({ code: 1024, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        response = await successMessage({ data: response});
        return res.status(response.code).json(response.data);
    } catch (error) {
        return next(error);
    }

};

export const changeUserPassword = async (req,res,next) => {
    try {
        const userId          = req.auth.user.id;
        const currentPassword = req.body.currentPassword;
        const newPassword     = req.body.newPassword;
        const passwordConfirm = req.body.passwordConfirm;
        const moduleStatus    = await getModuleStatus({attributes: ["ecomStatus","ecomStatusDemo"]});

        if (newPassword !== passwordConfirm) {
            const response = await errorMessage({ code: 1021, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }


        let user = await User.findOne({ where: { id: userId } });
        const checkPassword = await bcrypt.compare(currentPassword, user["password"]);
        if (!checkPassword) {
            const response = await errorMessage({ code: 1021, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }
        const newPasswordEncrypted = await bcrypt.hash(newPassword, 10);
        await user.update({ password: newPasswordEncrypted });

        if (moduleStatus["ecomStatus"] || moduleStatus["ecomStatusDemo"]) {
            let ecomUser = await OcCustomer.findOne({ where: { customerId: user["ecomCustomerRefId"] } });

            const newPasswordEncryptedEcom = crypto.createHash("md5").update(newPassword).digest("hex");
            await ecomUser.update({ password: newPasswordEncryptedEcom });

        }
        const toData = {
            name    : user.username,
            to      : user.email
        }
        const mailDetails = {
            newPassword
        };
        await mailService.sentNotificationMail({mailType:'change_password', toData, mailDetails});
        return res.status(200).json({status:true, data: "Password_updated_successfully"});
    } catch (error) {
        logger.error("ERROR FROM changeUserPassword",error);
        return next(error);
    }
};

export const changeTransactionPassword = async (req,res,next) => {
    try {
        const userId          = req.auth.user.id;
        const currentPassword = req.body.currentPassword;
        const newPassword     = req.body.newPassword;
        const passwordConfirm = req.body.passwordConfirm;3
        const user            = await User.findByPk(userId);

        if (newPassword !== passwordConfirm) {
            const response = await errorMessage({ code: 1021, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        let transactionPassword = await TransactionPassword.findOne({ where: { userId: userId } });
        const checkPassword = await bcrypt.compare(currentPassword, transactionPassword["password"]);
        if (!checkPassword) {
            const response = await errorMessage({ code: 1021, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        const newPasswordEncrypted = await bcrypt.hash(newPassword, 10);
        await transactionPassword.update({ password: newPasswordEncrypted });
        const toData = {
            name    : user.username,
            to      : user.email
        }
        const mailDetails = {
            newPassword
        };
        await mailService.sentNotificationMail({mailType:'send_tranpass', toData, mailDetails});

        const response = await successMessage({ data: "Transaction password updated successfully."})
        return res.status(response.code).json(response.data);

    } catch (error) {
        logger.error("ERROR FROM changeTransactionPassword",error);
        return next(error);
    }
};

export const getKYCDetails = async (req,res,next) => {
    try {
        const userId      = req.auth.user.id;
        let kycCategory = await KycCategory.findAll({where:{status:1}, raw:true});
        const groupedData = {};
        const userKyc     = await KycCategory.findAll(
                            {
                                attributes:["category", "id"],
                                include:[{ model: KycDoc, where:{userId}, attributes: ["id", "userId", "fileName", "status", "reason", "date", "createdAt", "updatedAt", "type"]}],
                            });

        // const appliedDoc  =  userKyc.map( (item) => {
        //     const approved = item.KycDocs.filter( doc => parseInt(doc.status) === 1).length;
        //     const pending = item.KycDocs.filter( doc => parseInt(doc.status) === 2).length;
        //     const rejected = item.KycDocs.filter( doc => parseInt(doc.status) === 0).length;
        //     item.status = {approved, pending, rejected};
        //     return item;
        // })
        const appliedDoc = userKyc.map((item) => {
            item.status = item.KycDocs.reduce(
                (acc, doc) => {
                    const status = parseInt(doc.status);
                    if (status === 1) acc.approved++;
                    else if (status === 2) acc.pending++;
                    else if (status === 0) acc.rejected++;
                    return acc;
                },
                { approved: 0, pending: 0, rejected: 0 }
            );
            return item;
        });
        const deleteCategory = appliedDoc.filter( item => parseInt(item.status.approved) === 2 || parseInt(item.status.pending) >= 2).map( category => category.id);
        kycCategory       = _.reject(kycCategory, category => deleteCategory.includes(category.id));
       
        const data = [];
        for(const item of appliedDoc) {
            data.push({
                id  : item.id, 
                category : item.category,
                date : convertTolocal(item.KycDocs[0].date),
                status: item.status.approved >= 1
                    ? "approved"
                    : item.status.pending >= 1
                        ? "pending"
                        : "deleted",
                files : item.KycDocs.filter(item => parseInt(item.status) !== 0 ).map( file => ({id:file.id,status:parseInt(file.status),file:file.fileName})),
                rejectedFiles : item.KycDocs.filter(item => parseInt(item.status) === 0 ).map( file => ({id:file.id,file:file.fileName}))

            })
        }
        const response = await successMessage({data: {kycCategory, userKyc:data}});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getKYCDetails",error);
        return next(error);
    }
};

export const kycUpload = async (req, res, next) => {
    try {
        if(!req.query.category) {
            const response = await errorMessage({ code: 1109, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }
        const userId        = req.auth.user.id;
        const category      = req.query.category;
        const __dirname     = path.dirname(new URL(import.meta.url).pathname);
        
        const kycVerification = await UserDetail.findOne({ attributes: ["kycStatus"], where: { userId: userId, kycStatus: 1 } })
        if (kycVerification) {
            const response = await errorMessage({ code: 1124, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }
        
        const kycRecord = await KycDoc.findAndCountAll({
            where:{
                userId:userId,
                type: category,
                status: 2 // pending
            },
        });
        if (kycRecord.count >= 2 ) {
            const response = await errorMessage({ code: 1110, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        // if no existing pending record, upload new file
        try {
            const result   = await uploadFile(req, res);
            logger.debug("KYC FILE DETAILS",result)
        } catch (error) {
            const response = await errorMessage({ code: error.error, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }
        const kycData = req.files.map(file => ({
            userId: userId,
            fileName: process.env.IMAGE_URL + file.path,
            type: category,
            status: 2,
            reason: "NA",
            date: new Date()
        }));
        const tabled = await KycDoc.bulkCreate(kycData);

        const response = await successMessage({data: "File uploaded successfully."});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM kycUpload",error);
        return next(error);
    }
};

export const kycDelete = async (req, res, next) => {
    try {
        const kycArr = req.body.kycArr;
        const __dirname     = path.dirname(new URL(import.meta.url).pathname);
        const kycDetail = await KycDoc.findAll({ 
            where: { 
                id: kycArr,
                status: {[Op.ne]: 1} // not approved
            }, 
            raw:true
        });
        kycDetail.forEach(async doc => {
            // Since fs.existsSync() expects a local file path, it won't work with a URL
            const imagePath = doc["fileName"].split(process.env.IMAGE_URL).pop();
            const filePath = path.join(__dirname, "../../", imagePath);
            logger.debug("imagePath: ",imagePath,"\nfilePath: ",filePath);
            if (fs.existsSync(filePath)) {
                fs.unlinkSync(filePath);
                console.log("Old image ",doc.id," deleted successfully.");
            } else {
                console.log("Old image ",doc.id," does not exist.");
            }
            await KycDoc.destroy({where: {id: doc.id}});
        });

        const response = await successMessage({data: "File deleted successfully."});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM kycDelete",error);
        return next(error);
    }
};

export const updateCustomFieldValue = async (req, res, next) => {
    try {
        const userId    = req.auth.user.id;
        if(!req['body']['fields'].length) {
            const response = await errorMessage({ code: 1106, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        
        for(const field of req.body.fields) {
            const currentValue = await CustomfieldValue.findOne({where: {customfieldId: field.customfieldId, userId}});
            if(currentValue) {
                await currentValue.update({value: field.value});
            } else {
                await CustomfieldValue.create({
                    customfieldId : field.customfieldId,
                    userId,
                    value : field.value
                });
            }   
        }
        const response = await successMessage({ data: "Updated Successfully"})
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM updateCustomFieldValue",error);
        return next(error);
    }
}