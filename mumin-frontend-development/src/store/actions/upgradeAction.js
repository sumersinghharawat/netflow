import { upgradeService } from "../../services/upgrade/upgrade";

export const UpgradeActions = {
  getUpgradeProducts: async () => {
    try {
      const response = await upgradeService.getUpgradeProducts();
      return response.data.data;
    } catch (error) {
      return error.message;
    }
  },
  upgradeSubscription: async (data) => {
    try {
      const response = await upgradeService.upgradeSubscription(data);
      return response.data
    } catch (error) {
      return error.message
    }
  }
};
