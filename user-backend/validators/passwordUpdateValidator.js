import Joi from "joi";

export const passwordUpdateSchema = Joi.object({
    password : Joi.string().required(),
    hash : Joi.string().required(),
});