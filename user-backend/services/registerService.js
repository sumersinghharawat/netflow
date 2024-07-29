import _ from "lodash";
import bcrypt from "bcryptjs";
import { Op } from "sequelize";
import { sequelize } from "../config/db.js";
import {
    consoleLog,
    convertToUTC,
    errorMessage,
    logger,
} from "../helper/index.js";
import getDynamicUsername from "../utils/getDynamicUsername.js";
import { verifyTransactionPassword } from "../utils/index.js";
import paymentService from "./paymentService.js";
import {
    Configuration,
    Country,
    CustomfieldLang,
    CustomfieldValue,
    LegDetail,
    Package,
    PaymentGatewayConfig,
    PendingRegistration,
    PinNumber,
    RoiOrder,
    SalesOrder,
    SponsorTreepath,
    State,
    SubscriptionConfig,
    TransactionPassword,
    Treepath,
    User,
    UserBalanceAmount,
    UserDetail,
    UsersRegistration,
    UserPlacement
} from "../models/association.js";
import UsernameConfig from "../models/usernameConfig.js";
import moment from "moment-timezone";
class RegisterService {
    async getContactAndCustomFields({
        authUser,
        signupSettings,
        signupFields,
        defaultLang,
    }) {
        let [contactField, customField, options] = [[], []];
        let countries = await Country.findAll({ include: State });
        for await (let [key, value] of Object.entries(signupFields)) {
            if (!value.isCustom) {
                contactField[key] = {
                    code: value.name,
                    type: value.type,
                    value: value.defaultValue,
                    required: value.required,
                    editable: value.editable,
                };
            }
            switch (value.name) {
                case "gender":
                    options = [
                        { code: "male", title: "Male", value: "M" },
                        { code: "female", title: "Female", value: "F" },
                        { code: "other", title: "Others", value: "O" },
                    ];
                    contactField[key]["options"] = [...options];
                    break;
                case "date_of_birth":
                    options = [
                        { validation: { ageLimit: signupSettings.ageLimit } },
                    ];
                    contactField[key]["options"] = [...options];
                    break;
                case "country":
                    options = [...countries];
                    contactField[key]["options"] = [...options];
                case "state":
                    options = [...countries];
                    contactField[key]["options"] = [...options];
            }
            if (value.isCustom) {
                const userLangId = authUser.defaultLang
                    ? authUser.defaultLang
                    : defaultLang.id;
                let customLang = await CustomfieldLang.findOne({
                    where: { languageId: userLangId, customfieldId: value.id },
                });

                customField.push({
                    id: value.id,
                    code: value.name,
                    type: value.type,
                    required: value.required,
                    isEditable: value.editable,
                    value: customLang?.value,
                });
            }
        }
        let contactAndCustomField = { contactField, customField };
        return contactAndCustomField;
    }

