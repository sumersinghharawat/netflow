import Joi from "joi";

export const composeMailSchema = Joi.object({
    message: Joi.string().required(),
    username: Joi.string().optional().allow("").allow(null),
    subject: Joi.string().optional().allow("").allow(null),
    parentMailId: Joi.optional().allow("").allow(null),
    type: Joi.string().optional().allow("").allow(null)
});
