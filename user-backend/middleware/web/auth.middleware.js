import dotenv from 'dotenv';
import { verifyToken } from "../../utils/jwtToken.js";
import { getFromDb } from '../../utils/jwtToken.js';
import { errorMessage } from '../../helper/response.js';
dotenv.config();

export default async (req, res, next) => {
	try {
		const token = req.headers["access-token"];
		if (!token) {
			return res.status(401).json({ message: 'Authorization header missing' });
		}
		const decoded 		= await verifyToken(token);
		const tokenFromDB 	= await getFromDb(decoded.id);
		
		if (!tokenFromDB || tokenFromDB.token != token) {
			let response = await errorMessage({ code: 1002 })
			return res.status(401).json(response);
		}
		delete decoded.iat;
		delete decoded.exp;
		req.auth = {
			user: decoded
		};
		return next();
	} catch (err) {
		console.log('From auth middlware:---', err.message);
		let response = await errorMessage({ code: 1076 })
		return res.status(401).json(response);
	}
};

