import axios from "axios";
import { BASE_URL, DEFAULT_KEY } from "../config/config";

const instance = axios.create({
    baseURL: BASE_URL,
});

instance.interceptors.response.use(
    (response) => {
        if (response.status === 200) {
            return response;
        }
    },
    (error) => {
        if (error.response && error.response.status === 401) {
          if (error.response.data.data.data.code === 1042) {
            return error.response.data.data;
          }
          localStorage.clear();
          window.location.href = "/login"; 
          return Promise.reject(error);
        } else {
          return error.response.data;
        }
    }
);

instance.interceptors.request.use((config) => {
    config.headers["Accept"] = "application/json";
    config.headers["Content-Type"] = "application/json";
    config.headers["api-key"] = DEFAULT_KEY;

    if(!(config.url.search('replica-home')) || !(config.url.search('replica-register-get')) ) {
        config.params = {
            referralId: localStorage.getItem('referralId'),
            hash: localStorage.getItem('hashKey')
        };
    }
    return config;
});

const replicaAPI = instance;

export default replicaAPI;
