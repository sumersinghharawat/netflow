import bcrypt from "bcryptjs";
import speakeasy from "speakeasy";
import jwt from "jsonwebtoken";
import { consoleLog, errorMessage, successMessage, convertTolocal, logger } from "../../helper/index.js";
import { generateQRCode, getJwtToken, getModuleStatus, setJwtToken, signupSettings, unApprovedUserToken } from "../../utils/index.js";
import authService from "../../services/authService.js";
import User from "../../models/user.js";
import StringValidator from "../../models/stringValidator.js";
import Language from "../../models/language.js";
import { CurrencyDetail, UserDetail } from "../../models/association.js";
import { mailConfig } from "../../utils/nodeMailer.js";
import MailSetting from "../../models/mailSetting.js";
import CommonMailSetting from "../../models/commonMailSetting.js";
import utilityService from "../../services/utilityService.js";
import PasswordReset from "../../models/passwordReset.js";
import { decrypt } from "../../utils/crypto.js";
import { userIdToName } from "../../utils/index.js";
import { encrypt } from "../../utils/crypto.js";
import _ from "lodash";
import PasswordPolicy from "../../models/passwordPolicy.js";


export const getCompanyLogo = async (req, res, next) => {
	try {
		const companyDetails = await utilityService.getCompanyProfile();
		const response = await successMessage({data: companyDetails});
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM getCompanyLogo",error)
		return next(error);
	}
}

