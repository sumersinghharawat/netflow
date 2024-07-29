import axios from "axios";
import API from "../../api/api";
import { BASE_URL, DEFAULT_KEY } from "../../config/config";

const ProfileService = {
  getProfile: async () => {
    return API.get("/profile-view")
      .then((response) => response?.data?.data)
      .catch((error) => console.log(error.message));
  },
  setPersonalData: async (data) => {
    return API.patch("/update-personal-details", data)
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  },
  setContactDetails: async (data) => {
    return API.patch("/update-contact-details", data)
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  },
  updateBankDetails: async (data) => {
    return API.patch("/update-bank-details", data)
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  },
  updateUserProfilePic: async (data) => {
    const formData = new FormData();
    formData.append("file", data);
    const type = "profile";
    const customAxios = axios.create({
      baseURL: BASE_URL,
    });

    customAxios.defaults.headers.common["api-key"] = DEFAULT_KEY;
    customAxios.defaults.headers.common["access-token"] =
      localStorage.getItem("access-token") || "";
    customAxios.defaults.headers.common["Content-Type"] = "multipart/form-data";

    try {
      const response = await customAxios.post(
        `update-avatar?type=${type}`,
        formData
      );
      return response.data;
    } catch (error) {
      return error.response.data;
    }
  },
  updateAdditionalDetails: async (data) => {
    return API.patch("/update-additionalData", data)
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  },
  updatePaymentDetails: async (data) => {
    return API.patch("update-payment-details", data)
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  },
  getKycDetails: async () => {
    return API.get("kyc-details")
      .then((response) => response?.data?.data)
      .catch((error) => console.log(error.message));
  },
  getkycUploads: async (files, category, type) => {
    const formData = new FormData();

    [...files].forEach((file, i) => {
      formData.append(`file`, file, file.name);
    });

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
        `kyc-upload?category=${category}&type=${type}`,
        formData
      );
      return response.data;
    } catch (error) {
      return error?.response?.data;
    }
  },
  deleteKycFile: async (filesId) => {
    const payload = { kycArr: filesId };
    return API.post(`kyc-delete`, payload)
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  },
  deleteProfileAvatar: async () => {
    return API.patch(`remove-avatar`)
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  },
  changePassword: async (body) => {
    return API.patch(`change-user-password`, JSON.stringify(body))
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  },
  changeTransactionPassword: async (body) => {
    return API.patch(`change-transaction-password`, JSON.stringify(body))
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  }
};

export default ProfileService;
