import API from "../../api/api";


const callApi = async (endpoint) => {
  try {
    const response = await API.get(endpoint);
    if (response.status === 200) {
      return response?.data?.data;
    } else {
      return response?.data?.data;
    }
  } catch (error) {
    console.log(error);
    throw error;
  }
};

const patchApi = async (endpoint, body) => {
  try {
    const response = await API.patch(endpoint, body);
    if (response.status === 200) {
      return response?.data?.data;
    } else {
      return response?.data?.data;
    }
  } catch (error) {
    console.log(error);
    throw error;
  }
}

const DashboardService = {
  dashboardTiles: async () => {
    return callApi("dashboard-tiles");
  },
  dashboardProfile: async () => {
    return callApi("dashboard-user-profile");
  },
  appLayout: async () => {
    return callApi("app-layout");
  },
  getGraph: async (params) => {
    return callApi(`get-graph?timeFrame=${params}`);
  },
  multiCurrencyUpdation: async (body) => {
    return patchApi(`change-currency`, body);
  },
  multiLanguageUpdation: async (body) => {
    return patchApi(`change-language`, body);
  },
  notificationCall: async () => {
    return callApi('notifications');
  },
  ReadAllNotification: async () => {
    return API.post(`notifications-read-all`);
  },
  dashboardDetails: async () => {
    return callApi("dashboard-details");
  },
  topRecruiters: async () => {
    return callApi("top-recruiters");
  },
  packageOverview: async () => {
    return callApi("package-overview");
  },
  rankOverview: async () => {
    return callApi("rank-overview");
  },
  dashboardExpenses: async () => {
    return callApi("dashboard-expenses");
  }
};


export default DashboardService;
