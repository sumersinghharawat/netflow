import Joi from "joi";

export const loginSchemaApp = Joi.object({
    username : Joi.string().required(),
    password : Joi.string().required()
});