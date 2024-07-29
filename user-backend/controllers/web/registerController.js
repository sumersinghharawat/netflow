import bcrypt from "bcryptjs";
import { Sequelize } from "sequelize";
import { sequelize } from "../../config/db.js";
import { logger, errorMessage, consoleLog, successMessage, convertTolocal } from "../../helper/index.js";
import { getModuleStatus, signupSettings, getUsernameConfig, 
    getConfiguration, getPasswordPolicy, usernameToid, getTermsAndCondition, defaultLanguage, generateSalesInvoiceNumber } from "../../utils/index.js";
import regSteps from "../../utils/regSteps.js";

import commissionService from "../../services/commissionService.js";
import registerService from "../../services/registerService.js";
import utilityService from "../../services/utilityService.js";
import { CompanyProfile, Configuration, Package, PaymentGatewayConfig, PendingRegistration, SignupField, SignupSettings, User, UserBalanceAmount, UserDetail, UserPlacement } from "../../models/association.js";
import _ from "lodash";
import PaymentService from "../../services/paymentService.js";
import Letterconfig from "../../models/letterconfig.js";
import mailService from "../../services/mailService.js";


export const getRegister = async (req, res, next) => {
    try {
        const authUserId          = req.auth.user.id;
        const father              = req.query.father ?? false;
        const position            = req.query.position ?? false; 
        const regFromTree         = father && position;
        const queryLen            = Object.keys(req.query).length > 0;          
        let regPack               = null;
        let regData               = null;
        let placementData         = {};

        const [
            signupSettingsResult,
            signupFieldResults,
            userResult,
            usernameConfigResult,
            configurationResult,
            moduleStatusResult,
            passwordPolicyResult,
            termsAndCondition,
            defaultLang
        ] = await Promise.all([
            signupSettings(),
            SignupField.findAll({ where: { status: 1 } }),
            User.findByPk(authUserId, {
                attributes: ["id", "username", "email", "defaultLang"],
                include: [
                    {
                        model: UserDetail,
                        attributes: [
                            [Sequelize.literal("CONCAT(name, ' ', second_name)"), "fullName"],
                            "image"
                        ],
                    },
                ],
            }),
            getUsernameConfig(),
            getConfiguration(),
            getModuleStatus({attributes: ["productStatus","mlmPlan"]}),
            getPasswordPolicy(),
            getTermsAndCondition(req.auth.user.defaultLang),
            defaultLanguage()
        ]);
        
        let registrationSteps = regSteps;
        if(!moduleStatusResult.productStatus) {
            registrationSteps = regSteps.map(item => item.label === "pick_your_products" ? { ...item, label: "reg_amount" } : item);
        }

        if(regFromTree) {
            const fatherData    = await usernameToid(father);
            if (!fatherData) {
                const response = await errorMessage({ code: 1009, statusCode: 422 });
                return res.status(response.code).json(response.data);
            }
            const checkPlacement = await registerService.checkPositionAvilable({mlmPlan: moduleStatusResult.mlmPlan, authUser: userResult, fatherData, position, width:configurationResult.widthCeiling });
            if (!checkPlacement.status) {
                const response = await errorMessage({ code: checkPlacement.code, statusCode: 422 });
                return res.status(response.code).json(response.data);
            }

            placementData = fatherData;
            placementData.username = fatherData.username.toUpperCase();
            placementData.position = (moduleStatusResult.mlmPlan === "Binary") ? (position === 1 ? "L" : "R") : null;
        }
        if(moduleStatusResult.productStatus && !moduleStatusResult.ecomStatus) {
            regPack = await registerService.getRegPackDetails();
            if (!regPack || !regPack.length) {
                const response = await errorMessage({ code: 1092, statusCode: 422 });
                return res.status(response.code).json(response.data);
            }
        } else {
            regData = configurationResult.regAmount;
        }
        const [
            contactInformation,
            loginInformation,
            paymentGateways
        ] = await Promise.all([
            registerService.getContactAndCustomFields({ authUser: userResult, signupSettings: signupSettingsResult, signupFields: signupFieldResults, defaultLang}),
            registerService.getLoginInformation({ authUser: userResult, usernameSettings: usernameConfigResult, passwordPolicy: passwordPolicyResult}),
            registerService.getPaymentGateways({ configuration: configurationResult, moduleStatus: moduleStatusResult})
        ]);

        if(!paymentGateways) {
            const response = await errorMessage({ code: 1093, statusCode: 422 });
            return res.status(response.code).json(response.data);
        }

        let validEpins = null;
        if (paymentGateways.filter( gateway => gateway.title === "e-pin").length > 0) {
            validEpins = await registerService.getValidEpins(authUserId);
        }
        const response = await successMessage({ data: { registrationSteps, sponsorData: userResult, placementData, regPack, contactInformation, loginInformation,termsAndCondition, paymentGateways, validEpins, regData }});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getRegister",error);
        return next(error);
    }
};

