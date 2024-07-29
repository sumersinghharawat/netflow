import User from "../models/user.js";
import { UserBalanceAmount } from "../models/association.js";
import logger from "../helper/logger.js";

export default async (req, res, next) => {
    try {
        const userData = await User.findByPk(req.auth.user.id, {
            attributes: ["id", "username"],
            include: [{ model: UserBalanceAmount, attributes: ["balance_amount", "purchase_wallet"] }]
        });
        return {
            'ewalletBalance': userData.UserBalanceAmount.balance_amount,
            'purchaseWalletBalance': userData.UserBalanceAmount.purchase_wallet
        };
    } catch (error) {
        console.log("error from getUserBalance -----");
        // return next(error);

    }
}