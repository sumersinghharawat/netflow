import { LcpServices } from "../../services/Lcp/lcp";

export const getReplicaApi = async (adminUsername) => {
  try {
    const response = await LcpServices.getApiKey(adminUsername);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const getCompanyDetails = async (referID, hash) => {
  try {
    const response = await LcpServices.getCompanyDetails(referID, hash);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const AddLcpLead = async (body) => {
  try {
    const referID = body.username;
    const hash = body.hash;

    delete body.hash;
    delete body.username;

    const response = await LcpServices.AddLcpLead(body, referID, hash);
    return response;
  } catch (error) {
    return error.message;
  }
};

