import API from "../../api/api";

export const renewService = {
  getSubscriptionDetails: async () => {
    const response = API.get(`get-subscription-details`);
    return response;
  },
  renewSubscription: async (data) => {
    const response = API.post(`renew-subscription`, JSON.stringify(data))
    return response;
  },
  autoSubscription: async (data) => {
    return API.post(`paypal-autosubscription`, JSON.stringify(data))
      .then((response) => response)
      .catch((error) => error);
  },
  cancelSubscription: async (data) => {
    return API.post('cancel-autosubscription', JSON.stringify(data))
      .then((response) => response.data)
      .catch((error) => error);
  }
};
