import { PayoutService } from "../../services/payout/payout";


export const TilesAndDetails = async (page, perPage, type) => {
    try {
        const response = await PayoutService.callDetails(page, perPage, type)
        return response
    } catch (error) {
        return error.message
    }
}

export const PayoutRequestDetails = async () => {
    try {
        const response = await PayoutService.callPayoutRequestDetails()
        return response
    } catch (error) {
        return error.message
    }
}

export const PayoutRequestApi = async (data) => {
    try {
        const response = await PayoutService.callPayoutRequest(data)
        return response
    } catch (error) {
        return error.message
    }
}

export const PayoutTiles = async() => {
    try {
        const response = await PayoutService.callPayoutTiles();
        return response
    } catch (error) {
        return error.message
    }
}