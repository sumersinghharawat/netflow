import { Op } from "sequelize";
import { errorMessage, logger } from "../helper/index.js";
import InfPresetDemoVisiter from "../models/infPresetDemoVisitor.js";
import InfiniteMlmLead from "../models/infiniteMlmLead.js";
import InfiniteMlmLeadsConfig from "../models/infiniteMlmLeadConfig.js";
import { mailConfigDemo, sendMailDemo } from "../utils/nodeMailer.js";
import { getModuleStatus } from "../utils/index.js";
import DemoUser from "../models/demoUser.js";
import Country from "../models/countries.js";
class DemoService {
    async checkEmailReuseCount(isPreset,leadsConfig, email) {
        try {

            const unlimitedEmailCheck = await InfiniteMlmLeadsConfig.findOne({
                where:{
                    unlimitedEmails: { [Op.like] : `%${email}%` }
                }
            });
            if (unlimitedEmailCheck) return true;

            const leadCount = await InfiniteMlmLead.count({
                where: {
                    email: email,
                    demoType: isPreset ? "demo" : "custom"
                }
            });
            if (leadCount<=leadsConfig.emailReuseCount) return true;
            return false;
        } catch (error) {
            logger.error("ERROR FROM checkEmailReuseCount",error);
            throw error;
        }
    }

    async addDemoVisitor(transaction, leadsConfig, isPreset, name, email, mobile, country, ip) {
        try {
            const moduleStatus = await getModuleStatus({attributes:["mlmPlan"]});
            const currentDate = new Date();
            // to not change the currentDate obj
            let accessExpiry = currentDate;
            const timeoutPeriod = country.toLowerCase()=="india" 
                ? Number(leadsConfig.indianPresetDemoTimeout) 
                : Number(leadsConfig.otherPresetDemoTimeout);
            accessExpiry = new Date(accessExpiry.setHours(accessExpiry.getHours() + timeoutPeriod));
            
            const visitor = await InfPresetDemoVisiter.create({
                userFullName: name,
                userEmail: email,
                mobile: mobile,
                country: country,
                ip: ip,
                date: currentDate
            }, { transaction });

            const leadDetails = await InfiniteMlmLead.create({
                name: name,
                email: email,
                phone: mobile,
                country: country,
                ipAddress: ip,
                demoType: isPreset ? "preset" : "custom",
                demoRefId: visitor.id,
                status: "pending",
                addedDate: currentDate,
                accessExpiry: accessExpiry,
            }, { transaction });

            const mailBody = `<br><br>Hi,
                <br><br>&nbsp;&nbsp;&nbsp;The following is the information submitted by the preset demo visitor:  
                <br><br>Name : ${name} 
                <br>Email :${email} 
                <br>Plan : ${moduleStatus.mlmPlan} 
                <br>Admin User Country : ${country} 
                <br>Mobile : ${mobile}`;
            const mailArr = {
                subject: "Infinite MLM - Preset Demo Visitor Details",
                content: mailBody,
                type: "support"
            };
            const Mail = await mailConfigDemo();
            const result = await sendMailDemo(Mail, "support@ioss.in", mailArr);
            logger.debug("MAIL RESULT",result);
            if (!result) return false;

            const leadOTPResponse = await this.sendLeadOTP(transaction, leadsConfig, leadDetails);
            if (leadOTPResponse) {
                return visitor.id;
            } else {
                return false;
            }  
        } catch (error) {
            logger.error("ERROR IN addDemoVisitor");
            throw error;
        }
    }

    async sendLeadOTP(transaction, leadsConfig, leadDetails) {
        try {
        const emailOTP = Math.floor(1000 + Math.random() * 9000);
        const options = transaction? {transaction} : {};
        let mailArr = {
            subject: "Infinite MLM Demo Verification OTP",
            fullname: leadDetails.name,
            otp: emailOTP,
            type: "user"
        };

        const Mail = await mailConfigDemo();
        const result = await sendMailDemo(Mail,leadDetails.email,mailArr);
        logger.debug("MAIL RESULT",result);
        
        if (result) {
            const currentDate = new Date();
            const otpExpiry = new Date(currentDate.setMinutes(currentDate.getMinutes() + Number(leadsConfig.otpTimeout)));
            await leadDetails.update({
                emailOtp: emailOTP,
                status: "pending",
                otpExpiry: otpExpiry
            }, options);

            return true;
        } 

        return false;

        } catch(error) {
            logger.error("ERROR IN sendLeadOTP");
            throw error;
        }
    }

    async getVisitorLeadDetails(visitorId) {
        return await InfiniteMlmLead.findOne({where:{
            demoType: "preset",
            demoRefId:visitorId
        }});
    }
    async checkDemo({ prefix }) {
        return await DemoUser.findOne({where: {prefix}, attributes:["id", "username","isPreset", "prefix", "mlmPlan"]});
    }
    async getAllCountries() {
        return await Country.findAll();
    }
    async createMailAutomationData() {
        
    }
}

export default new DemoService;