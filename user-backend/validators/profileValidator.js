import Joi from "joi";

export const personalDataSchema = Joi.object({
    name    : Joi.string().required(),
    secondName : Joi.string().allow(null).allow(""),
    gender : Joi.string().valid('M', 'F', 'O')
});

export const contactDataSchema = Joi.object({
    address : Joi.string().allow(null).allow(""),
    address2 : Joi.string().allow(null).allow(""),
    country : Joi.number().integer().allow(null),
    state : Joi.number().integer().allow(null),
    city : Joi.string().allow(null).allow(""),
    zipCode : Joi.string().allow(null).allow(""),
    mobile : Joi.string().pattern(/^(\+?\d{0,3}\s?)?\d{1,14}$/).allow(null).allow(""),
});

export const bankDataSchema = Joi.object({
    bankName    : Joi.string().allow(null).allow(""),
    branchName : Joi.string().allow(null).allow(""),
    holderName : Joi.string().allow(null).allow(""),
    accountNo : Joi.number().integer().allow(null).allow(""),
    ifsc : Joi.string().allow(null).allow(""),
    pan : Joi.string().allow(null).allow(""),
});