    async checkUsernameValidity(username) {
        if (/^[a-zA-Z0-9]*$/.test(username)) {
            const checkUser = await User.findOne({
                where: { username: username },
            });
            if (checkUser) {
                const checkPendingUser = await PendingRegistration.findOne({
                    where: { username: username },
                });
                return !checkPendingUser;
            } else return false;
        } else return false;
    }
    async getRegPackDetails() {
        return await Package.findAll({
            where: { type: "registration", active: 1 },
        });
    }
    async getLoginInformation({ authUser, usernameSettings, passwordPolicy }) {
        let loginField = [];
        if (usernameSettings.userNameType == "dynamic") {
            let dynamicUsername = await getDynamicUsername(next,usernameSettings);
            let usernameField = {
                code: "username",
                type: "text",
                value: dynamicUsername,
            };
            loginField.push(usernameField);
        } else {
            let length = usernameSettings.length.split(";");
            let usernameField = {
                code: "username",
                type: "text",
                validation: {
                    min: parseInt(length[1]),
                    max: parseInt(length[0]),
                },
            };
            loginField.push(usernameField);
        }
        let passwordField = {
            code: "password",
            type: "password",
            validation: passwordPolicy.enablePolicy
                ? {
                      mixedCase: passwordPolicy.mixedCase,
                      number: passwordPolicy.number,
                      spChar: passwordPolicy.spChar,
                      minLength: passwordPolicy.minLength,
                  }
                : { minLength: passwordPolicy.minLength },
        };
        loginField.push(passwordField);
        return loginField;
    }
    async getPaymentGateways({ configuration, moduleStatus }) {
        let paymentField = [];
        if (configuration.regAmount == 0 && !moduleStatus.productStatus) {
            return false;
        }
        let paymentMethods = await PaymentGatewayConfig.findAll({
            attributes: ["id", "name", "slug", "status", "logo"],
            where: { status: 1, registration: 1 },
        });
        Object.entries(paymentMethods).map(([key, value]) => {
            paymentField[key] = {
                id: value.id,
                code: value.name,
                title: value.slug,
                logo: value.logo,
            };
            switch (value.slug) {
                case "e-pin":
                    paymentField[key].icon = "fa-solid fa-bolt";
                    break;
                case "e-wallet":
                    paymentField[key].icon = "fa-solid fa-wallet";
                    break;
                case "free-joining":
                    paymentField[key].icon = "fa-regular fa-id-card";
                    break;
                case "bank-transfer":
                    paymentField[key].icon = "fa-solid fa-paper-plane";
                    break;
                case "stripe":
                    paymentField[key].icon = "fa-brands fa-cc-stripe";
                    break;
                case "paypal":
                    paymentField[key].icon = "fa-brands fa-paypal";
                    break;
                default:
                    break;
            }
        });
        return paymentField;
    }

    async getValidEpins(userId) {
        return await PinNumber.findAll({
            attributes: ["id", "numbers", "balanceAmount", "expiryDate"],
            where: {
                allocatedUser: userId,
                status: "active",
            },
        });
    }

    async checkPositionAvilable({
        mlmlPlan,
        authUser,
        fatherData,
        position,
        width,
    }) {
        const checkFatherIsUnderYou = await Treepath.findOne({
            where: { ancestor: authUser.id, descendant: fatherData.id },
        });
        if (!checkFatherIsUnderYou) {
            return { status: false, code: 1094 };
        }

        let whererCondition = [{ father_id: fatherData.id }];
        if (mlmlPlan === "Binary") {
            whererCondition.push({ position });
        }
        const check = await User.findAndCountAll({ where: whererCondition });
        if (mlmlPlan === "Binary") {
            return {
                status: !check.count,
                code: check.count < width ? false : 1009,
            };
        } else if (mlmlPlan === "Matrix") {
            return {
                status: check.count < width,
                code: check.count < width ? false : 1009,
            };
        }
        return { status: true, code: false };
    }

