import Joi from "joi";

export const upgradeDataSchema = Joi.object({
    paymentMethod: Joi.number().required(),
    transactionPassword: Joi.string().optional(),
    oldProductId: Joi.number().allow('').required(),
    upgradeProductId: Joi.number().allow('').required(),
    totalAmount: Joi.number().required(),
    totalAmt: Joi.string().optional(),
    epinBalance: Joi.optional(),
    totalEpinAmount: Joi.number().optional(),
    epins: Joi.array().optional(),
    bankReceipt: Joi.string().optional(),
});