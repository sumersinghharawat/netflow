import { successMessage } from "../../helper/response.js";
import Country from "../../models/countries.js";
import utilityService from "../../services/utilityService.js";

export const updateCurrency = async(req, res, next) => {
    try {
        const response = await utilityService.updateCurrency(req, res, next);
		return res.status(response.code).json(response.data);
    } catch (error) {
		return next(error);
    }
};

export const updateLanguage = async (req, res, next) => {
    try {
        const response = await utilityService.updateLanguage(req, res, next);
        return res.status(response.code).json(response.data);
    } catch (error) {
        return next(error);
    }
};

export const getCountries = async(req, res,next) => {
    try {
        let countries = await Country.findAll({raw: true});
        countries = countries.map(element => ({
            label : element.name,
            value : element.id
        }));
        const response = await successMessage({data: countries});
        return res.status(response.code).json(response.data);
    } catch (error) {
        return next(error);
    }
}
