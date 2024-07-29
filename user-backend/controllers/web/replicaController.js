import fs from "fs";
import path from "path";
import moment from "moment-timezone";
import { consoleLog, errorMessage, logger, successMessage } from "../../helper/index.js";
import { getModuleStatus, usernameToid, getConfiguration,
    getTermsAndCondition, defaultLanguage, 
    signupSettings, getUsernameConfig, getPasswordPolicy, generateSalesInvoiceNumber
} from "../../utils/index.js";
import { uploadFile } from "../../utils/fileUpload.js";
import paymentService from "../../services/paymentService.js";
import utilityService from "../../services/utilityService.js";
import { CompanyProfile, Configuration, Contact, DemoUser, ReplicaBanner, ReplicaContent, 
    User, UserDetail, SignupField, PendingRegistration, SignupSettings, PaymentGatewayConfig, Package, CurrencyDetail, PaymentReceipt } from "../../models/association.js";
import dotenv from "dotenv";
import { makeHash } from "../../helper/utility.js";
import regSteps from "../../utils/regSteps.js";
import { Op, Sequelize } from "sequelize";
import registerService from "../../services/registerService.js";
import _ from "lodash";
import commissionService from "../../services/commissionService.js";
import { sequelize } from "../../config/db.js";
import Language from "../../models/language.js";
import appService from "../../services/appService.js";

dotenv.config();

export const getReplicaBanner = async (req, res, next) => {
    try {
        const userId = req.auth.user.id;
        const replicaBanner = await ReplicaBanner.findAll({ where: { userId: userId }});
        return res.status(200).json(replicaBanner);
    } catch (error) {
        logger.error("ERROR FROM getReplicaBanner",error);
        return next(error);
    }
};

export const uploadReplicaBanner = async (req, res, next) => {
    try {
        const userId        = req.auth.user.id;
        const ip            = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const __dirname     = path.dirname(new URL(import.meta.url).pathname);
        let upload;
        try {
            req.query.type  = "replica/banner";
            upload          = await uploadFile(req, res);
        } catch (err) {
            const response  = await errorMessage({code: err.error, statusCode: 422})
            return res.status(response.code).json(response.data);
        }
        const image         = process.env.IMAGE_URL + upload.file[0].path;
        
        // const replicaBanner = await ReplicaBanner.findOne({ where: { userId: userId } });
        
        logger.info("REPLICA BANNER UPLOADED: ",upload);
        const replicaBannerData = upload.file.map(element => ({
            userId: userId,
            image: process.env.IMAGE_URL + element.path,
            isDefault: 0
        }))
        await ReplicaBanner.bulkCreate(replicaBannerData)
        // if (replicaBanner) {
        //     if (replicaBanner.image) {
        //         // Since fs.existsSync() expects a local file path, it won't work with a URL
        //         const imagePath = replicaBanner["image"].split(process.env.IMAGE_URL).pop();
        //         const filePath = path.join(__dirname, "../", "../", imagePath);
        //         logger.debug("imagePath: ",imagePath,"\nfilePath: ",filePath);
        //         if (fs.existsSync(filePath)) {
        //             fs.unlinkSync(filePath);
        //             console.log("Old image deleted successfully.");
        //         } else {
        //             console.log("Old image does not exist.");
        //         }
        //     }
        //     await replicaBanner.update({ image: image });
        // } else {
        //     await ReplicaBanner.create({
        //         userId: userId,
        //         image: image,
        //         isDefault: 0
        //     });
        // }

        await utilityService.insertUserActivity({
            userId:userId, 
            userType:"user", 
            data:"", 
            description:"Replica Banner uploaded by user", 
            ip:ip, 
            activity:"Replica Banner"
        });
        const response = await successMessage({ data: {message: "Replica banner uploaded successfully.", image}})
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM uploadReplicaBanner",error);
        return next(error);
    }
};