export const registerFieldVerification = async (req,res,next) => {
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
            const response = await successMessage({ data: {status: 1, field:field}});
            return res.status(response.code).json(response.data);
        }
        const response = await errorMessage({ code: 1117, statusCode: 422});
        response.data.data.field = field;
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM registerFieldVerification",error);
        return next(error);
    }
};

export const registerUser = async (req, res, next) => {
    try{
        const prefix        = req.prefix+"_";
        let position        = req.body.position;
        let placement       = req.body.placement;
        let legPosition     = position;
        let regFromTree     = req.body.regFromTree ? true : false;
        const ip            = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
        let placementData;
        let productData;
        let paymentData = {};

        const [ moduleStatus, config, userData, checkUsernameEmail ] = await Promise.all([
            getModuleStatus({attributes: ["productStatus","mlmPlan","roiStatus","subscriptionStatus"]}),
            Configuration.findOne(),
            User.findByPk(req.auth.user.id, { attributes: ["id","username","userLevel"], include:["Language"]}),
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
            placementData   = await registerService.getBinaryPlacementFromUserPlacement({ sponsor: userData, prefix, legPosition, regFromTree});
        } else if(moduleStatus.mlmPlan === "Unilevel") {
            placementData   = await registerService.getUnilevelPlacement({ sponsor: userData, prefix, legPosition, regFromTree});
        } else if(moduleStatus.mlmPlan === "Matrix") {
            placementData = await registerService.getMatrixPlacement({sponsor: userData, prefix, legPosition, regFromTree, config });
        }

        if(regFromTree && !placementData) {
            const placementUserDetails = await User.findOne({ where: { username: placement} });
            if(placementUserDetails && await registerService.checkPlacementAvailable({ mlmPlan: moduleStatus.mlmPlan, placementUserDetails, position, sponsor: userData})) {
                placementData = {
                    fatherId : placementUserDetails.id,
                    fatherUsername : placementUserDetails.username,
                    positionString : parseInt(legPosition)
                };
            } else {
                const response = await errorMessage({ code: 1009, statusCode: 422 });
                return res.status(response.code).json(response.data);
            }
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
                attributes: ["id", "username", "fatherId", "sponsorId", "userLevel", "sponsorLevel", "groupPv","legPosition"],
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
                
                if (req.body.paymentType == 4) {
                    const receipt = await PaymentService.updatePaymentReceipt({ transaction: dbTransaction, pendingUserId: pendingUser.id, type: 'register', username: req.body.username});
                    if (!receipt) {
                        await dbTransaction.rollback();
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
                    packageValidity, mlmPlan: moduleStatus.mlmPlan, productStatus: moduleStatus.productStatus, 
                    subscriptionStatus: moduleStatus.subscriptionStatus, productData,
                    transaction : dbTransaction
                });
                logger.debug("NEW USER ID",newUser.id);
                logger.debug("Placement: ",placementData);
                const payment = await registerService.managePayment({req, res, next, newUser, userPaymentType, regData: req.body, sponsorData, moduleStatus,  transaction: dbTransaction});
                if (!payment) {
                    await dbTransaction.rollback();
                    const response = await errorMessage({ code: 1036, statusCode: 422 });
                    return res.status(response.code).json(response.data);    
                }
                if(moduleStatus.mlmPlan === 'Binary') {
                    await registerService.setUserPlacementData({sponsorData,legPosition, placementData, newUser, transaction: dbTransaction, regFromTree});
                }
                await Promise.all([
                    registerService.insertTreePath({ ancestors: fatherAncestors, newUser, transaction: dbTransaction}),
                    registerService.addToUserDetails({newUser, regData: req.body, signUpSettings: signUpSettingsData, paymentGateway, sponsorData, transaction: dbTransaction}),
                    registerService.addToRegistrationDetails({newUser, regData: req.body, signUpSettings: signUpSettingsData, paymentGateway, sponsorData, paymentData, transaction: dbTransaction}),
                    registerService.addTransactionPassword({ newUser, transaction:dbTransaction }),
                    registerService.addToSponsorTreePath({ ancestors: sponsorAncestors, newUser, transaction: dbTransaction}),
                    registerService.addToUserBalance({ newUser, transaction: dbTransaction }),
                    // registerService.updateGroupPv({ newUser, pv: req.body.pv, sponsorUplines: sponsorAncestors, action: "registration", transaction: dbTransaction})
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
            logger.error("ERROR IN REGISTER:- \n", error);
            await dbTransaction.rollback();
            throw error;
        }
        if(parseInt(signUpSettingsData.mailNotification) && req.body.email) {
            const toData = {
                name    : req.body.first_name,
                fullName: (req.body.last_name) ? `${req.body.first_name} ${req.body.last_name}` : `${req.body.first_name}`,
                to      : req.body.email
            }
            const mailType = signUpSettingsData.emailVerification ? 'registration_email_verification' : 'registration';
            await mailService.sentNotificationMail({mailType, authUser: req.auth.user, toData});
        }
        if (!userPaymentType.regPendingStatus) {
            try {
                const activityData = req.body;
                utilityService.insertUserActivity({
                    userId: req.auth.user.id,
                    userType: "user",
                    data: JSON.stringify(activityData),
                    description: `${newUser.username} added.`,
                    ip: ip,
                    activity: "New user registered",
                });
                console.log(`-----COMMISSION CALL NEW USER: ${newUser.id}-----`);
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
            }
        };
        const response = await successMessage({data: {newUser, letterPreview: userPaymentType.regPendingStatus}});
        return res.status(response.code).json(response.data);  
    } catch (error) {
        logger.error("ERROR FROM registerUser",error);
        return next(error);
    }
};

export const checkTransactionPassword = async (req,res,next) => {
    try {
        const password      = req.body.transPassword;
        const totalAmount   = req.body.totalAmount;
        const userId        = req.auth.user.id;

        if(!password) {
            const response = await errorMessage({ code: 1015, statusCode: 422 });
            return res.status(response.code).json(response.data);  
        }
        if(!totalAmount) {
            const response = await errorMessage({ code: 1057, statusCode: 422 });
            return res.status(response.code).json(response.data);  
        }
        const user = await User.findOne({
            where: {id: req.auth.user.id},
            include:["TransactionPassword"],
        });
        const userTransPassword = user.TransactionPassword.password;
        if(!await bcrypt.compare(password, userTransPassword)){
            const error = await errorMessage({ code: 1015, statusCode: 422 });
            return res.status(error.code).json(error.data);  
        }

        const userBalance = await UserBalanceAmount.findOne({where: { userId: userId }});
        if (parseFloat(totalAmount) > parseFloat(userBalance.balanceAmount)) {
            const error = await errorMessage({ code: 1014, statusCode: 422 });
            return res.status(error.code).json(error.data);  
        }

        const response = await successMessage({data: "Valid Ewallet."});
        return res.status(response.code).json(response.data);  
    } catch (error) {
        logger.error("ERROR FROM checkTransactionPassword",error);
        return next(error);
    }
};

export const getEcomRegisterLink = async (req, res, next) => {
    try {
        const user = req.auth.user;
        const userString = await utilityService.getRandomString(user.id);
        const regFromTree = req.query.regFromTree ? parseInt(req.query.regFromTree) : false;
        let response;
        if(!regFromTree) {
            response = await successMessage({data: {link: `${process.env.STORE_URL}index.php?route=account/login&db_prefix=${req.prefix}&string=${userString}&register=1`}});
        } else {
            const position      = req.query.position;
            const placement     = req.query.placement ? req.query.placement : req.auth.user.username;
            const placementId   = await usernameToid(placement);
            const placementUserDetails = (placementId) ? await User.findByPk(placementId.id) : false;
            if(!placementUserDetails) {
                const error = await errorMessage({ code: 1009, statusCode: 422 });
                return res.status(error.code).json(error.data);  
            }
            // TODO Check placement available 
            response = await successMessage({data: {link: `${process.env.STORE_URL}index.php?route=account/login&db_prefix=${req.prefix}&string=${userString}&register=1&username=${placement}&position=${position}&reg_from_tree=1`}});
        }
        return res.status(response.code).json(response.data);  
    } catch (error) {
        logger.error("ERROR FROM getEcomRegisterLink",error);
        return next(error);
    }
};

export const getEcomStoreLink = async (req, res, next) => {
    try {
        const user = req.auth.user;
        const userString = await utilityService.getRandomString(user.id);
        const response = await successMessage({data: {link: `${process.env.STORE_URL}index.php?route=account/login&db_prefix=${req.prefix}&string=${userString}&store=1`}});
        return res.status(response.code).json(response.data);  
    } catch (error) {
        logger.error("ERROR FROM getEcomStoreLink",error);
        return next(error);
    }
};

export const letterPreview = async (req, res, next) => {
    if(!req.query.username || req.query.username === "" || req.query.username === null) {
        const response = await errorMessage({ code: 1107, statusCode: 422})
        return res.status(response.code).json(response.data);
    }
    const username          = req.query.username;
    const checkPending      = await PendingRegistration.findOne({ where: { username, status:'pending' }, include:[{ model: Package, required: false}]});

    if(!checkPending) {
        const response = await errorMessage({ code: 1107, statusCode: 422})
        return res.status(response.code).json(response.data);
    }
    const userRegData       = JSON.parse(checkPending.data);
    const userData          = {
        username : checkPending.username,
        fullName : userRegData.first_name,
        gender   : userRegData.gender ?? '',
        email    : checkPending.email,
        sponsorName : userRegData.sponsorFullname
    };
    const moduleStatus      = await getModuleStatus({attributes:["productStatus"]});
    let productData = {};
    if(moduleStatus.productStatus) {
        productData = checkPending.Package
    }
    
    const companyData       = await CompanyProfile.findOne();
    const welcomeLetter     = await Letterconfig.findAll();
    welcomeLetter.forEach( item => item.content.replace(new RegExp(companyData.name, 'g'), ':company'));
    const response = await successMessage({ data: {totalAmount: userRegData.totalAmount, regFee: userRegData.reg_amount, companyData,userData, productData, welcomeLetter, date: convertTolocal(checkPending.createdAt)}});
    return res.status(response.code).json(response.data);

}