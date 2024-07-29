import axios from "axios";
import { BASE_URL, DEFAULT_KEY } from "../config/config";
import { toast } from "react-toastify";

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
        localStorage.clear();
        window.location.href = "/login"; // Change the URL as needed
        return error.response.data.data;
      }
      localStorage.clear();
      window.location.href = "/login"; // Change the URL as needed
      return Promise.reject(error);
    } else if (error.response && error.response.status === 403) {
      if (error?.response?.data?.description) {
        toast.error(error?.response?.data?.description)
      }
      localStorage.clear();
      window.location.href = "/login"; // Change the URL as needed
      return Promise.reject(error);
    } else if (error.response.status === 500) {
      // localStorage.clear();
      // window.location.href = "/login"; // Change the URL as needed
    } else {
      return error.response.data;
    }
  }
);

instance.interceptors.request.use((config) => {
  config.headers["Accept"] = "application/json";
  config.headers["Content-Type"] = "application/json";
  config.headers["api-key"] = DEFAULT_KEY;
  config.headers["access-token"] = localStorage.getItem("access-token") || "";
  return config;
});

const API = instance;

export default API;