    async getBinaryPlacementFromUserPlacement({ sponsor, prefix, legPosition, regFromTree }) {
        if (regFromTree) {
            return false;
        }
        const sponsorData   = await UserPlacement.findOne({ 
                                where: { userId: sponsor.id },
                                attributes: ['userId', 'branchParent', 'leftMost','rightMost'],
                                include:[
                                    {
                                        model: User,
                                        as: 'user',
                                        attributes: ['id', 'legPosition', 'position', 'username'],
                                    },
                                    {
                                        model: User,
                                        as: 'parent',
                                        attributes: ['id', 'legPosition', 'position', 'username'],
                                    },
                                    {
                                        model: User,
                                        as: 'left',
                                        attributes: ['id', 'legPosition', 'position', 'username']
                                    },
                                    {
                                        model: User,
                                        as: 'right',
                                        attributes: ['id', 'legPosition', 'position', 'username']
                                    }
                                ]
                            });
        let branchParent  = sponsorData.user;
        let leftMost      = (sponsorData.left) ? sponsorData.left.id : sponsorData.user.id;
        let rightMost     = (sponsorData.right) ? sponsorData.right.id : sponsorData.user.id;
        let leftMostUsername  = (sponsorData.left) ? sponsorData.left.username : sponsorData.user.username;
        let rightMostUsername = (sponsorData.right) ? sponsorData.right.username : sponsorData.user.username;
        if(sponsorData.parent && (parseInt(sponsorData.parent.legPosition) === parseInt(legPosition))) {
            branchParent  = sponsorData.parent;
        }
        // checking sponsor has parent or not
        if(branchParent && (parseInt(legPosition) === parseInt(sponsorData.user.legPosition))) {
            branchParent = await UserPlacement.findOne({ 
                where: { userId: sponsorData.parent.id },
                attributes: ['userId', 'branchParent', 'leftMost','rightMost'],
                include:[
                    {
                        model: User,
                        as: 'user',
                        attributes: ['id', 'legPosition', 'position', 'username'],
                    },
                    {
                        model: User,
                        as: 'parent',
                        attributes: ['id', 'legPosition', 'position', 'username'],
                    },
                    {
                        model: User,
                        as: 'left',
                        attributes: ['id', 'legPosition', 'position', 'username']
                    },
                    {
                        model: User,
                        as: 'right',
                        attributes: ['id', 'legPosition', 'position', 'username']
                    }
                ]
            });
            leftMost      = (branchParent.left) ? branchParent.left.id : branchParent.user.id;
            rightMost     = (branchParent.right) ? branchParent.right.id : branchParent.user.id;
            leftMostUsername   = (branchParent.left) ? branchParent.left.username : branchParent.user.username;
            rightMostUsername  = (branchParent.right) ? branchParent.right.username : branchParent.user.username;
            branchParent  = branchParent.user;
        }
        const nodeParent    = branchParent;
        return {
            fatherId        : (parseInt(legPosition) === 1) ? leftMost : rightMost,
            fatherUsername  : (parseInt(legPosition) === 1) ? leftMostUsername : rightMostUsername,
            positionString  : parseInt(legPosition),
            nodeParentData  : nodeParent,
            parentIsSponsor : !(branchParent && (parseInt(legPosition) === parseInt(sponsorData.user.legPosition)))
        };
    }
    // async getBinaryPlacement({ sponsor, prefix, legPosition, regFromTree }) {
    //     if (regFromTree) {
    //         return false;
    //     }
    //     let query = `SELECT ANY_VALUE(downline.username) AS father, ANY_VALUE(downline.id) AS id, GROUP_CONCAT(DISTINCT (upline.leg_position) SEPARATOR '') AS positionString FROM ${prefix}treepaths`;
    //     query += ` JOIN ${prefix}users AS downline ON downline.id = ${prefix}treepaths.descendant \n
    //             JOIN ${prefix}treepaths AS tpa ON tpa.descendant = ${prefix}treepaths.descendant \n
    //             JOIN ${prefix}users AS upline ON upline.id = tpa.ancestor \n
    //             LEFT JOIN ${prefix}users AS father ON downline.id = father.father_id AND father.leg_position = :legPosition \n
    //             WHERE ${prefix}treepaths.ancestor = :sponsorId \n
    //             AND upline.user_level > :sponsorUserLevel \n
    //             AND father.username is Null \n
    //             GROUP BY ${prefix}treepaths.descendant \n
    //             HAVING positionstring = :legPosition`;
    //     const data = await sequelize.query(query, {
    //         replacements: {
    //             legPosition: legPosition,
    //             sponsorUserLevel: sponsor.userLevel,
    //             sponsorId: sponsor.id,
    //         },
    //         type: sequelize.QueryTypes.SELECT,
    //     });
    //     return {
    //         fatherId: data.length ? data[0].id : sponsor.id,
    //         fatherUsername: data.length ? data[0].father : sponsor.username,
    //         positionString: data.length
    //             ? parseInt(data[0].positionString)
    //             : parseInt(legPosition),
    //     };
    // }


    async getUnilevelPlacement({ sponsor, prefix, legPosition, regFromTree }) {
        if (regFromTree) {
            return false;
        }
        const children = await User.findAndCountAll({
            where: {
                fatherId: sponsor.id,
            },
        });
        return {
            fatherId: sponsor.id,
            fatherUsername: sponsor.username,
            positionString: children.count + 1,
        };
    }

