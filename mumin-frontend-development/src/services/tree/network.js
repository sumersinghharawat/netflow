import API from "../../api/api";

const callGetApi = async (endpoint, params) => {
  try {
    const response = await API.get(endpoint, { params });
    if (response.status === 200) {
      return response.data;
    } else {
      return response;
    }
  } catch (error) {
    throw error;
  }
};

export const GenealogyService = {
  getTreelist: async (userId, userName) => {
    return callGetApi(`get-genealogy-tree?userId=${userId}&username=${userName}`)
  },
  getUnilevelMore: async (fatherId, position) => {
    const endpoint = `get-unilevel-more`;
    const params = { fatherId: fatherId, position: position }
    return callGetApi(endpoint, params);
  }
};

export const TreeViewService = {
  getTreelist: async (userId) => {
    return callGetApi(`get-tree-view?userId=${userId}`)
  },
};

export const SponserTreeService = {
  getTreelist: async (userId, userName) => {
    return callGetApi(`get-sponsor-tree?userId=${userId}&username=${userName}`)
  },
  getSponserTreeMore: async (sponsorId, position) => {
    return callGetApi(`get-sponsor-tree-more?sponsorId=${sponsorId}&position=${position}`)
  }
}

export const DownlineMembersService = {
  callDownline: async (level, page, itemsPerPage) => {
    const endpoint = `get-downlines`;
    const params = { level: level, page: page, perPage: itemsPerPage }
    return callGetApi(endpoint, params);
    // return callApi(`get-downlines?level=${level}&page=${page}&perPage=${itemsPerPage}`)
  },
  callHeader: async () => {
    const endpoint = `get-downline-header`;
    return callGetApi(endpoint);
  }
}

export const ReferralMembersService = {
  callReferral: async (level, page, itemsPerPage) => {
    const endpoint = `get-referrals`;
    const params = { level: level, page: page, perPage: itemsPerPage }
    return callGetApi(endpoint, params);
  },
  callHeader: async () => {
    const endpoint = `get-referral-header`;
    return callGetApi(endpoint);
  }
}

