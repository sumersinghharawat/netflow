import Joi from "joi";
export const CreatTicketSchema = Joi.object({
    trackId:Joi.string().required(),
    priorityId:Joi.number().required(),
    statusId:Joi.number().required(),
    categoryId:Joi.number().required(),
    message: Joi.string().required(),
    subject:Joi.string().required()
});
