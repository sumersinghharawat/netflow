import Joi from "joi";

export const replicaContactSchema = Joi.object({
    referralId: Joi.string().required(),
    contactData: Joi.object({
        name: Joi.string().required(),
        email: Joi.string().required(),
        address: Joi.string().required(),
        phone: Joi.string().required(),
        contactInfo: Joi.string().required(),
    }).required(),
    
});

export const replicaRegisterSchema = Joi.object({

});