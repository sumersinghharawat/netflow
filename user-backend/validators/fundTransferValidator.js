import Joi from "joi";

export const fundTransferSchema = Joi.object({
    username : Joi.string().required(),
    amount: Joi.number().greater(0).required(),
    transactionPassword: Joi.string().required()
});