    async getMatrixPlacement({
        sponsor,
        prefix,
        legPosition,
        regFromTree,
        config,
    }) {
        const widthCeiling = config.widthCeiling;
        if (regFromTree) {
            return false;
        }

        let query = `SELECT ft.id, ft.username, (SELECT COUNT(*) FROM ${prefix}users WHERE father_id = ft.id) AS leg_count, GROUP_CONCAT(fta.leg_position ORDER BY fta.user_level ASC SEPARATOR ',' ) AS orderColumn \n
            FROM ${prefix}treepaths as tp \n
            JOIN ${prefix}users as ft 
                ON tp.descendant = ft.id \n
            JOIN ${prefix}treepaths as tpa 
                ON tpa.descendant = tp.descendant \n
            JOIN ${prefix}users as fta 
                ON fta.id = tpa.ancestor \n
            WHERE tp.ancestor = :sponsorId AND fta.user_level >= :sponsorUserLevel \n
            GROUP BY tp.descendant \n
            HAVING leg_count < :widthCeiling \n
            ORDER BY ft.user_level, orderColumn LIMIT 1;`;

        const data = await sequelize.query(query, {
            replacements: {
                widthCeiling: widthCeiling,
                sponsorUserLevel: sponsor.userLevel,
                sponsorId: sponsor.id,
            },
            type: sequelize.QueryTypes.SELECT,
        });
        // logger.info(data)

        return {
            fatherId: data.length ? data[0].id : sponsor.id,
            fatherUsername: data.length ? data[0].username : sponsor.username,
            positionString: data.length ? data[0].leg_count + 1 : 1,
            // orderColumn: null,
        };
    }

    async getPackageValidity({ currentValidity = new Date(), addMonth }) {
        const validityDate = new Date(currentValidity);
        validityDate.setMonth(validityDate.getMonth() + parseInt(addMonth));
        return validityDate;
    }

    async addToUserTable({
        regData,
        placementData,
        fatherData,
        sponsorData,
        packageValidity,
        mlmPlan,
        productStatus,
        subscriptionStatus,
        productData,
        transaction,
    }) {
        try {
            if (!mlmPlan) return false;
    
            const options = transaction ? { transaction } : {};
            let productValidity = null;
            let legPosition = parseInt(placementData.positionString);
            let position = legPosition;
            const password = regData.password;
            const dateOfJoining = new Date();
            if (!productStatus && subscriptionStatus) {
                let subscriptionPeriod = await SubscriptionConfig.findOne();
                productValidity = await this.getPackageValidity({
                    addMonth: subscriptionPeriod.subscriptionPeriod,
                });
            } else if (productStatus && subscriptionStatus) {
                productValidity = packageValidity;
            }
            if (mlmPlan === "Binary") {
                position = parseInt(legPosition) === 1 ? "L" : "R";
            } else if (mlmPlan === "Matrix") {
                legPosition = parseInt(placementData.positionString);
                position = parseInt(placementData.positionString);
            }
            const sponsorIndex = parseInt(sponsorData.downline.length || 0) + 1;
            const newUser = await User.create(
                {
                    regFrom: 1,
                    userType: "user",
                    username: regData.username,
                    email: regData.email,
                    fatherId: placementData.fatherId,
                    sponsorId: sponsorData.id,
                    sponsorIndex,
                    userLevel: fatherData.userLevel + 1,
                    sponsorLevel: sponsorData.sponsorLevel + 1,
                    personalPv: productData ? productData.pairValue : regData.pv,
                    password: await bcrypt.hash(password, 10),
                    dateOfJoining: moment.utc(dateOfJoining).format("YYYY-MM-DD HH:mm:ss").toString(),
                    position,
                    legPosition,
                    productId: productData?.id ?? null,
                    productValidity: productValidity,
                    year: dateOfJoining.getFullYear(),
                    yearMonth: moment.utc(dateOfJoining).format("YYYY-MM").toString(),
                },
                options
            );
            return newUser;
            
        } catch (error) {
            logger.error("ERROR IN addToUserTable:- \n", error);
            throw error;
        }
    }
    async insertTreePath({ ancestors, newUser, transaction }) {
        try {
            let insertData = [];
            const options = transaction ? { transaction } : {};
            const chunkSize = 100000;
    
            for (const item of ancestors) {
                insertData.push({
                    descendant: newUser.id,
                    ancestor: item.id,
                    depth: newUser.userLevel - item.userLevel,
                });
            }
            insertData.push({
                descendant: newUser.id,
                ancestor: newUser.id,
                depth: 0,
            });
            if (insertData.length > chunkSize) {
                const chunks = _.chunk(insertData, chunkSize);
                for (const chunck of chunks) {
                    await Treepath.bulkCreate(chunck, options);
                }
            } else {
                await Treepath.bulkCreate(insertData, options);
            }
            return true;
        } catch (error) {
            logger.error("ERROR IN insertTreePath:- \n", error);
            throw error;
        }
    }