export const deleteReplicaBanner = async (req, res, next) => {
    try{
        const userId = req.auth.user.id;
        const bannerId = req.body.id;

        const deleteBanner = await ReplicaBanner.destroy({ where: { id: bannerId, userId: userId } });
        if (deleteBanner == 0) {
            const response  = await errorMessage({code: 1032, statusCode: 422});
            res.status(response.code).json(response.data);
        }
        const response = await successMessage({ data: { message: "Replica banner deleted successfully." } })
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM deleteReplicaBanner",error);
        return next(error);
    }
}

export const getReplicaHome = async (req,res,next) => {
    try {
        if(!req.query.referralId || !req.query.hash || req.query.referralId === '' || req.query.hash === '') {
            const response  = await errorMessage({code: 1114, statusCode: 422});
            res.status(response.code).json(response.data);
        }
        const username      = req.query.referralId;
        const hash          = req.query.hash;
        const user          = await User.findOne({
                                attributes:["id","email","defaultLang"], 
                                where:{username:username, userType: {[Op.ne]:"emloyee"}},
                                include:[
                                    {model:UserDetail,
                                        attributes:["name","secondName","mobile"]}
                            ]}); 
        if (!user) {
            const response = await errorMessage({ code: 1070, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }
        // let { replicaHashKey } = await Configuration.findOne({ attributes: ["replicaHashKey"] });
        let replicaHashKey = process.env.HASH_KEY
        const generatedHash = await makeHash({param: username, hashKey: replicaHashKey});

        if(generatedHash !== hash) {
            const response = await errorMessage({code: 1112, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const langId = (user.defaultLang) ? user.defaultLang : 1; 
        const {code: langCode} = await Language.findOne({where:{id:langId}});

        const moduleStatus  = await getModuleStatus({attributes:["ecomStatus"]});
        let homeDetails     = await ReplicaContent.findAll({ where: { userId: user.id, langId} });
        if(!homeDetails.length) {
            homeDetails     = await ReplicaContent.findAll({ where: { userId: null, langId} });
        }
        let replicaBanner = await ReplicaBanner.findAll({ where: { userId: user.id } });
        if(!replicaBanner.length) {
            replicaBanner = await ReplicaBanner.findAll({ where: { userId: null} });
        }
        const company           = await CompanyProfile.findOne();

        // get registration url
        let registrationUrl = `${process.env.SITE_URL}/replica-register`;
        if(moduleStatus.ecomStatus) {
            if (process.env.DEMO_STATUS == "yes") {
                registrationUrl = `${process.env.STORE_URL}?route=register/mlm&replica=${username}&db_prefix=${req.prefix}`;
            }else{
                registrationUrl = `${process.env.STORE_URL}?route=register/mlm&replica=${username}`;
            }
        }
        const replicaData   = {
            replicaBanners : {
                title1 : homeDetails.find( item => item.key === "home_title1")?.value,
                title2 : homeDetails.find( item => item.key === "home_title2")?.value,
                banner: replicaBanner.map( item => item.image)
            },
            features    : homeDetails.find( item => item.key === "features")?.value,
            aboutUs     : homeDetails.find( item => item.key === "about")?.value,
            services    : homeDetails.find( item => item.key === "plan")?.value,
            chooseUs    : homeDetails.find( item => item.key === "why_choose_us")?.value,
            policy      : homeDetails.find( item => item.key === "policy")?.value,
            terms       : homeDetails.find( item => item.key === "terms")?.value,
        }
        const data = {
            replicaOwner    : user,
            replicaHome     : replicaData,
            langId          : langCode,
            registrationUrl : registrationUrl,
            companyDetails  : company
        };
        const response = await successMessage({data: data});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getReplicaHome",error);
        return next(error);
    }
};

export const uploadReplicaContact = async (req,res,next) => {
    try {
        const { referralId, contactData } = req.body;
        const {name, email, address, phone, contactInfo} = contactData;
        const currentDate       = new Date();
        const mailAddedDate     = moment.utc(currentDate).format();
        const referalUserId    = await usernameToid(referralId);
        if(!referalUserId) {
            const response  = await errorMessage({code: 1118, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const result = await Contact.create({
            name: name,
            email: email,
            address: address,
            phone: phone,
            contactInfo: contactInfo,
            ownerId: referalUserId.id,
            status: 1,
            mailAddedDate: mailAddedDate,
            readMsg: 0,
        });
        if (result) {
            const response = await successMessage({ data : "Contact added successfully."});
            return res.status(response.code).json(response.data);
        }

    } catch (error) {
        logger.error("ERROR FROM uploadReplicaContact",error);
        return next(error);
    }
};

// only one entry per userId+type? payment receipts
export const uploadReplicaPaymentReceipt = async (req, res, next) => {
    try {
        let result;
        try {
            result = await uploadFile(req, res);
            logger.info("REPLICA BANK RECEIPT UPLOADED: ",result);
        } catch (error) {
            logger.error("ERROR FROM uploadReplicaPaymentReceipt",error);
            const response = await errorMessage({ code: error.error, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        const receipt        = result.file[0].path;
        const referralId     = req.body.referralId;
        const newUsername    = req.body.username;
        const paymentReceipt = await paymentService.insertIntoPaymentReceipt({ transaction: null, receipt, userId: referralId, type: "replica", pendingRegistrationId: null, username: newUsername, orderId: null });

        const response = await successMessage({ data: {message: result.message, file: result.file[0]}});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM uploadReplicaPaymentReceipt",error);
        return next(error);       
    }
};

export const getReplicaRegister = async (req, res, next) => {
    try {
        if(!req.query.referralId || !req.query.hash || req.query.referralId === '' || req.query.hash === '') {
            const response  = await errorMessage({code: 1114, statusCode: 422});
            res.status(response.code).json(response.data);
        }
        const referralId    = req.query.referralId;
        const hash          = req.query.hash;
        // let { replicaHashKey } = await Configuration.findOne({ attributes: ["replicaHashKey"] });
        let replicaHashKey = process.env.HASH_KEY
        const generatedHash = await makeHash({ param: referralId, hashKey: replicaHashKey});
        if(hash !== generatedHash){
            const response = await errorMessage({code: 1112, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const sponsorId     = await usernameToid(referralId);
        let placementData   = {};
        let regData         = null;
        let regPack         = null;
        const [
            signupSettingsResult,
            signupFieldResults,
            userResult,
            replicaContent,
            usernameConfigResult,
            configurationResult,
            moduleStatusResult,
            passwordPolicyResult,
            companyProfileAndLang,
            defaultCurrency,
            defaultLang,
        ] = await Promise.all([
            signupSettings(),
            SignupField.findAll({ where: { status: 1 } }),
            User.findByPk(sponsorId.id, {
                attributes: ["id", "username", "email", "defaultLang","defaultCurrency"],
                include: [
                    {
                        model: UserDetail,
                        attributes: [
                            [Sequelize.literal("CONCAT(name, ' ', second_name)"), "fullName"],
                        ],
                    },
                    { model: CurrencyDetail, attributes: ['id', 'code', 'symbolLeft', 'value']},
                ],
            }),
            ReplicaContent.findAll({
                attributes:["key","value"],
                where:{key:["policy","terms"]}
            }),
            getUsernameConfig(),
            getConfiguration(),
            getModuleStatus({attributes: ["productStatus","mlmPlan","ecomStatus"]}),
            getPasswordPolicy(),
            appService.getCompanyAndLang(),
            CurrencyDetail.findOne({attributes:["id","code","symbolLeft","value"],where:{status:1,default:1}}),
            defaultLanguage(),
        ]);

        if (moduleStatusResult.ecomStatus) {
            const response = await errorMessage({code: 1034, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        
        const termsAndCondition     = await getTermsAndCondition(userResult.defaultLang);
        let registrationSteps = regSteps;
        if(!moduleStatusResult.productStatus) {
            registrationSteps = regSteps.map(item => item.label === "pick_your_products" ? { ...item, label: "reg_amount" } : item);
        }

        if(moduleStatusResult.productStatus) {
            regPack = await registerService.getRegPackDetails();
            if (!regPack || !regPack.length) {
                const response = await errorMessage({ code: 1092, statusCode: 422 });
                return res.status(response.code).json(response.data);
            }
        } else {
            regData = configurationResult.regAmount;
        }
        const contactInformation = await registerService.getContactAndCustomFields({ authUser: userResult, signupSettings: signupSettingsResult, signupFields: signupFieldResults, defaultLang});
        const loginInformation   = await registerService.getLoginInformation({ authUser: userResult, usernameSettings: usernameConfigResult, passwordPolicy: passwordPolicyResult});

        let paymentGateways = await registerService.getPaymentGateways({ configuration: configurationResult, moduleStatus: moduleStatusResult});
        if(!paymentGateways) {
            const response = await errorMessage({ code: 1093, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }
        // paymentGateways   = paymentGateways.filter( item => item.title !== 'e-pin' || item.title !== 'e-wallet');
        paymentGateways   = _.reject(paymentGateways, ['title', 'e-pin']);
        paymentGateways   = _.reject(paymentGateways, ['title', 'e-wallet']);
        const replicaTermsAndPolicy = replicaContent.reduce((result, row) => {
            result[row.key] = row.value;
            return result;
        }, {});
        const response = await successMessage({ data: { 
            registrationSteps, 
            sponsorData: userResult, 
            placementData, 
            regPack, 
            contactInformation, 
            loginInformation,
            termsAndCondition,
            replicaTerms: replicaTermsAndPolicy,
            modStatus:moduleStatusResult, 
            paymentGateways, 
            currencies: companyProfileAndLang.currencies,
            user: { defaultCurrency: defaultCurrency,
                selectedCurrency: userResult.CurrencyDetail
            },
            regData, 
            regFee:  configurationResult.regAmount}});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getReplicaRegister",error);
        return next(error);
    }
}
export const checkEmailUsername = async (req, res, next) => {
    try {
        if(!req.query.field || req.query.filed === '' || !req.query.value || req.query.value === '') {
            const response = await errorMessage({ code: 1114, statusCode: 422});
            res.status(response.code).json(response.data);
        }
        const field = req.query.field;
        const value = req.query.value;
        if(field !== 'username' && field !== 'email') {
            const response = await errorMessage({ code: 1116, statusCode: 422});
            res.status(response.code).json(response.data);
        }
        const check1 = await User.findOne({ where: { [field]: value } });
        const check2 = await PendingRegistration.findOne({ where: { [field]: value } });
        if (!check1 && !check2) {
            const response = await successMessage({ data: {status: 1, field}})
            return res.status(response.code).json(response.data);
        }
        const response = await errorMessage({ code: 1117, statusCode: 422});
        response.data.data.field = field;
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM checkEmailUsername",error);
        return next(error);
    }
}

export const deleteReplicaPaymentReceipt = async (req,res,next) => {
    try {
        let file = req.body.filepath;
        
        // varies depending on how app is run
        const __dirname     = path.dirname(new URL(import.meta.url).pathname);
        const filePath = path.join(__dirname, "../", "../","uploads/register/replica/", file);
        const imagePath = process.env.IMAGE_URL + 'uploads/register/replica/' + file;
        logger.info("filePath",filePath,"imagePath",imagePath)
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
        logger.error("ERROR FROM deleteReplicaPaymentReceipt",error);
        return next(error);
    }
}

export const replicaRegister = async (req,res,next) => {
    try {
        const sponsorId   = req.body.referralId;
        let position      = req.body.position;
        let legPosition   = position;
        const prefix      = req.prefix+"_";
        const ip          = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        const regFromTree = false;
        let productData;
        let placementData;
        let paymentData = {};

        const [ moduleStatus, config, userData, checkUsernameEmail ] = await Promise.all([
            getModuleStatus({attributes: ["productStatus","mlmPlan","roiStatus","subscriptionStatus"]}),
            Configuration.findOne(),
            User.findByPk(req.body.referralId, { attributes: ["id","username","userLevel"], include:["Language"]}),
            registerService.checkUsernameAndEmail({ username: req.body.username, email: req.body.email})
        ]);
        paymentData.regAmount = config.regAmount;

        if(!checkUsernameEmail){
            const response = await errorMessage({ code: 1098, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        if(moduleStatus.productStatus) {
            productData = await Package.findOne({ where: { id: req.body.product.id, type: "registration"}});
            paymentData.productAmount = productData.price;
        }
        paymentData.totalAmount = paymentData.productAmount 
            ? Number(paymentData.regAmount) + Number(paymentData.productAmount) 
            : Number(paymentData.regAmount) ;
        logger.info("paymentData",paymentData)

        if(moduleStatus.mlmPlan === "Binary") {
            legPosition     = (position === "L") ? 1 : 2;
            placementData   = await registerService.getBinaryPlacement({ sponsor: userData, prefix, legPosition, regFromTree});
        } else if(moduleStatus.mlmPlan === "Unilevel") {
            placementData   = await registerService.getUnilevelPlacement({ sponsor: userData, prefix, legPosition, regFromTree});
        } else if(moduleStatus.mlmPlan === "Matrix") {
            placementData = await registerService.getMatrixPlacement({sponsor: userData, prefix, legPosition, regFromTree, config });
        }
        logger.debug("PLACEMENT DATA:- \n",placementData);

        const [ fatherData, sponsorData, signUpSettingsData, paymentGateway, userPaymentType ] = await Promise.all([
            User.findByPk(placementData.fatherId, {
                attributes: ["id", "username", "fatherId", "sponsorId", "userLevel", "sponsorLevel"],
                include: [{
                        model: User, 
                        as: "userAncestorData",
                        attributes: ["id", "username", "userLevel", "sponsorLevel", "groupPv"],
                        through: {
                            attributes: ["depth"]
                        }
                    },
                    {
                        model: UserDetail,
                        attributes: ["name", "secondName"]
                    }
                ]
            }),
            User.findByPk(userData.id, {
                attributes: ["id", "username", "fatherId", "sponsorId", "userLevel", "sponsorLevel", "groupPv"],
                include: [
                    {
                        model: User, 
                        as: "userUnilevelAncestorData",
                        attributes: ["id", "username", "userLevel", "sponsorLevel", "groupPv"],
                        through: {
                            attributes: ["depth"]
                        }
                    },
                    {
                        model: UserDetail,
                        attributes: ["name", "secondName"]
                    },
                    {
                        model: User,
                        as: 'downline',
                        attributes: ["id", "username"]
                    }
                ]
            }),
            SignupSettings.findOne(),
            PaymentGatewayConfig.findAll(),
            PaymentGatewayConfig.findByPk(req.body.paymentType)
        ]);

        if (!userPaymentType) {
            const response = await errorMessage({ code: 1036, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        let packageValidity = "";
        if(productData) {
            packageValidity = await registerService.getPackageValidity({ addMonth: productData.validity});
        }

        const fatherAncestors   = fatherData.userAncestorData;
        const sponsorAncestors  = sponsorData.userUnilevelAncestorData;

        let dbTransaction;
        let newUser;
        try {
            dbTransaction = await sequelize.transaction();
            if (userPaymentType.regPendingStatus) {
                const pendingUser = await registerService.addToPendingRegistration({regData: req.body, sponsorData, placementData, moduleStatus, signUpSettingsData,regFromTree, config, transaction: dbTransaction});
                logger.info("NEW PENDING USER ID",pendingUser.id);

                const receipt = await paymentService.updatePaymentReceipt({ transaction: dbTransaction, pendingUserId: pendingUser.id, type: 'register', username: req.body.username});
                if (req.body.paymentType == 4) {
                    if (!receipt) {
                        await dbTransaction.rollback();
                        logger.error("ERROR FROM replicaRegister Receipt update");
                        const response = await errorMessage({ code: 1082, statusCode: 422 });
                        return res.status(response.code).json(response.data);
                    }
                }
                if (moduleStatus.productStatus) {
                    const invoiceNo = await generateSalesInvoiceNumber();
                    await registerService.addToSalesOrder({user: pendingUser, invoiceNo, pendingStatus: userPaymentType.regPendingStatus, regData: req.body, paymentData, transaction: dbTransaction });
                }
                if (moduleStatus.roiStatus && moduleStatus.productStatus) {
                    await registerService.insertRoi({ user: pendingUser, product: req.body.product, paymentType: req.body.paymentType, transaction: dbTransaction});
                }
                newUser = pendingUser;
            } else {
                newUser = await registerService.addToUserTable({ 
                    regData: req.body, placementData, fatherData, sponsorData, 
                    packageValidity, mlmPlan: moduleStatus.mlmPlan, productStatu: moduleStatus.productStatus, 
                    subscriptionStatus: moduleStatus.subscriptionStatus, productData,
                    transaction : dbTransaction
                });
                logger.debug("NEW USER ID",newUser.id);

                await registerService.managePayment({req, res, next, newUser, userPaymentType, regData: req.body, sponsorData, moduleStatus,  transaction: dbTransaction});

                await Promise.all([
                    registerService.insertTreePath({ ancestors: fatherAncestors, newUser, transaction: dbTransaction}),
                    registerService.addToUserDetails({newUser, regData: req.body, signUpSettings:signUpSettingsData, paymentGateway, sponsorData, transaction: dbTransaction}),
                    registerService.addToRegistrationDetails({newUser, regData: req.body, signUpSettings: signUpSettingsData, paymentGateway, sponsorData, paymentData, transaction: dbTransaction}),
                    registerService.addTransactionPassword({ newUser, transaction:dbTransaction }),
                    registerService.addToSponsorTreePath({ ancestors: sponsorAncestors, newUser, transaction: dbTransaction}),
                    registerService.addToUserBalance({ newUser, transaction: dbTransaction }),
                    registerService.updateGroupPv({ newUser, pv: req.body.pv, sponsorUplines: sponsorAncestors, action: "registration", transaction: dbTransaction})
                ]);
                if(req.body.customFields && req.body.customFields.length) {
                    await registerService.addCustomFieldData({ newUser, customFields: req.body.customFields, transaction: dbTransaction });
                }
                if(moduleStatus.mlmPlan === "Binary") {
                    await registerService.addLegDetails({ newUser, transaction: dbTransaction });
                } else if(moduleStatus.mlmPlan === "Stair_Step") {
                    // TODO stairsetp entry
                }
                if(moduleStatus.productStatus) {
                    const invoiceNo = await generateSalesInvoiceNumber();
                    await registerService.addToSalesOrder({user: newUser, invoiceNo, pendingStatus: userPaymentType.regPendingStatus, regData: req.body, paymentData, transaction: dbTransaction });
                }
                
                if (moduleStatus.roiStatus && moduleStatus.productStatus) {
                    await registerService.insertRoi({ user: newUser, product: req.body.product, paymentType: req.body.paymentType, transaction: dbTransaction});
                }
            }
            await dbTransaction.commit();
        } catch (error) {
            logger.error("ERROR FROM replicaRegister",error);
            await dbTransaction.rollback();
            throw error;
        }
        if (!userPaymentType.regPendingStatus) {
            try {
                const activityData = req.body;
                await utilityService.insertUserActivity({
                    userId: sponsorId,
                    userType: "user",
                    data: JSON.stringify(activityData),
                    description: `${newUser.username} added.`,
                    ip: ip,
                    activity: "New user registered",
                    // transaction: None
                });
                console.log(`-----COMMISSION CALL NEW USER ID: ${newUser.id}-----`);
                const commData = {
                    userId: newUser.id,
                    productId: newUser.productId,
                    productPv: newUser.personalPv,
                    productAmount: req.body.totalAmount,
                    // orderId: orderId,
                    // ocOrderId: null,
                };
                const prefix = req.prefix;
                await commissionService.commissionCall(prefix, newUser.id, commData, "register");
            } catch (error) {
                logger.error("Commission or acivity error:- \n", error);
                // throw error;
            }
        };
        const response = await successMessage({data:"Registration success"});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM replicaRegister",error);
        return next(error);
    }
    
}
export const createPaymentIntent = async (req, res, next) => {
    try {
      const { amount, desc, email, type } = req.body;
      const data = await paymentService.createStripePaymentIntent(
        amount,
        desc,
        email,
        type
      );
      const response = await successMessage({ data });
      return res.status(response.code).json(response.data);
    } catch (error) {
      console.log(error);
      return next(error);
    }
  };