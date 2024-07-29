export const validateRequest = (schema) => {
    return async (req, res, next) => {

        try {
            const { error } = schema.validate(req.body);
            if(error) {
                const errorMsg = error.details.map( (msg) => msg.message).join(', ');
                return res.status(422).json({ message : errorMsg });
            }
        } catch (error) {
            console.log('from validation --------------', error);
        }
        next();
    }
}