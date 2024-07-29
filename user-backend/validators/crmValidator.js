import Joi from "joi";

export const addCRMSchema = Joi.object({
    firstName: Joi.string().required(),
    lastName: Joi.string().optional().allow("").allow(null),
    emailId: Joi.string().required(),
    skypeId: Joi.string().optional().allow(""),
    mobileNo: Joi.string().required(),
    countryId: Joi.number().optional().allow(null),
    description: Joi.string().optional().allow("").allow(null),
    interestStatus: Joi.number().optional().allow(null).allow(""),
    followupDate: Joi.string().allow(null).allow(""),
    leadStatus: Joi.number().allow(null).optional().allow(""),
    confirmationDate: Joi.string().optional().allow(null).allow("")
});
