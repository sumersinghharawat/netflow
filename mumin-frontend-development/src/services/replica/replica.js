import axios from "axios";
import replicaAPI from "../../api/replicaApi";
import { BASE_URL, DEFAULT_KEY } from "../../config/config";

const callApi = async (endpoint) => {
  try {
    const response = await replicaAPI.get(endpoint);
    if (response.status === 200) {
      return response?.data;
    } else {
      return response;
    }
  } catch (error) {
    console.log(error);
    throw error;
  }
};

export const ReplicaService = {
  getApiKey: async (admin) => {
    const adminUsername = admin ?? localStorage.getItem("admin_user_name");
    const response = await callApi(
      `get-api-key?admin_username=${adminUsername}`
    );
    return response;
  },
  getReplicaHome: async () => {
    const response = await callApi("replica-home");
    return response;
  },
  getReplicaRegister: async () => {
    const response = await callApi("replica-register-get");
    return response;
  },
  getFieldCheck: async (field, value) => {
    const response = await callApi(
      `replica-checkUsernameEmail?field=${field}&value=${value}`
    );
    return response;
  },
  callBankUpload: async (data, username, referralId, type) => {
    const formData = new FormData();
    formData.append("file", data);
    formData.append("username", username);
    formData.append("referralId", referralId);

    // Create a new Axios instance for this specific request
    const customAxios = axios.create({
      baseURL: BASE_URL,
    });

        // Copy the api-key and access-token headers from the API instance to customAxios
        customAxios.defaults.headers.common["api-key"] = DEFAULT_KEY

    // Set the "Content-Type" header to "multipart/form-data"
    customAxios.defaults.headers.common["Content-Type"] = "multipart/form-data";

    try {
      const response = await customAxios.post(
        `replica-payment-receipt-upload?type=${type}`,
        formData
      );
      return response.data;
    } catch (error) {
      return error.response.data;
    }
  },
  ReplicaBankRecieptDelete: async (data) => {
    return replicaAPI
      .post(`replica-payment-receipt-delete`, data)
      .then((response) => response.data)
      .catch((error) => error);
  },
  CallReplicaRegister: async (data) => {
    return replicaAPI
      .post("replica-register-post", JSON.stringify(data))
      .then((response) => response.data)
      .catch((error) => error);
  },
  replicaContactUpload: async (data) => {
    return replicaAPI
      .post("replica-contact-upload", JSON.stringify(data))
      .then((response) => response.data)
      .catch((error) => error);
  },
};