    async addToUserDetails({
        newUser,
        regData,
        signUpSettings,
        paymentGateway,
        sponsorData,
        transaction,
    }) {
        try {
            let options = transaction ? { transaction } : {};
            const payoutType = paymentGateway.find(
                    (item) => item.id === regData.paymentType
                )?.payoutStatus 
                    ? regData.paymentType 
                    : paymentGateway.find((item) => item.slug === "bank-transfer").id
            const details = {
                userId: newUser.id,
                sponsorId: sponsorData.id,
                countryId: regData.country ?? signUpSettings.defaultCountry,
                name: regData.first_name,
                secondName: regData.last_name ?? "",
                dob: new Date(convertToUTC(regData.date_of_birth ?? "1990-06-07")),
                mobile: regData.mobile ?? "9999999999",
                joinDate: new Date(),
                gender: regData.gender ?? "M",
                payoutType: payoutType,
                stateId: regData.state ?? null,
                pin: regData.pin ?? null,
                address: regData.address ?? null,
                address2: regData.address ?? null,
            };
            return await UserDetail.create(details, options);
        } catch (error) {
            logger.error("ERROR IN addToUserDetails:- \n", error);
            throw error;
        }
    }
    async addToRegistrationDetails({
        newUser,
        paymentData,
        regData,
        signUpSettings,
        paymentGateway,
        sponsorData,
        transaction,
    }) {
        try {
            let options = transaction ? { transaction } : {};
            const details = {
                userId: newUser.id,
                username: regData.username,
                name: regData.first_name,
                secondName: regData.last_name ?? "",
                regAmount: paymentData.regAmount,
                totalAmount: paymentData.totalAmount,
                productId: regData.product?.id ?? null,
                productAmount: paymentData.productAmount,
                productPv: regData.pv,
                email: regData.email,
                paymentMethod:
                    regData.paymentType ??
                    paymentGateway.find((item) => item.slug === "free-joining")
                        .id,
                countryId: regData.country ?? signUpSettings.defaultCountry,
                ocProductId: null,
                address: regData.address ?? null,
                address2: regData.address2 ?? null,
            };
            return await UsersRegistration.create(details, options);
        } catch (error) {
            logger.error("ERROR IN addToRegistrationDetails:- \n", error);
            throw error;
        }
    }

    async addCustomFieldData({ newUser, customFields, transaction }) {
        try {
            let options = transaction ? { transaction } : {};
            let insertData = [];
            for (const field of customFields) {
                insertData.push({
                    customfieldId: field.id,
                    userId: newUser.id,
                    value: field.value,
                });
            }
            return await CustomfieldValue.bulkCreate(insertData, options);
        } catch (error) {
            logger.error("ERROR IN addCustomFieldData:- \n", error);
            throw error;
        }
    }

