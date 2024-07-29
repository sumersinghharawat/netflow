import {logger} from "../utils/index.js"
import jwt from 'jsonwebtoken';

export const verifyAccessToken = async(req,res,next) => {
    logger.info('hello from verifyAccessToken.js')
    const token = req.headers["access-token"];
    const api_key = req.headers["api-key"];

    const decoded = jwt.verify(token, process.env.TOKEN_KEY);
    // req.user = decoded;
    next()
}
