import { EwalletService } from "../../services/ewallet/ewallet";


export const Tiles = async () => {
    try {
        const response = await EwalletService.callTiles();
        return response
    } catch (error) {
        return error.message
    }
}

export const Statement = async (page, itemsPerPage) => {
    try {
        const response = await EwalletService.callStatement(page, itemsPerPage);
        return response
    } catch (error) {
        return error.message
    }
}

export const TransferHistory = async (page, itemsPerPage, selectedCategory, startDate, endDate) => {
    try {
        const response = await EwalletService.callTransferHistory(page, itemsPerPage, selectedCategory, startDate, endDate);
        return response
    } catch (error) {
        return error.message
    }
}

export const PurchaseHistory = async (page, itemsPerPage) => {
    try {
        const response = await EwalletService.callPurchaseHistory(page, itemsPerPage);
        return response
    } catch (error) {
        return error.message
    }
}

export const MyEarnings = async (page, itemsPerPage, selectedCategory, startDate, endDate) => {
    try {
        const response = await EwalletService.callMyEarnings(page, itemsPerPage, selectedCategory, startDate, endDate);
        return response
    } catch (error) {
        return error.message
    }
}

export const FundTransfer = async (data) => {
    try {
        const response = await EwalletService.callFundTransfer(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const EwalletBalance = async () => {
    try {
        const response = await EwalletService.callEwalletBalance();
        return response
    } catch (error) {
        console.log(error.message);
    }
}