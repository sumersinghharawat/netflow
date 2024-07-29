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

export const EpinService = {
    callEpinTiles: async () => {
        return callApi('epin-tiles');
    },
    callEpinList: async (page, perPage, epin, amount, status) => {
        return callApi(`epin-list?page=${page}&perPage=${perPage}&direction=&epins=${epin}&amounts=${amount}&status=${status}`);
    },
    callEpinPendingRequest: async (page, perPage) => {
        return callApi(`pending-epin-request?page=${page}&perPage=${perPage}&direction=`);
    },
    callEpinTransferHistory: async (page, perPage) => {
        return callApi(`epin-transfer-history?page=${page}&perPage=${perPage}`);
    },
    callEpinPurchase: async (data) => {
        return API.post("epin-purchase", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));

    },
    callEpinRequest: async (data) => {
        return API.post("epin-request", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callEpinTransfer: async (data) => {
        return API.post("epin-transfer", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callEpinRefund: async (data) => {
        return API.post("epin-refund", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callPurchasedEpinList: async () => {
        return callApi(`purchased-epin-list`);
    },
    callEpinPartials: async () => {
        return callApi('epin-partials');
    }
}