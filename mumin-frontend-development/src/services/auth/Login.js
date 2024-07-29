import API from "../../api/api";


const LoginService = {
    authAccess: async (data) => {
        return API.post("auth/access", JSON.stringify(data))
            .then((response) => response.data)
            .catch((error) => Promise.reject(error));
    },
    logout: async () => {
        return API.post("/auth/logout")
            .then((response) => response.data)
            .catch((error) => Promise.reject(error));
    },
    forgotPassword: async (data) => {
        return API.post("/auth/forgot-password", JSON.stringify(data))
            .then((response) => response.data)
            .catch((error) => Promise.reject(error));
    },
    verifyForgotPassword: async (data) => {
        return API.post('/auth/verify-forgot-password', data)
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    changeForgotPassword: async (data) => {
        return API.post('/auth/update-password', data)
            .then((response) => response.data)
            .catch((error) => Promise.reject(error));
    }

}

export default LoginService
