import axios from "axios";
import FormData from 'form-data';
import fs from "fs";
import JSEncrypt from 'node-jsencrypt';  
// import NodeRSA from "node-rsa";
import path from "path";
import { consoleLog, logger } from "../helper/index.js";
import UserCommissionStatusHistory from "../models/userCommissionStatusHistory.js";
import User from "../models/user.js";

class CommissionService {
    
    async commissionCall(prefix,userId, commData, action) {
        try {
            const userData = await User.findOne({
                attributes: ["sponsorId", "fatherId", "position"],
                where: { id: userId },
                raw: true
            });

            let data = {
                "action": action,
                "user_id": userId,
                "sponsor_id": userData.sponsorId ?? null,
                "product_id": commData.productId ?? null,
                "product_pv": (commData.productPv) ?? null,
                "price": (commData.productAmount) ?? null,
                "oc_order_id": 0,
                "order_id": (commData.orderId) ?? null,
                "upline_id": userData.fatherId ?? null,
                "position": userData.position ?? null,
            };

            let { id: statusId } = await UserCommissionStatusHistory.create({
                userId: userId,
                commission: "user_commission",
                data: JSON.stringify(data),
                status: 0,
                date: new Date()

            });
            data["status_id"] = statusId;
            logger.debug("USER COMMISSION STATUS HISTORY ID:",statusId);
            
            const {encData, encKey} = await this.encryptCommissionData(data);
            let form = new FormData();
            form.append("enc_data",encData);
            // const prefix = req.prefix;
            
            let commission;
            logger.debug("encrypted key: ",encKey)
            // try {
            axios.post(
                `${process.env.COMMISSION_URL}run_calculation`,
                form,
                { headers: {
                    "Content-Type": "multipart/form-data",
                    prefix: prefix,
                    SECRET_KEY: encKey,
                }}
            )
            .then(response => {
                commission = true;
            })
            .catch(error => {
                logger.error("ERROR IN AXIOS ",error.message);
                commission = false;
                
            })
            // } catch (error) {
            // }
            return commission;
        } catch (error) {
            logger.error("ERROR FROM commissionCall",error);
            throw error;
        }
    }

async encryptCommissionData(data) {
    try {
        data            = JSON.stringify(data)
        const secretKey = process.env.COMMISSION_KEY;
        const __dirname = path.dirname(new URL(import.meta.url).pathname);
        const keyPath   = path.join(__dirname,"../keys/","public.pem");
        const publicKey = fs.readFileSync(keyPath,"utf8");
        
        const jsEncrypt = new JSEncrypt();
        jsEncrypt.setPublicKey(publicKey);
        const encData = jsEncrypt.encrypt(data);
        const encKey = jsEncrypt.encrypt(secretKey);
        return {encData, encKey};
    } catch (error) {
        throw error;
    }
}

}
export default new CommissionService;

