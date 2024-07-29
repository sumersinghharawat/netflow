import { renewService } from "../../services/renew/renew";

export const RenewActions = {
  getUpgradeProducts: async () => {
    try {
      const response = await renewService.getSubscriptionDetails();
      return response.data.data;
    } catch (error) {
      return error.message;
    }
  },
  renewSubscription: async (data) => {
    try {
      const response = await renewService.renewSubscription(data);
      return response.data
    } catch (error) {
      return error
    }
  },
  AutoSubscription: async (data) => {
    try {
      const response = await renewService.autoSubscription(data);
      return response
    } catch (error) {
      return error.message
    }
  },
  CancelSubscription: async (data) => {
    try {
      const response = await renewService.cancelSubscription(data);
      return response
    } catch (error) {
      return error.message
    }
  }

};
