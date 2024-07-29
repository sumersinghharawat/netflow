import fs from "fs";
import path from "path";
import { sequelize } from "../config/db.js";
import { consoleLog, errorMessage, logger, successMessage } from "../helper/index.js";
import { encrypt, decrypt } from "../utils/crypto.js";
import { uploadFile } from "../utils/fileUpload.js";
import { Country, CustomfieldLang, CustomfieldValue, OcProduct, Package, PackageValidityExtendHistory, PaymentGatewayConfig, PurchaseRank, Rank, RankDetail, RankDownlineRank, SignupField, State, SubscriptionConfig, User, UserDetail } from "../models/association.js";
import RankConfiguration from "../models/rankConfiguration.js";

class ProfileService {

    async getProfileData(userId, moduleStatus) {
        let includes = [
            {
                model: UserDetail,
                include: [
                    {
                        model: Country,
                        attributes: ["id", "name", "code", "isoCode"]
                    }, 
                    {
                        model: State,
                        attributes: ["id", "name", "code"]
                    }, 
                ]
            },
            {
                model: Package,
                attributes: ["id", "productId", "name", "validity", "days", "image"],
            }, 
            {
                model: User,
                as: "sponsor",
                attributes: ["id", "username"],
                include:[
                    {
                        model: UserDetail,
                        attributes: ["name", "second_name"]
                    }
                ]
            }, 
            {
                model: User,
                as: "father",
                attributes: ["id", "username"],
                include:[
                    {
                        model: UserDetail,
                        attributes: ["name", "second_name"]
                    }
                ]
            }, 
            {
                model: Rank,
                attributes: ["id", "name", "image"]
            }, 
           
        ];

        if(moduleStatus.ecomStatus) {
            includes.push({
                model: OcProduct,
                attributes: ["productId", "model", "image", "sku", "validity", "days","subscriptionPeriod"],
            });
        }
        if(moduleStatus.rankStatus) {
            includes.push({
                model: Rank,
                attributes: ["id","name"]
            })
        }
        const data = await User.findByPk(userId, { 
            attributes: ["id", "username", "position", "legPosition", "userRankId","totalLeftCarry", "email",
            "totalRightCarry", "productValidity", "personalPv", "groupPv", "emailVerified", "autoRenewalStatus"],
            include: includes
        });
        return data;
    }

    async getCustomFields(userId) {
        let values = await SignupField.findAll({
            attributes: ["id", "name", "type", "deafult_value"],
            where: { isCustom: 1, status: 1 },
            include: [
                { model: CustomfieldLang, required: false },
                {
                    model: CustomfieldValue,
                    where: {
                        user_id: userId,
                    },
                    required:false,
                },
            ],
        });
        return values
    }

    async getPayoutDetails(userId)
    {
        return await PaymentGatewayConfig.findAll({ where: { payoutStatus: 1 }, attributes: ["id", "name", "slug", "logo", "mode"], raw: true });
    }
    async updatePersonalData(req, moduleStatus, next) {
    	let dbTransaction;
        try {
            let { name, secondName, gender} = req.body;
            const prefix        = `${req.prefix}_`;
            let userData        = await User.findByPk(req.auth.user.id);
		    dbTransaction   	= await sequelize.transaction();
            await UserDetail.update( { name, secondName, gender }, { where: { userId : req.auth.user.id }, transaction: dbTransaction });
            if(moduleStatus.ecomStatus) {
                await Promise.all([
                    sequelize.query(`UPDATE ${prefix}oc_customer
                    SET firstname = :name,
                        lastname = :secondName
                    WHERE customer_id= :ecomCustomerRefId;`, { replacements: {
                        name, secondName, ecomCustomerRefId: userData.ecomCustomerRefId
                    }, transaction: dbTransaction }),
                    sequelize.query(`UPDATE ${prefix}oc_address
                    SET firstname = '${name}',
                        lastname = '${secondName}'
                    WHERE customer_id='${userData.ecomCustomerRefId}';`, { transaction: dbTransaction }),
                ]);  
            }
            await dbTransaction.commit();
        } catch (error) {
            await dbTransaction.rollback();
            return await errorMessage({ code : 1086, statusCode: 404 });
		    return next(error);
        }
        return await successMessage({ data: "Personal Data Updated Successfully."});
    }

