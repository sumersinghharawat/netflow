import bcrypt from 'bcryptjs';
import { consoleLog, convertTolocal, errorMessage, logger, successMessage } from "../helper/index.js";
import { getModuleStatus, getJwtToken, getFromDb, getJwtAppToken } from "../utils/index.js";
import Language from "../models/language.js";
import { CurrencyDetail, User, UserDetail } from "../models/association.js";
import OcSessions from "../models/ocSession.js"
class authService {
    async getUser(req) {
        let { username } = req.body;
        username = username.trim();
        return await User.findOne({
            attributes: ["id", "password", "username", 'userType', 'email', 'emailVerified', 'defaultCurrency', 'dateOfJoining', 'createdAt', "active"],
            where: { username: username, user_type: "user" },
            include: [
                { model: UserDetail, attributes: ['name', 'secondName']},
                { model: CurrencyDetail, attributes: ['id', 'code', 'symbolLeft']},
                { model: User, as: "sponsor", attributes: ['id', 'username']},
                { model: User, as: "father", attributes: ['id', 'username']},
                { model: Language, attributes: ['id', 'code', 'name', 'nameInEnglish', 'flagImage']},
            ],
        });
    }
    async getAccessToken(req, user, moduleStatus, next, type = null) {
        let { username, password } = req.body;
        username = username.trim();
        let tokenData, accessToken;
        try {
            // User login process
            const validPassword = await bcrypt.compare(password, user.password);
            if (!validPassword) return  { status: false, data: {code: 1003, statusCode: 422} };

            tokenData = {
                id: user.id,
                username: user.username,
                user_type: user.userType
            };
            if(type && type == 'app') {
                accessToken = await getJwtAppToken(tokenData);
            } else {
                accessToken = await getJwtToken(tokenData);
            }

            // console.log("accessToken",accessToken);

            return { status : true, accessToken};
        } catch (error) {
            logger.error("ERROR FROM getAccessToken",error);
            throw error;
        }
    }
    async logout(req, res, next) {
        let moduleStatus 	= await getModuleStatus({attributes:["ecomStatus"]})
		const authUser 		= req.auth.user;
		const user          = await getFromDb(authUser.id);
		user.update({ token: ""});

		if (moduleStatus.ecomStatus) {
			let OcUser = await User.findOne({
				attributes: ['ecomCustomerRefId'],
				where: { id: user.id },
			})
			let sessions = await OcSessions.findOne({
				where: {
					customerId: OcUser.ecomCustomerRefId
				},
			})
			if(sessions) await sessions.destroy();
		}
        return await successMessage({ data: [] });
    }
}

export default new authService;