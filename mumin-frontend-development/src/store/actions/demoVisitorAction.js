import { demoServices } from "../../services/demo-visitor/demoVisitor"

export const callCheckIsPresent = async () => {
    try {
        const response = await demoServices.callCheckIsPresent();
        return response?.data
    } catch (error) {
        return error.message
    }
};

export const callDemoVisitorData = async (data) => {
    try {
        const response = await demoServices.callDemoVisitorData(data);
        return response.data
    } catch (error) {
        return error.message
    }
};

export const callResendOtp = async (data) => {
    try {
        const response = await demoServices.callResendOtp(data);
        return response.data
    } catch (error) {
        return error.message
    }
};

export const callVerifyOtp = async (data) => {
    try {
        const response = await demoServices.callVerifyOtp(data);
        return response.data
    } catch (error) {
        return error.message
    }
};