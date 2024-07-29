import API from "../../api/api";

export const upgradeService = {
  getUpgradeProducts: async () => {
    const response = API.get(`get-upgrade-products`);
    return response;
  },
  upgradeSubscription: async (data) => {
    const response = API.post(`upgrade`, JSON.stringify(data));
    return response;
  },
};
