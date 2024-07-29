import CrmService from "../../services/crm/crm";

export const CrmTiles = async () => {
  try {
    const response = await CrmService.crmTiles();
    return response;
  } catch (error) {
    console.log(error.message);
    return error.message;
  }
};

export const CrmGraph = async () => {
  try {
    const response = await CrmService.crmGraph();
    return response;
  } catch (error) {
    console.log(error.message);
    return error.message;
  }
};

export const FollowupToday = async (page, itemsPerPage) => {
  try {
    const response = await CrmService.followupToday(page, itemsPerPage);
    return response;
  } catch (error) {
    console.log(error.message);
    return error.message;
  }
};

export const RecentLeads = async (page, itemsPerPage) => {
  try {
    const response = await CrmService.recentLeads(page, itemsPerPage);
    return response;
  } catch (error) {
    console.log(error.message);
    return error.message;
  }
};

export const MissedFollowup = async (page, itemsPerPage) => {
  try {
    const response = await CrmService.missedFollowup(page, itemsPerPage);
    return response;
  } catch (error) {
    console.log(error.message);
    return error.message;
  }
};

export const ViewLeads = async (data, page, itemsPerPage) => {
  try {
    const response = await CrmService.viewLeads(
      data.searchTag,
      data.fromDate,
      data.toDate,
      data.nextFromDate,
      data.nextToDate,
      data.level_of_interest,
      data.country,
      data.leadStatus,
      data.statusFromDate,
      data.statusToDate,
      page,
      itemsPerPage
    );
    return response;
  } catch (error) {
    console.log(error.message);
    return error.message;
  }
};

export const EditCrmLead = async (updatedLead) => {
  try {
    const response = await CrmService.editCrmLead(updatedLead);
    return response;
  } catch (error) {
    console.log(error.message);
    return error.message;
  }
};

export const AddFollowUp = async (followUp) => {
  try {
    const response = await CrmService.addFollowUp(followUp);
    return response;
  } catch (error) {
    console.log(error.message);
    return error.message;
  }
};

export const crmTimeline = async (data) => {
  try {
    const response = await CrmService.crmTimeline(data);
    return response;
  } catch (error) {
    console.log(error.message);
    return error.message;
  }
};

export const AddCrmLead = async (data) => {
  try {
    const response = await CrmService.addCrmLead(data);
    return response
  } catch (error) {
    console.log(error);
    return error.message;
  }
}

export const GetCountries = async () => {
  try {
    const response = await CrmService.getCountries();
    return response
  } catch (error) {
    console.log(error);
    return error.message
  }
}

export const LeadDetails = async (id) => {
  try {
    const response = await CrmService.leadDetails(id);
    return response
  } catch (error) {
    console.log(error);
    return error.message
  }
}

export const addNextFollowUp = async (data) => {
  try {
    const response = await CrmService.addNextFollowUp(data);
    return response
  } catch (error) {
    console.log(error);
    return error.message
  }
}