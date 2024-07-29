import Joi from "joi";

export const epinPurchaseSchema = Joi.object({
    epinCount: Joi.string().required(),
    amountCode: Joi.array().required(),
    transactionPassword: Joi.string().required(),
    expiryDate: Joi.date().required()
});

export const epinRequestSchema = Joi.object({
    epinCount: Joi.string().required(),
    amountCode: Joi.array().required(),
    expiryDate: Joi.date().required()
});

export const epinTransferSchema = Joi.object({
    epin: Joi.array().required(),
    toUsername: Joi.string().required()
});
