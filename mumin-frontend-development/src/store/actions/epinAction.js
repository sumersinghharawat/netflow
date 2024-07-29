import { EpinService } from "../../services/epin/epin"



export const EpinTiles = async () => {
    try {
        const response = await EpinService.callEpinTiles();
        return response
    } catch (error) {
        return error.message
    }
}

export const EpinList = async (page, perPage, epin, amount, status) => {
    try {
        const response = await EpinService.callEpinList(page, perPage, epin, amount, status);
        return response
    } catch (error) {
        return error.message
    }
}

export const EpinPendingRequest = async (page, perPage) => {
    try {
        const response = await EpinService.callEpinPendingRequest(page, perPage);
        return response
    } catch (error) {
        return error.message
    }
}

export const EpinTransferHistory = async (page, perPage) => {
    try {
        const response = await EpinService.callEpinTransferHistory(page, perPage);
        return response
    } catch (error) {
        return error.message
    }
}

export const EpinTransfer = async (data) => {
    try {
        const response = await EpinService.callEpinTransfer(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const EpinPurchase = async (data) => {
    try {
        const response = await EpinService.callEpinPurchase(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const EpinRequest = async (data) => {
    try {
        const response = await EpinService.callEpinRequest(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const EpinRefund = async (data) => {
    try {
        const response = await EpinService.callEpinRefund(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const PurchasedEpinList = async () => {
    try {
        const response = await EpinService.callPurchasedEpinList();
        return response
    } catch (error) {
        return error.message
    }
}

export const EpinPartials = async () => {
    try {
        const response = await EpinService.callEpinPartials();
        return response
    } catch (error) {
        return error.message
    }
}