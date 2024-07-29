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

export const EwalletService = {
    callTiles: async () => {
        return callApi("ewallet-tiles");
    },
    callStatement: async (page, itemsPerPage) => {
        return callApi(`ewallet-statement?page=${page}&perPage=${itemsPerPage}`)
    },
    callTransferHistory: async (page, itemsPerPage, selectedCategory, startDate, endDate) => {
        return callApi(`/ewallet-transfer-history?type=${selectedCategory}&startDate=${startDate}&endDate=${endDate}&page=${page}&perPage=${itemsPerPage}`)
    },
    callPurchaseHistory: async (page, itemsPerPage) => {
        return callApi(`purchase-wallet?page=${page}&perPage=${itemsPerPage}`)
    },
    callMyEarnings: async (page, itemsPerPage, selectedCategory, startDate, endDate) => {
        return callApi(`my-earnings?page=${page}&perPage=${itemsPerPage}&startDate=${startDate}&endDate=${endDate}&direction=desc&type=${selectedCategory}`)
    },
    callEwalletBalance: async () => {
        return callApi('get-ewallet-balance')
    },
    callFundTransfer: async (data) => {
        return API.post("fund-transfer", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    }
}