import API from "../../api/api";

export const demoServices = {
    callDemoVisitorData: async (data) => {
        return API.post("add-demo-visitor", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callVerifyOtp: async (data) => {
        return API.post("verify-otp", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callCheckIsPresent: async () => {
        return API.get("check-is-preset")
        .then((response) => response)
        .catch((error) => Promise.reject(error));
    },
    callResendOtp: async (data) => {
        return API.post("resend-otp", JSON.stringify(data))
        .then((response) => response)
        .catch((error) => Promise.reject(error));
    }
}