import API from "../../api/api";

const callApi = async (endpoint) => {
  try {
    const response = await API.get(endpoint);
    if (response.status === 200) {
      return response?.data;
    } else {
      return response?.data?.data;
    }
  } catch (error) {
    console.log(error);
    throw error;
  }
};

export const MailServices = {
  getInboxes: async (page, limit) => {
    return callApi(`inbox?page=${page}&perPage=${limit}`);
  },
  getSingleMail: async (id, type) => {
    return callApi(`view-single-mail?mailId=${id}&type=${type}`);
  },
  replyMail: async (replyMail) => {
    return API.post("reply-to-mail", JSON.stringify(replyMail))
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  getInboxFromAdmin: async (page, limit) => {
    return callApi(`inbox-from-admin?page=${page}&perPage=${limit}`);
  },
  sendInternalMail: async (mailContent) => {
    return API.post(`send-internal-mail`, JSON.stringify(mailContent))
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  deleteMail: async (mailId) => {
    return API.post(`delete-mail`, mailId)
      .then((response) => response)
      .catch((error) => Promise.reject(error));
  },
  sentMail: async (page, limit) => {
    return callApi(`sent-mail?page=${page}&perPage=${limit}`);
  },
  contacts: async (page, limit) => {
    return callApi(`contacts?page=${page}&perPage=${limit}`);
  },
};
