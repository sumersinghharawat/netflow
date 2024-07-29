import jwt from 'jsonwebtoken';
import AccessKey from '../models/accessKeys.js';
import consoleLog from '../helper/consoleLog.js';


const getJwtToken = async (tokenData) => jwt.sign(tokenData, process.env.TOKEN_KEY, { expiresIn: process.env.TOKEN_EXPIRY });

const setJwtToken = async(accessToken, userId) => {
    const availableToken = await AccessKey.findOne({ where: { userId }});
    if (availableToken != null) {
        await availableToken.update({ token: accessToken, }, {},);
    } else {
        await AccessKey.create({ userId, token: accessToken, expiry: 1, }, {});
    }
}

const verifyToken = async (token) => jwt.verify(token, process.env.TOKEN_KEY);

const getFromDb = async (userId) => await AccessKey.findOne({ where: { userId }, });

// Mobile App
const getJwtAppToken = async (tokenData) => jwt.sign(tokenData, process.env.APP_TOKEN_KEY, { expiresIn: process.env.TOKEN_EXPIRY });

const setJwtAppToken = async(accessToken, userId) => {
    const availableToken = await AccessKey.findOne({ where: { userId }});
    if (availableToken != null) {
        await availableToken.update({ mobileToken: accessToken});
    } else {
        await AccessKey.create({ userId, mobileToken: accessToken, expiry: 0, }, {});
    }
}
const verifyAppToken = async (token) => jwt.verify(token, process.env.APP_TOKEN_KEY);

export {
    getJwtToken,
    setJwtToken,
    verifyToken,
    getFromDb,
    setJwtAppToken,
    verifyAppToken,
    getJwtAppToken
}