    async addTransactionPassword({ newUser, transaction }) {
        try {
            let options = transaction ? { transaction } : {};
            return await TransactionPassword.create(
                { userId: newUser.id, password: await bcrypt.hash("12345678", 10) },
                options
            );
        } catch (error) {
            logger.error("ERROR IN addTransactionPassword:- \n", error);
            throw error;
        }
    }

    async addToSponsorTreePath({ ancestors, newUser, transaction }) {
        try {
            let insertData = [];
            const options = transaction ? { transaction } : {};
            const chunkSize = 100;
    
            for (const item of ancestors) {
                insertData.push({
                    descendant: newUser.id,
                    ancestor: item.id,
                    depth: newUser.sponsorLevel - item.sponsorLevel,
                });
            }
            insertData.push({
                descendant: newUser.id,
                ancestor: newUser.id,
                depth: 0,
            });
            if (insertData.length > chunkSize) {
                const chunks = _.chunk(insertData, chunkSize);
                for (const chunck of chunks) {
                    await SponsorTreepath.bulkCreate(chunck, options);
                }
            } else {
                await SponsorTreepath.bulkCreate(insertData, options);
            }
            return true;
        } catch (error) {
            logger.error("ERROR IN addToSponsorTreePath:- \n", error);
            throw error;
        }
    }

    async addToUserBalance({ newUser, transaction }) {
        try {
            let options = transaction ? { transaction } : {};
            return await UserBalanceAmount.create(
                { userId: newUser.id, balanceAmount: 0, purchaseWallet: 0 },
                options
            );
        } catch (error) {
            logger.error("ERROR IN addToUserBalance:- \n", error);
            throw error;
        }
    }

    async addLegDetails({ newUser, transaction }) {
        try {
            let options = transaction ? { transaction } : {};
            return await LegDetail.create({ userId: newUser.id }, options);
        } catch (error) {
            logger.error("ERROR IN addLegDetails:- \n", error);
            throw error;
        }
    }

    async updateGroupPv({
        newUser,
        sponsorUplines,
        pv,
        action = "null",
        transaction,
    }) {
        try {
            const chunkSize = 80;
            pv = parseFloat(pv);
            if (sponsorUplines.length > chunkSize) {
                const chunks = _.chunk(sponsorUplines, chunkSize);
                for await (const item of chunks) {
                    let updateIds = _.map(item, "id");
                    let res = await User.update(
                        { groupPv: sequelize.literal(`group_pv + ${pv}`) },
                        { where: { id: updateIds }, transaction }
                    );
                }
            } else {
                let updateIds = _.map(sponsorUplines, "id");
                await User.update(
                    { groupPv: sequelize.literal(`group_pv + ${pv}`) },
                    { where: { id: updateIds }, transaction }
                );
            }
            return true;
        } catch (error) {
            logger.error("ERROR IN updateGroupPv:- \n", error);
            throw error;
        }
    }

    async addToSalesOrder({
        user,
        invoiceNo,
        pendingStatus,
        regData,
        paymentData,
        transaction,
    }) {
        try {
            let options = transaction ? { transaction } : {};
            return await SalesOrder.create(
                {
                    productId: regData.product.id,
                    amount: paymentData.productAmount,
                    productPv: regData.pv,
                    paymentMethod: regData.paymentType,
                    userId: pendingStatus ? null : user.id,
                    pendingUserId: pendingStatus ? user.id : null,
                    invoiceNo: invoiceNo,
                    regAmount: paymentData.totalAmount,
                },
                options
            );
        } catch (error) {
            logger.error("ERROR IN addToSalesOrder:- \n", error);
            throw error;
        }
    }

