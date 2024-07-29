import { ShoppingService } from "../../services/shopping/shopping"

export const getRepurchaseItems = async () => {
    try {
        const response = await ShoppingService.callRepurchaseItems();
        return response
    } catch (error) {
        return error.message
    }
}

export const addToCart = async (data) => {
    try {
        const response = await ShoppingService.callAddToCart(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const getCartItems = async () => {
    try {
        const response = await ShoppingService.callCartItems();
        return response
    } catch (error) {
        return error.message
    }
}

export const decrementCartItem = async (data) => {
    try {
        const response = await ShoppingService.callDecrementCartItem(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const removeCartItem = async (data) => {
    try {
        const response = await ShoppingService.callRemoveCartItem(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const addAddress = async (data) => {
    try {
        const response = await ShoppingService.callAddAddress(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const getAddress = async () => {
    try {
        const response = await ShoppingService.callAddress();
        return response
    } catch (error) {
        return error.message
    }
}

export const callPaymentMethods = async (action) => {
    try {
        const response = await ShoppingService.callPaymentMethods(action);
        return response
    } catch (error) {
        return error.message
    }
}

export const removeAddress = async (data) => {
    try {
        const response = await ShoppingService.callRemoveAddress(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const ProductDetails = async (id) => {
    try {
        const response = await ShoppingService.callProductDetails(id);
        return response
    } catch (error) {
        console.log(error.message);
    }
}

export const DefaultAddressChange = async (id) => {
    try {
        const response = await ShoppingService.callDefaultAddressChange(id);
        return response
    } catch (error) {
        console.log(error.message);
    }
}

export const PlaceRepurchaseOrder = async (data) => {
    try {
        const response = await ShoppingService.callPlaceRepurchaseOrder(data);
        return response
    } catch (error) {
        console.log(error.message);
    }
}

export const RepurchaseReport = async (page,limit) => {
    try {
        const response = await ShoppingService.callRepurchaseReport(page,limit);
        return response
    } catch (error) {
        console.log(error.message);
    }
}

export const RepurchaseInvoice = async(orderId) => {
    try {
        const response = await ShoppingService.callPurchaseInvoice(orderId);
        return response
    } catch (error) {
        console.log(error.message);       
    }
}