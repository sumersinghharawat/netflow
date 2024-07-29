import Joi from "joi";

export const addToCartSchema = Joi.object({
    packageId : Joi.number().required(),
    count : Joi.number().optional()
});

export const addressSchema = Joi.object({
    name: Joi.string().required(), 
    address: Joi.string().required(), 
    zipCode: Joi.string().required(), 
    city: Joi.string().required(), 
    phoneNumber: Joi.string().pattern(/^(\+?\d{0,3}\s?)?\d{1,14}$/)
})

export const placeOrderSchema = Joi.object({
    product: Joi.array().required(),
    addressId: Joi.number().required(),
    totalAmount: Joi.number().required(),
    paymentType: Joi.number().required(),
    transactionPassword: Joi.string().optional(),
    epins: Joi.array().optional(),
    bankReceipt: Joi.string().optional(),
    totalAmt: Joi.string().optional(),
    epinBalance: Joi.optional(),
    totalEpinAmount: Joi.number().optional(),
    totalEpinAmt: Joi.number().optional(),
})