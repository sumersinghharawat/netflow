import Joi from "joi";

export const demoVisitorSchema = Joi.object({
    name: Joi.string().required(),
    email: Joi.string().required(),
    phone: Joi.number().required(),
    countryId: Joi.number().required(),

});