    async updateContactDetails(req, moduleStatus, next) {
        let { address, address2, country, state, city, zipCode, mobile } = req.body;

        let userData        = await User.findByPk(req.auth.user.id);
        const prefix        = `${req.prefix}_`;
    	let dbTransaction;
        try {
		    dbTransaction   	= await sequelize.transaction();

            await UserDetail.update({ address, address2: address2, countryId: country, stateId: state, city, pin: zipCode, mobile },
                { where: { userId: req.auth.user.id }, transaction: dbTransaction});
            if(moduleStatus.ecomStatus) {
                await Promise.all([
                    sequelize.query(`UPDATE ${prefix}oc_address
                    SET address_1 = :address,
                        address_2 = :address2,
                        city = :city,
                        postcode = :zipCode
                    WHERE customer_id='${userData.ecomCustomerRefId}';`, {
                        replacements: {
                            address, address2: address2, city, zipCode
                        }, transaction: dbTransaction
                    }),
                    sequelize.query(`UPDATE ${prefix}oc_customer
                    SET telephone = :mobile
                    WHERE customer_id='${userData.ecomCustomerRefId}';`, { replacements: { mobile }, transaction: dbTransaction }),
                ]);  
            }
            await dbTransaction.commit();
        } catch (error) {
            await dbTransaction.rollback();
		    return next(error);
            return await errorMessage({ code : 1086, statusCode: 404 });
        }
        userData.address    = address;
        userData.address2   = address2;
        userData.countryId  = country;
        userData.stateId    = state;
        userData.city       = city;
        userData.pin        = zipCode;
        userData.mobile     = mobile;
        if(userData.save()) {
            return await successMessage( { data: "Contact details Updated Successfully."});
        }
        return await errorMessage({ code : 1086, statusCode: 404 });
    }

    async updateBankDetails(req) {
        let { bankName, branchName, holderName, accountNo, ifsc, pan } = req.body;
        let userData       = await UserDetail.update(
            { bank: bankName, branch: branchName, nacctHolder: holderName, accountNumber: accountNo, ifsc  : ifsc, pan: pan }, 
            { where: { userId : req.auth.user.id }});
        return await successMessage({ data: "Bank details Updated Successfully."});
    }
    async updatePaymentDetails(req) {
        let { stripeAccount, paypalAccount, paymentMethod } = req.body;
        let data = {};
        if(stripeAccount) data.stripe = await encrypt(stripeAccount);
        if(paypalAccount) data.paypal = await encrypt(paypalAccount);

        const paymentDetails          = await PaymentGatewayConfig.findByPk(paymentMethod);
        if(paymentDetails) data.payoutType  = paymentDetails.id;
        
        await UserDetail.update(data, { where: { userId: req.auth.user.id }});
        return await successMessage({ data: "Payment details Updated Successfully."});
    }
    async updateSettings(req, moduleStatus) {
        const { language, currency, lockPosition } = req.body;
        let data = {};
        if(moduleStatus.multilangStatus) data.defaultLang = language;
        if(moduleStatus.multiCurrencyStatus) data.defaultCurrencyCode = currency; 
        if(moduleStatus.mlmPlan == "Binary") data.lockPosition = lockPosition; 
        await User.update(data, { where: { id: req.auth.user.id }});
        return await successMessage({ data: "Settings Updated Successfully."});
    }
    async removeAvatar(req) {
        const __dirname     = path.dirname(new URL(import.meta.url).pathname);
        const userDetail = await UserDetail.findOne({ where: { userId: req.auth.user.id }});
        if (userDetail.image) {
            // Since fs.existsSync() expects a local file path, it won't work with a URL
            const imagePath = userDetail["image"].split(process.env.IMAGE_URL).pop();
            const filePath = path.join(__dirname, "../", imagePath);
            logger.debug("imagePath: ",imagePath,"\nfilePath: ",filePath);
            if (fs.existsSync(filePath)) {
                fs.unlinkSync(filePath);
                console.log("Old image deleted successfully.");
            } else {
                console.log("Old image does not exist.");
            }
        }
        await UserDetail.update({ image: null }, { where: {userId: req.auth.user.id } });
        return await successMessage({ data: "Avatar removed Successfully."});
    }

    async updateuserDetails(req, path){
        let image = process.env.IMAGE_URL+`${path}`;
        await UserDetail.update({ image}, { where: {userId: req.auth.user.id }});
        return await successMessage({ data: "Profile pic updated successfully"});
    }

    async updatePackageValidity({transaction, user, currentValidity, productData, paymentAmount, paymentMethod, renewalStatus, subscriptionConfig}){
        const options = transaction ? {transaction} : {}
        
        // do not use new Date() for comparison - avoid creating a new obj
        // if user["productValidity"] = null, new Date(null) = Unix epoch time (1970/01/01)
        if (currentValidity < Date.now()) {
            currentValidity = new Date();
        };
        
        // find new product validity
        let validityPeriod  = productData.validity;
        if (subscriptionConfig.basedOn === "member_package") {
            validityPeriod = subscriptionConfig.subscriptionPeriod;
        };
        const currentMonth  = currentValidity.getMonth();
        const newMonth      = (currentMonth + validityPeriod) % 12;
        const yearIncrement = Math.floor((currentMonth + validityPeriod) / 12);
        const newValidity   = new Date(currentValidity);
        newValidity.setMonth(newMonth);
        newValidity.setFullYear(newValidity.getFullYear() + yearIncrement);

        // update users table
        await user.update({ productValidity: newValidity }, options);
        return true;
    }

