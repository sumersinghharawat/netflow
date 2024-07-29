import Joi from "joi";

export const subscriptionDataSchema = Joi.object({
    packageId:Joi.number().required(),
    paymentMethod: Joi.number().required(),
    transactionPassword: Joi.string().optional(),
    epins: Joi.array().optional(),
    epinBalance: Joi.optional(),
    totalEpinAmount: Joi.number().optional(),
    bankReceipt: Joi.string().optional(),
    totalAmount: Joi.number().required(),
    stripeToken: Joi.string().optional(),
    paypalToken: Joi.string().optional(),
})



