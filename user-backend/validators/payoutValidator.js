import Joi from "joi";

export const payoutRequestSchema = Joi.object({
    payoutAmount: Joi.number().required(),
    transactionPassword: Joi.string().required(),
})

export const payoutCancelSchema = Joi.object({
    payoutIdArr : Joi.array().required(),
})
