import axios from "axios";
import { BASE_URL } from "../../config/config";

const webToReact = {
    accessToken: async (string,code) => {
        const customAxios = axios.create({
            baseURL: BASE_URL,
        });
        customAxios.defaults.headers.common["api-key"] = code.replace('_','');
        customAxios.defaults.headers.common["Accept"] = "application/json"
        customAxios.defaults.headers.common["string"] = string;
        
        try {
            const response = await customAxios.post(`/auth/validate-webview`);
            return response.data;
        } catch (error) {
            return Promise.reject(error);
        }
    }
}

export default webToReact