    async insertRoi({ user, product, paymentType, transaction }) {
        try {
            let options = transaction ? { transaction } : {};
            const packageDetails = await Package.findByPk(product.id);
            return await RoiOrder.create(
                {
                    packageId: product.id,
                    user_id: user.id,
                    amount: product.price,
                    paymentMethod: paymentType,
                    pendingStatus: 1,
                    roi: packageDetails.roi,
                    days: packageDetails.days,
                },
                options
            );
        } catch (error) {
            logger.error("ERROR IN insertRoi:- \n", error);
            throw error;
        }
    }
    async addToPendingRegistration({
        regData,
        sponsorData,
        placementData,
        moduleStatus,
        signUpSettingsData,
        regFromTree,
        config,
        transaction,
    }) {
        const usernameConfig = await UsernameConfig.findOne();

        let options = transaction ? { transaction } : {};
        const currentDate = new Date();
        const pendingData = {
            sponsor_id: sponsorData.id,
            sponsorName: sponsorData.username,
            username: regData.username,
            password: regData.password,
            password_confirmation: regData.password,
            terms: "yes",
            payment_method: regData.paymentType,
            regFromTree: regFromTree,
            date_of_birth: regData.date_of_birth,
            sponsorFullname: `${sponsorData.UserDetail.name} ${sponsorData.UserDetail.secondName}`,
            placement_username: placementData.fatherUsername,
            placement_fullname: placementData.fatherUsername, //TODO change to full name
            position: regData.position ?? null,
            product_id: regData.product?.id ?? null,
            first_name: regData.first_name,
            gender: regData.gender,
            email: regData.email,
            mobile: regData.mobile,
            totalAmount: regData.totalAmount,
            epin: regData.epin ?? null,
            tranusername: null,
            tranPassword: regData.transactionPassword ?? null,
            mlm_plan: moduleStatus.mlmPlan,
            username_type: usernameConfig.userNameType,
            default_country: null, //TODO
            reg_amount: config.regAmount, //
            placement_id: placementData.id,
            product_amount: (regData.product) ? regData.product.price : config.regAmount,
            product_pv: (regData.product) ? regData.product.pv : config.regAmount,
            date_of_joining: moment.utc(currentDate).format("YYYY-MM-DD HH:mm:ss").toString(),
        };
        return await PendingRegistration.create(
            {
                username: regData.username,
                email: regData.email,
                updatedId: null,
                packageId: moduleStatus.productStatus
                    ? regData.product.id
                    : null,
                sponsorId: sponsorData.id,
                paymentMethod: regData.paymentType,
                data: JSON.stringify(pendingData),
                status: "pending",
                dateAdded: currentDate,
                dateModified: null,
                emailVerificationStatus: signUpSettingsData.mailNotification
                    ? "yes"
                    : "no",
                defaultCurrency: null,
                userTokens: null,
            },
            options
        );
    }

