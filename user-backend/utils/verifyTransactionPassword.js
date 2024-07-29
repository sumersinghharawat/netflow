import becrypt from "bcryptjs";
import User from "../models/user.js";
import { TransactionPassword } from "../models/association.js";
import {consoleLog, logger} from "../helper/index.js";

export default async (req, res, next) => {
    try {
        const user = await User.findByPk(req.auth.user.id, {
            attributes: ["id", "username"],
            include: [ { model: TransactionPassword, attributes: ["password"],}]
        });
        let password = req.body.transactionPassword || '12345678';
        if(!user || !user.TransactionPassword.password) return false;
        return await becrypt.compare(password, user.TransactionPassword.password);
    } catch (error) {
        logger.error('Error from verifyTransaction password : -',error)
        throw error;
    }
}