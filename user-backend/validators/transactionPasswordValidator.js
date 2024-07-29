import Joi from "joi";

export default Joi.object({
    transPassword: Joi.string().required(),
    totalAmount: Joi.number().required()
});