    async managePayment({
        req,
        res,
        next,
        newUser,
        userPaymentType,
        regData,
        sponsorData,
        moduleStatus,
        transaction,
    }) {
        try {
            switch (userPaymentType["slug"]) {
                case "e-pin":
                    let epins = regData.epins;
                    // remove duplicate epins
                    epins = epins.filter(
                        (value, index, self) => self.indexOf(value) === index
                    );
                    logger.info("FILTERED EPINS: ", epins);

                    const result = await paymentService.epinPayment(
                        res,
                        transaction,
                        sponsorData.id,
                        epins,
                        regData.totalAmount,
                        "register"
                    );
                    break;

                case "e-wallet":
                    const checkPassword = await verifyTransactionPassword(req,res,next);
                    if (!checkPassword) {
                        await transaction.rollback();
                        const response = await errorMessage({ code: 1015, statusCode: 422 });
                        return res.status(response.code).json(response.data);
                    }
                    const userBalance = await UserBalanceAmount.findOne({
                                            where: { userId: sponsorData.id },
                                        });
                    if (parseFloat(userBalance.balanceAmount) < parseFloat(regData.totalAmount)) {
                        await transaction.rollback();
                        let response = await errorMessage({
                            code: 1014,
                            statusCode: 422,
                        });
                        return res.status(response.code).json(response);
                    }

                    await paymentService.ewalletPayment({
                        transaction,
                        userId: sponsorData.id,
                        userBalance,
                        totalAmount: regData.totalAmount,
                        action: "registration"
                    });

                    break;
                case "free-joining":
                    break;
                // case "purchase-wallet":
                //     break;
                case "bank-transfer":
                    break;
                // case "stripe":
                //     const stripeToken = regData.stripeToken.id;

                //     const stripeResponse =
                //         await paymentService.createStripeCharge(
                //             stripeToken,
                //             regData.totalAmount,
                //             "Register"
                //         );
                //     if (stripeResponse == false) {
                //         let response = await errorMessage({ code: 429 });
                //         return res.status(500).json(response);
                //     }
                //     const chargeId = stripeResponse["charge_id"];
                //     const paymentMethod = stripeResponse["payment_method"];

                //     await paymentService.insertIntoStripePaymentDetail(
                //         sponsorData.id,
                //         chargeId,
                //         newUser.productId,
                //         null,
                //         regData.totalAmount,
                //         "register",
                //         paymentMethod,
                //         stripeResponse,
                //         transaction
                //     );

                //     break;
                case "stripe":
                    const stripeToken = regData.stripeToken;
          
                    const stripeResponse = await paymentService.retrievePaymentIntent(
                      stripeToken
                    );
                    if (stripeResponse == false) {
                      await transaction.rollback();
                      let response = await errorMessage({ code: 429 });
                      return res.status(500).json(response);
                    }
                    const paymentMethod = stripeResponse["payment_method"] ?? "";
          
                    await paymentService.insertIntoStripePaymentDetail(
                      sponsorData.id,
                      stripeToken,
                      newUser.productId,
                      null,
                      regData.totalAmount,
                      "register",
                      paymentMethod,
                      stripeResponse,
                      transaction
                    );
          
                    break;
                case "paypal":
                    break;
                default:
                    logger.warn(`INVALID PAYMENT METHOD ${userPaymentType.slug}`);
                    throw new Error("Invalid Payment Method")
                    // await transaction.rollback();
                    // const response = await errorMessage({
                    //     code: 1036,
                    //     statusCode: 422,
                    // });
                    // return res.status(response.code).json(response.data);
            }

            return true;
        } catch (error) {
            logger.error("ERROR FROM managePayment");
            throw error;
        }
    }
    async checkUsernameAndEmail({ username, email }) {
        const [user, pendingUser] = await Promise.all([
            User.count({
                attributes: [],
                where: {
                    [Op.or]: [{ username }, { email }],
                },
            }),
            PendingRegistration.count({
                attributes: [],
                where: {
                    [Op.and]: [
                        {
                            [Op.or]: [{ username }, { email }],
                        },
                        {
                            status: {
                                [Op.ne]: "rejected",
                            },
                        },
                    ],
                },
            }),
        ]);
        return user || pendingUser ? false : true;
    }

    async checkPlacementAvailable({mlmPlan,placementUserDetails,position,sponsor,}) {
        const checkPlacementIsDescendantOfSponsor = await Treepath.findOne({
            where: {
                descendant: placementUserDetails.id,
                ancestor: sponsor.id,
            },
        });
        if (!checkPlacementIsDescendantOfSponsor) return false;

        if (mlmPlan !== "Unilevel") {
            const checkPosition = await User.findOne({
                attributes: ["id", "fatherId", "username"],
                where: {
                    position: position,
                    fatherId: placementUserDetails.id,
                },
            });
            if (checkPosition) return false;
        }
        return true;
    }

    async setUserPlacementData({sponsorData, legPosition, placementData, newUser, transaction, regFromTree}) {
        let options = transaction ? { transaction }: {};
        let setData = (legPosition === 1)
                        ? {leftMost: newUser.id}
                        : {rightMost: newUser.id};
        let whereClause   = {};
        const sponsorUserPlacement = await UserPlacement.findOne({ attributes: ["branchParent"], where: { userId: sponsorData.id } })

        whereClause.userId = (parseInt(sponsorData.legPosition) === parseInt(legPosition))
                        ? sponsorUserPlacement.branchParent
                        : sponsorData.id;
        await UserPlacement.update(setData, {
                where: whereClause,
                transaction: transaction
        });
        await UserPlacement.create({ userId: newUser.id, branchParent: whereClause.userId}, options);
    }
}

export default new RegisterService();