    async insertIntoPackageValidityExtendHistory({transaction, user, productData, paymentAmount, paymentMethod, renewalStatus, receipt }) {
        const options = transaction ? {transaction} : {};
        
        const lastId = await PackageValidityExtendHistory.findOne({order:[["createdAt","DESC"]]});
        const invoiceId = lastId ? "VLDPCK" + (1000 + lastId.id + 1) : "VLDPCK" + (1000 + 1 + 1);

        await PackageValidityExtendHistory.create({
            userId: user["id"],
            packageId: productData["id"],
            invoiceId: invoiceId,
            totalAmount: paymentAmount,
            productPv: productData.pairValue,
            paymentType: paymentMethod,
            payType: "manual",
            renewalDetails: null,
            renewalStatus: renewalStatus,
            receipt: receipt ?? null
        }, options);
    }

    async getRankForProfile(rankData,moduleStatus) {
        var activeConfig = await RankConfiguration.findAll({where: { status: 1}}); 
        let select = [];
        let relation = [{
            model: RankDetail,
            as: "details",
            attributes: select
        }];
        if(activeConfig.find( item => item.slug === "joiner-package")) {
            if (moduleStatus.ecomStatus) {
                relation.push({
                    model: OcProduct,
                    attributes: ["productId",["model","name"],"image"]
                })
            } else {
                relation.push({
                    model: Package,
                    attributes: ["id", "name", "productId", "image"]
                });
            }
            
        }
        if(activeConfig.find( item => item.slug === "referral-count")) {
            select.push("referralCount");
        }
        if(activeConfig.find( item => item.slug === "personal-pv")) {
            select.push("personalPv");
        }
        if (activeConfig.find(item => item.slug === "group-pv")) {
            select.push("groupPv");
        }
        if (activeConfig.find(item => item.slug === "downline-member-count")) {
            select.push("downlineCount");
        }
        if (activeConfig.find(item => item.slug === "downline-package-count")) {
            if (moduleStatus.ecomStatus) {
                relation.push({
                    model: OcProduct,
                    as: "OcProductCount",
                    attributes: ["productId", ["model","name"], "image"],
                    through: {
                        model: PurchaseRank,
                        attributes: ["count"]
                    }
                })
            } else {
                relation.push({
                    model: Package,
                    as: "PackageCount",
                    attributes: ["id", "name", "image"],
                    through: {
                        model: PurchaseRank,
                        attributes: ["count"]
                    }
                });
            }

        }
        if (activeConfig.find(item => item.slug === "downline-rank-count")) {
            relation.push({
                model: Rank,
                as: "RankCount",
                attributes: ["id", "name"],
                through: {
                    model: RankDownlineRank,
                    attributes: ["count"]
                }
            });
        }
        const ranks = await Rank.findAll({
            attributes: ["id", "name", "image", "color", "commission"],
            where: { status: 1 },
            include: relation
        });
        for (const rank of ranks) {
            let criteria = [];
            if (activeConfig.find(item => item.slug === "joiner-package"))
                criteria.push({
                    name: 'joiner-package',
                    value: moduleStatus.ecomStatus ? rank.OcProduct.name : rank.Package.name
                });
            if (activeConfig.find(item => item.slug === "referral-count"))
                criteria.push({
                    name: 'referral-count',
                    value: rank.details.referralCount
                });
            if (activeConfig.find(item => item.slug === "personal-pv"))
                criteria.push({
                    name: 'personalPv',
                    value: rank.details.personalPv
                });
            if (activeConfig.find(item => item.slug === "group-pv"))
                criteria.push({
                    name: 'group-pv',
                    value: rank.details.groupPv
                });
            if (activeConfig.find(item => item.slug === "downline-member-count"))
                criteria.push({
                    name: 'downline-member-count',
                    value: rank.details.downlineCount
                });
            if (activeConfig.find(item => item.slug === "downline-package-count"))
                if (moduleStatus.ecomStatus) {
                    criteria.push({
                        name: 'downline-package-count',
                        value: rank.OcProductCount.map(item => ({ label: item.name, value: item.PurchaseRank.count }))
                    });
                } else {
                    criteria.push({
                        name: 'downline-package-count',
                        value: rank.PackageCount.map(item => ({ label: item.name, value: item.PurchaseRank.count }))
                    });
                } 
            if (activeConfig.find(item => item.slug === "downline-rank-count"))
                criteria.push({
                    name: 'downline-rank-count',
                    value: rank.RankCount.map(item => ({ label: item.name, value: item.RankDownlineRank.count }))
                });
            rankData.push({
                id: rank.id,
                name: rank.name,
                image: rank.image,
                color: rank.color,
                commission: rank.commission,
                criteria
            });
        }
        return rankData;




    }
}

export default new ProfileService;