import axios from "axios";
import API from "../../api/api";
import { BASE_URL, DEFAULT_KEY } from "../../config/config";

export const toolsServices = {
  getFaqs: async () => {
    return API.get(`get-faqs`)
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  getNews: async () => {
    return API.get(`all-news`)
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  getNewsById: async (newsId) => {
    return API.get(`get-news-article?newsId=${newsId}`)
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  getLeads: async (page, itemsPerPage) => {
    return API.get(`leads?page=${page}&perPage=${itemsPerPage}`)
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  searchLead: async (name) => {
    return API.get(`search-lead?name=${name}`)
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  updateLead: async (editFormData, leadId) => {
    return API.patch(`update-lead/${leadId}`, JSON.stringify(editFormData))
      .then((response) => response.data)
      .catch((error) => console.log(error.message));
  },
  getReplicaBanner: async () => {
    return API.get(`get-replica-banner`)
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  uploadReplicaBanner: async (files) => {
    const formData = new FormData();
    [...files].forEach((file, i) => {
      formData.append("file", file);
    });
    // Create a new Axios instance for this specific request
    const customAxios = axios.create({
      baseURL: BASE_URL,
    });

    // Copy the api-key and access-token headers from the API instance to customAxios
    customAxios.defaults.headers.common["api-key"] =
      localStorage.getItem("api-key") || DEFAULT_KEY;
    customAxios.defaults.headers.common["access-token"] =
      localStorage.getItem("access-token") || "";

    // Set the "Content-Type" header to "multipart/form-data"
    customAxios.defaults.headers.common["Content-Type"] = "multipart/form-data";

    try {
      const response = await customAxios.post(
        `upload-replica-banner`,
        formData
      );
      return response.data;
    } catch (error) {
      return error.response.data;
    }
  },
  deleteReplicaBanner: async (body) => {
    return API.delete(`delete-replica-banner`, { data: JSON.stringify(body) })
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  getDownloadMaterials: async () => {
    return API.get(`downloadable-material`)
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
};
