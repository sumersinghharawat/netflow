import { toolsServices } from "../../services/Tools/tools";

export const getFaqs = async () => {
  try {
    const response = toolsServices.getFaqs();
    return response;
  } catch (error) {
    return error.message;
  }
};

export const getNews = async () => {
  try {
    const response = toolsServices.getNews();
    return response;
  } catch (error) {
    return error.message;
  }
};

export const getNewsById = async (newsId) => {
  try {
    const response = toolsServices.getNewsById(newsId);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const getLeads = async (page, itemsPerPage) => {
  try {
    const response = toolsServices.getLeads(page, itemsPerPage);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const searchLeads = async (name) => {
  try {
    const response = toolsServices.searchLead(name);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const updateLead = async (data) => {
  try {
    const id = data.leadId;
    delete data.leadId;

    const response = toolsServices.updateLead(data, id);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const getReplicaBanner = async () => {
  try {
    const response = toolsServices.getReplicaBanner();
    return response;
  } catch (error) {
    return error.message;
  }
};

export const uploadReplicaBanner = async (file) => {
  try {
    const response = toolsServices.uploadReplicaBanner(file);
    return response;
  } catch (error) {
    return error.response.data;
  }
};

export const deleteReplicaBanner = async (bannerId) => {
  try {
    const response = toolsServices.deleteReplicaBanner(bannerId);
    return response;
  } catch (error) {
    return error.response.data;
  }
};

export const getDownloadMaterials = async () => {
  try {
    const response = toolsServices.getDownloadMaterials();
    return response;
  } catch (error) {
    return error.response.data;
  }
};
