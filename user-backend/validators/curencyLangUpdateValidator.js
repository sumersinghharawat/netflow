import Joi from "joi";

export const currencyUpdateSchema = Joi.object({
    currencyId: Joi.string().required()
});

export const langUpdateScema = Joi.object({
    langId: Joi.string().required()
});