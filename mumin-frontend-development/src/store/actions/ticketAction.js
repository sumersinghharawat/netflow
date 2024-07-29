import { SupportServices } from "../../services/support/support";


export const getTickets = async (page,itemsPerPage,category,priority,ticketId,status) => {
    try {
        const response = await SupportServices.getTickets(page,itemsPerPage,category,priority,ticketId,status);
        return response
    } catch (error) {
        console.log(error);
        return error.message
    }
}

export const getTicketPartials = async () => {
    try {
        const response = await SupportServices.getTicketPartials();
        return response
    } catch (error) {
        console.log(error);
        return error.message
    }
}

export const getTrackId = async () => {
    try {
        const response = await SupportServices.getTrackId();
        return response
    } catch (error) {
        console.log(error);
        return error.message
    }
}

export const createTicket = async (data) => {
    try {
        const response = await SupportServices.createTicket(data);
        return response
    } catch (error) {
        console.log(error);
        return error.message
    }
}

export const getTicketDetails = async (trackId) => {
    try {
        const response = await SupportServices.getTicketDetails(trackId);
        return response
    } catch (error) {
        console.log(error);
        return error.message
    }
}

export const getTicketReplies = async (trackId) => {
    try {
        const response = await SupportServices.getTicketReplies(trackId);
        return response
    } catch (error) {
        console.log(error);
        return error.message
    }
}

export const getTicketFaqs = async () => {
    try {
        const response = await SupportServices.getTicketFaqs();
        return response
    } catch (error) {
        console.log(error);
        return error.message
    }
}

export const ticketReply = async (data) => {
    try {
        const response = await SupportServices.ticketReply(data);
        return response
    } catch (error) {
        console.log(error);
        return error.message
    }
}

export const ticketTimeline = async (trackId) => {
    try {
        const response = await SupportServices.ticketTimeline(trackId);
        return response
    } catch (error) {
        console.log(error);
        return error.message
    }
}