export const getAccessToken = async (req, res, next) => {
	try {
		const [signupData, moduleStatus, user] = await Promise.all([
			signupSettings(),
			getModuleStatus({}),
			authService.getUser(req)
		]);

		// Un-approved user login section
		if (signupData.loginUnapproved && !user) {
			const tokenData = await unApprovedUserToken(req, res, next);
			if (tokenData.status) {
				const response = await successMessage({ data: tokenData.data });
				return res.status(response.code).json(response.data);
			} else {
				const response = errorMessage({ code: tokenData.code, statusCode: 422 }); // "Invalid Username / Username Not Found"
				return res.status(response.code).json(response.data);
			}
		}
		if (!user) {
			const response = await errorMessage({ code: 1003, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		if (user && !user.active) {
			const response = await errorMessage({ code: 1104, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		// 2Fa Section
		if (user && moduleStatus.googleAuthStatus) {
			const qrCode = await generateQRCode(req, res, next);
			const response = await successMessage({ data: qrCode });
			return res.status(response.code).json(response.data);
		}

		// mail verification section
		if (signupData.emailVerification && !user.emailVerified) {
			const response = await errorMessage({ code: 1037, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
		const accessTokenData = await authService.getAccessToken(req, user, moduleStatus, next);
		if(!accessTokenData.status) {
			const response = await errorMessage({ code: accessTokenData.data.code, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}

		await setJwtToken(accessTokenData.accessToken, user.id)

		const [currencyDetails, languageDetails] = await Promise.all([
			CurrencyDetail.findOne({ attributes: ['id', 'code', 'title', 'value', 'symbolLeft'], where: { default: 1 } }),
			Language.findOne({ attributes: [ 'id','code', 'name', 'nameInEnglish', 'flagImage'], where: { default: 1 } })
		])
		
		const flag = languageDetails.flagImage.split('.')[0]+'.svg';
		languageDetails.flagImage = 'images/'+flag;
		let data = {
			accessToken: accessTokenData.accessToken,
			// apiKey : req.headers['api-key'],
			apiKey: req.apiKey,
			user: {
				id : user.id,
				username: user.username,
				fullName: user.UserDetail.name + " " + user.UserDetail.secondName,
				email: user.email,
				currency: user.defaultCurrency ? user.CurrencyDetail : '',
				language: user.Language ? 'images/'+user.Language.flagImage.split('.')[0]+'.svg' : '',
				dateOfJoining: convertTolocal(user.dateOfJoining)
			},
			defaultCurrency: currencyDetails,
			defaultLanguage: user.Language ? user.Language : languageDetails,
		}
		const response = await successMessage({ data});
		return res.status(response.code).json(response.data);
	} catch (error) {
		return next(error);
	}
};

export const logout = async (req, res, next) => {
	try {
		const response = await authService.logout(req, res, next);
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM Logout:- ",error);
		return res.status(401).json({ status: false });
	}
};

export const backToBackOffice = async (req, res, next) => {
	const string = req.headers['string'];
	const ip = req.headers["x-forwarded-for"] || req.socket.remoteAddress || null;
	let currencyDetails, languageDetails, tokenData, accessToken, userDashboardDetails;

	const checkString = await StringValidator.findOne({
		where: {
			string,
			status: 1
		}
	});

	if (!checkString) {
		return res.status(401).json({ status: false });
	}
	checkString.update({ status: 0 });

	const user = await User.findOne({
		attributes: ["id", "password", "username", 'userType', 'email', 'emailVerified', 'defaultCurrency', 'dateOfJoining', 'createdAt'],
		where: { id: checkString.userId },
		include: [
			{ model: UserDetail, attributes: ['name', 'secondName'] },
			{ model: CurrencyDetail, attributes: ['id', 'code', 'symbolLeft'] },
			{ model: User, as: "sponsor", attributes: ['id', 'username'] },
			{ model: User, as: "father", attributes: ['id', 'username'] },
			{ model: Language, attributes: ['id', 'code', 'name', 'nameInEnglish', 'flagImage'] },
		],
	});
	if (!user) {
		return res.status(401).json({ status: false });
	}

	tokenData = {
		id: user.id,
		username: user.username,
		user_type: user.userType
	};
	accessToken = await getJwtToken(tokenData);
	await setJwtToken(accessToken, user.id);

	currencyDetails = await CurrencyDetail.findOne({ attributes: ['id', 'code', 'title', 'value', 'symbolLeft'], where: { default: 1 } })
	languageDetails = await Language.findOne({ attributes: ['id', 'code', 'name', 'nameInEnglish', 'flagImage'], where: { default: 1 } })
	const flag = languageDetails.flagImage.split('.')[0] + '.svg';
	languageDetails.flagImage = 'images/' + flag;

	let data = {
		accessToken: accessToken,
		apiKey: req.apiKey,
		user: {
			id: user.id,
			username: user.username,
			fullName: user.UserDetail.name + " " + user.UserDetail.secondName,
			email: user.email,
			currency: user.defaultCurrency ? user.CurrencyDetail : '',
			language: user.Language ? 'images/' + user.Language.flagImage.split('.')[0] + '.svg' : '',
			dateOfJoining: convertTolocal(user.dateOfJoining)
		},
		defaultCurrency: currencyDetails,
		defaultLanguage: languageDetails,
	}
	const response = await successMessage({ data });
	return res.status(response.code).json(response.data);
};

export const forgotPassword = async (req, res, next) => {
	try {
		const { username } = req.body;
		if(!username) {
			const response = await errorMessage({ code: 1125 });
			return res.status(422).json(response.data);
		}
		const user = await User.findOne({ where: { username: username, userType: "user" } });
		if (!user) {
			const response = await errorMessage({ code: 1120 });
			return res.status(422).json(response.data);
		}
		const string = await encrypt(user.id.toString())
		const result = await PasswordReset.findOne({ where: { userId: user.id } })
		if (result) {
			await result.update({ token: string, status: 1});
		} else {
			await PasswordReset.create({
				userId: user.id,
				token: string,
				status: 1,
			});
		}
		const mailDetails = {
			link : `${process.env.SITE_URL}/forgot-password/${string}`
		};
		const mailSettings 	= await MailSetting.findOne();
		let Mail 			= await mailConfig(mailSettings);
		let mailOptions 	= await utilityService.getMailOptions({ mailSettings, email: user.email,mailDetails, userId: user.id, type: "forgot_password" });
		
		try {
			let result = await Mail.sendMail(mailOptions)
			if (result) {
				const response = await successMessage({ data: "Password Reset Link Sent to Your Registered Email" });
				return res.status(response.code).json(response.data);
			} else {
				const response = await errorMessage({ code: 1011 });
				return res.status(422).json(response.data);
			}
		} catch (error) {
			logger.error("ERROR FROM forgotPassword", error);
			throw error;
		}

	} catch (error) {
		console.log(error);
		return next(error);
	}
};

export const verifyForgotPassword = async (req, res, next) => {
	try {
		const string 	= req.body.hash;
		if(!string) {
			const response = await errorMessage({ code: 1124 });
			return res.status(422).json(response.data);
		}
		const userId 	= await decrypt(string)
		const check 	= await PasswordReset.findOne({ where: { userId: userId, token: string, status: 1 } })

		if (!check) {
			const response = await errorMessage({ code: 1121 });
			return res.status(422).json(response.data);
		}

		const issuedAt 		= check.updatedAt;
		const validDate 	= new Date(issuedAt.getTime() + (30*60000));
		const currentDate 	= new Date();
		const passwordPolicy = await PasswordPolicy.findOne();

		if (currentDate < validDate) {
			let data = {
				message: "Password hash verified.",
				username: await userIdToName(userId),
				passwordPolicy
			}
			const response = await successMessage({ data: data })
			return res.status(response.code).json(response.data);
		}

		await check.update({ status: 0 });
		const response = await errorMessage({ code: 1121, statusCode: 422 })
		return res.status(response.code).json(response.data)
	} catch (error) {
		logger.error("ERROR FROM verifyForgotPasswordLink", error)
		return next(error)
	}
}

export const updatePassword = async (req, res, next) => {
	try {
		let status = false
		const { password, hash } = req.body
		const userId 	= await decrypt(hash)
		const check 	= await PasswordReset.findOne({ where: { userId: userId, token: hash, status: 1 } });
		
		if (check) {
			const passwordUpdate = await User.update({ password: await bcrypt.hash(password, 10) }, { where: { id: userId } })
			if (passwordUpdate) {
				await check.update({ status: 0 })
				status = true
			}
		}
		let response
		if (status) {
			response = await successMessage({ data: "Password Updated Successfully" })
		} else {
			response = await errorMessage({ code: 1030, statusCode: 422 })
		}
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("ERROR FROM update password", error)
		return next(error)
	}
}

