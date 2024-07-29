import axios from "axios";
import API from "../../api/api";
import { BASE_URL, DEFAULT_KEY } from "../../config/config";

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

const CrmService = {
  crmTiles: async () => {
    return callApi("crm-tiles");
  },
  crmGraph: async () => {
    return callApi("crm-graph");
  },
  followupToday: async (page, itemsPerPage) => {
    return callApi(`crm-followups-today?page=${page}&perPage=${itemsPerPage}`);
  },
  recentLeads: async (page, itemsPerPage) => {
    return callApi(`crm-recent-leads?page=${page}&perPage=${itemsPerPage}`);
  },
  missedFollowup: async (page, itemsPerPage) => {
    return callApi(`crm-missed-followups?page=${page}&perPage=${itemsPerPage}`);
  },
  viewLeads: async (
    tag,
    addedFromDate,
    addedToDate,
    nextToDate,
    nextFromDate,
    levelOfInterest = "all",
    country,
    leadStatus = "all",
    statusFromDate,
    statusToDate,
    page, itemsPerPage
  ) => {
    return callApi(
      `view-crm-leads?tag=${tag}&status=${leadStatus}&nextFollowupToDate=${nextToDate}&nextFollowupFromDate=${nextFromDate}&addedDateFrom=${addedFromDate}&addedDateTo=${addedToDate}&levelofIntereset=${levelOfInterest}&statusChangeDateFrom=${statusFromDate}&statusChangeDateTo=${statusToDate}&country=${country}&page=${page}&perPage=${itemsPerPage}`
    );
  },
  editCrmLead: async (updatedLead) => {
    return API.patch(`edit-crm-lead`, updatedLead);
  },
  addFollowUp: async (followUp) => {
    const formData = new FormData();
    [...followUp.files].forEach((file, i) => {
      formData.append(`file`, file);
    });
    formData.append("id", followUp.Id);
    formData.append("description", followUp.description);
    formData.append("followupDate", followUp.followupDate);
    const customAxios = axios.create({
      baseURL: BASE_URL,
    });

    customAxios.defaults.headers.common["api-key"] =
      localStorage.getItem("api-key") || DEFAULT_KEY;
    customAxios.defaults.headers.common["access-token"] =
      localStorage.getItem("access-token") || "";

    customAxios.defaults.headers.common["Content-Type"] = "multipart/form-data";
    try {
      const response = await customAxios.post(`add-crm-followup?type=crm`, formData);
      return response.data;
    } catch (error) {
      return error?.response?.data;
    }
  },
  crmTimeline: async (data) => {
    return callApi(`crm-timeline?id=${data}`);
  },
  getCountries: async () => {
    return callApi("get-countries");
  },
  addCrmLead: async (data) => {
    return API.post("add-crm-lead", JSON.stringify(data))
      .then((response) => response.data)
      .catch((error) => Promise.reject(error));
  },
  leadDetails: async (id) => {
    return callApi(`view-single-lead?id=${id}`);
  },
  addNextFollowUp: async (data) => {
    return API.post("add-next-crm-followup", JSON.stringify(data))
      .then((response) => response.data)
      .catch((error) => Promise.reject(error));
  },
};

export default CrmService;
