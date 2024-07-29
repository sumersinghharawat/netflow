import axios from "axios";
import API from "../../api/api";
import { BASE_URL } from "../../config/config";

const callApi = async (endpoint) => {
  try {
    const response = await API.get(endpoint);
    if (response.status === 200) {
      return response?.data?.data;
    } else {
      return response?.data?.data;
    }
  } catch (error) {
    console.log(error);
    throw error;
  }
};

export const LcpServices = {
  getApiKey: async (adminUsername) => {
    return callApi(`get-api-key?admin_username=${adminUsername}`);
  },
  getCompanyDetails: async (referID, hash) => {
    const customAxios = axios.create({
      baseURL: BASE_URL,
    });

    customAxios.defaults.headers.common["api-key"] =
      localStorage.getItem("apiKey");

    try {
      const response = await customAxios.get(`get-company-details?referralId=${referID}&hash=${hash}`);
      return response.data;
    } catch (error) {
      return Promise.reject(error);
    }
  },
  AddLcpLead: async (body,referID,hash) => {
    const customAxios = axios.create({
        baseURL: BASE_URL,
      });
  
      customAxios.defaults.headers.common["api-key"] =
        localStorage.getItem("apiKey");

    try {
        const response =await customAxios.post(`add-lcp-lead?referralId=${referID}&hash=${hash}`, body)
        return response.data
    } catch (error) {
        return error.response
    }
  },
};
