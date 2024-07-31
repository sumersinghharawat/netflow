import axios from "axios";
import API from "../../api/api";
import { BASE_URL, DEFAULT_KEY } from "../../config/config";

const callApi = async (endpoint) => {
  try {
    const response = await API.get(endpoint);
    if (response.status === 200) {
      return response?.data?.data;
    } else {
      return response;
    }
  } catch (error) {
    console.log(error);
    throw error;
  }
};
const postApi = async (endpoint, data) => {
  try {
    const response = await API.post(endpoint, data);
    if (response.status === 200) {
      return response?.data?.data;
    } else {
      return response;
    }
  } catch (error) {
    console.log(error);
    throw error;
  }
};
export const RegisterService = {
  callRegisterFields: async () => {
    const response = await callApi("register");
    return response;
  },
  callRegisterFieldCheck: async (field, value) => {
    const response = await callApi(
      `register-field-verification?field=${field}&value=${value}`
    );
    return response;
  },
  callTransPassCheck: async (data) => {
    return API.post("check-transaction-password", data)
      .then((response) => response.data)
      .catch((error) => Promise.reject(error));
  },
  callRegisterUser: async (data) => {
    return API.post("register", JSON.stringify(data))
      .then((response) => response.data)
      .catch((error) => Promise.reject(error));
  },
  callBankUpload: async (data, username, type) => {
    const formData = new FormData();
    formData.append("file", data);
    formData.append("username", username);

    // Create a new Axios instance for this specific request
    const customAxios = axios.create({
      baseURL: BASE_URL,
    });

    // Copy the api-key and access-token headers from the API instance to customAxios
    customAxios.defaults.headers.common["api-key"] = DEFAULT_KEY;
    customAxios.defaults.headers.common["access-token"] =
      localStorage.getItem("access-token") || "";

    // Set the "Content-Type" header to "multipart/form-data"
    customAxios.defaults.headers.common["Content-Type"] = "multipart/form-data";

    try {
      const response = await customAxios.post(
        `upload-bank-receipt?type=${type}`,
        formData
      );
      return response.data;
    } catch (error) {
      return error.response.data;
    }
  },
  deleteBankReceipt: async (data) => {
    const response = await API.post(`remove-bank-receipt`, data);
    return response?.data;
  },
  callEcomRegisterLink: async (data) => {
    const response = await callApi(
      `ecom-register-link?regFromTree=${data.regFromTree}&position=${data.position}&placement=${data.placement}`
    );
    return response;
  },
  callEcomStoreLink: async () => {
    const response = await callApi(`ecom-store-link`);
    return response;
  },
  callLetterPreview: async (username) => {
    const response = await callApi(`letter-preview?username=${username}`);
    return response;
  },
  createPaymentIntent: async (data) => {
    const response = await postApi(`create-payment-intent`, data);
    return response;
  },
  getPaymentGatewayKey: async (paymentId) => {
    const nowpaymentKey = localStorage.getItem("nowpaymentKey");
    const headers = {
      'x-api-key': nowpaymentKey,
      'Content-Type': 'application/json'
    };

    const response = await API.get(`payment-gateway-key`+"?paymentMethod="+paymentId,{headers:headers});
    console.log(response);
    return response;
  },
};
