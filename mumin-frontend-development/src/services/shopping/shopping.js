import API from "../../api/api";


const callApi = async (endpoint) => {
    try {
        const response = await API.get(endpoint);
        if (response.status === 200) {
            return response?.data?.data;
        } else {
            return response;
        }
    } catch (error) {
        console.log(error);
        throw error;
    }
};

export const ShoppingService = {
    callRepurchaseItems: async () => {
        const response = await callApi('repurchase-products');
        return response
    },
    callCartItems: async () => {
        const response = await callApi('get-cart');
        return response
    },
    callAddToCart: async (data) => {
        return API.post("add-to-cart", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callDecrementCartItem: async (data) => {
        return API.patch("decrement-cart-item", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callRemoveCartItem: async (data) => {
        return API.delete("remove-cart-item", { data: JSON.stringify(data) })
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callAddAddress: async (data) => {
        return API.post("add-address", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callAddress: async () => {
        const response = await callApi('get-address');
        return response
    },
    callPaymentMethods: async (action) => {
        const response = await callApi(`payment-methods?action=${action}`)
        return response
    },
    callRemoveAddress: async (data) => {
        return API.delete("delete-address", { data: JSON.stringify(data) })
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callProductDetails: async (id) => {
        const response = await callApi(`repurchase-product-detail?id=${id}`);
        return response
    },
    callDefaultAddressChange: async (id) => {
        return API.patch(`change-default-address?newDefaultId=${id}`)
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callPlaceRepurchaseOrder: async (data) => {
        return API.post("place-repurchase-order", JSON.stringify(data))
            .then((response) => response)
            .catch((error) => Promise.reject(error));
    },
    callRepurchaseReport: async (page,limit) => {
        const response = await callApi(`repurchase-report?page=${page}&perPage=${limit}&direction=ASC`)
        return response
    },
    callPurchaseInvoice: async (orderId) => {
        const response = await callApi(`repurchase-invoice?orderId=${orderId}`)
        return response
    }
}