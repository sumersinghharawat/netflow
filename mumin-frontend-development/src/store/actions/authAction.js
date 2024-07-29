import backToOffice from "../../services/auth/backToOffice";
import LoginService from "../../services/auth/Login";



export const StringValidate = async (string, code) => {
    try {
        const response = await backToOffice.accessToken(string, code)
        return response
    } catch (error) {
        console.log(error);
    }
}

export const ForgotPassword = async (data) => {
    try {
        const response = await LoginService.forgotPassword(data);
        return response
    } catch (error) {
        console.log(error);
    }
}

export const VerifyForgotPassword = async (data) => {
    try {
        const response = await LoginService.verifyForgotPassword(data);
        return response
    } catch (error) {
        console.log(error);
    }
}

export const ChangeForgotPassword = async (data) => {
    try {
        const response = await LoginService.changeForgotPassword(data);
        return response
    } catch (error) {
        console.log(error.message);
    }
}