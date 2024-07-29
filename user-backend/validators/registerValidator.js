import Joi from "joi";

//  use this for client projects
export default Joi.object({
    username: Joi.string().required(),
    email: Joi.string().required(),
    password: Joi.string().required(),
    pv: Joi.required()
});