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

export const PayoutService = {
    callDetails: async (page, perPage, type) => {
        return callApi(`payout-details?page=${page}&pageSize=${perPage}&status=[${type}]`);
    },
    callPayoutRequestDetails: async () => {
        return callApi('payout-request-details')
    },
    callPayoutRequest: async (data) => {
        return API.post("payout-request", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callPayoutTiles: async () => {
        return callApi('payout-tiles');
    }
}