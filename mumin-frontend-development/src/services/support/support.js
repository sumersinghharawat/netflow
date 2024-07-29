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

export const SupportServices = {
    getTickets: async (page, itemsPerPage, category, priority, ticketId, status) => {
        const response = await callApi(`/tickets?trackId=${ticketId}&category=${category}&priority=${priority}&status=${status}&page=${page}&perPage=${itemsPerPage}`);
        return response
    },
    getTicketPartials: async () => {
        const response = await callApi(`/ticket-partials`);
        return response
    },
    getTrackId: async () => {
        const response = await callApi('ticket-trackId');
        return response
    },
    createTicket: async (data) => {
        const formData = new FormData();

        [...data.attachment].forEach((file, i) => {
            formData.append(`file`, file);
        });
        formData.append('categoryId', data.category)
        formData.append('subject', data.subject)
        formData.append('trackId', data.ticketId)
        formData.append('message', data.message)

        const customAxios = axios.create({
            baseURL: BASE_URL,
        });

        customAxios.defaults.headers.common["api-key"] =
            localStorage.getItem("api-key") || DEFAULT_KEY;
        customAxios.defaults.headers.common["access-token"] =
            localStorage.getItem("access-token") || "";

        customAxios.defaults.headers.common["Content-Type"] = "multipart/form-data";

        try {
            const response = await customAxios.post(`ticket`, formData);
            return response.data;
        } catch (error) {
            return error?.response?.data;
        }
    },
    getTicketDetails: async (trackId) => {
        const response = await callApi(`ticket-details/${trackId}`)
        return response
    },
    getTicketReplies: async (trackId) => {
        const response = await callApi(`ticket-replies/${trackId}`)
        return response
    },
    ticketReply: async (data) => {
        const formData = new FormData();

        [...data.files].forEach((file, i) => {
            formData.append(`file`, file);
        });
        formData.append('msg', data.message)

        const customAxios = axios.create({
            baseURL: BASE_URL,
        });

        customAxios.defaults.headers.common["api-key"] =
            localStorage.getItem("api-key") || DEFAULT_KEY;
        customAxios.defaults.headers.common["access-token"] =
            localStorage.getItem("access-token") || "";

        customAxios.defaults.headers.common["Content-Type"] = "multipart/form-data";

        try {
            const response = await customAxios.put(`ticket-chat/${data.trackId}`, formData);
            return response.data;
        } catch (error) {
            return error?.response?.data;
        }
    },
    getTicketFaqs: async () => {
        const response = await callApi("ticket-faq");
        return response
    },
    ticketTimeline: async (trackId) => {
        const response = await callApi(`ticket-timeline/${trackId}`);
        return response
    }
}

