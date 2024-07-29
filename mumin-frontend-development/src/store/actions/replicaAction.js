import { ReplicaService } from "../../services/replica/replica"


export const getApiKey = async (adminUsername) => {
    try {
        const response = await ReplicaService.getApiKey(adminUsername);
        return response
    } catch (error) {
        return error.message
    }
}

export const ReplicaHome = async () => {
    try {
        const response = await ReplicaService.getReplicaHome();
        return response
    } catch (error) {
        return error.message
    }
}

export const ReplicaRegisterFields = async () => {
    try {
        const response = await ReplicaService.getReplicaRegister();
        return response
    } catch (error) {
        return error.message
    }
}

export const replicaFieldCheck = async (field, value) => {
    try {
        const response = await ReplicaService.getFieldCheck(field, value);
        return response
    } catch (error) {
        return error.message
    }
}

export const ReplicaBankUploadReceipt = async (data, username, referralId, type) => {
    try {
        const response = await ReplicaService.callBankUpload(data, username, referralId, type);
        return response
    } catch (error) {
        return error.message
    }
}

export const ReplicaBankRecieptDelete = async (data) => {
    try {
        const response = await ReplicaService.ReplicaBankRecieptDelete(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const ReplicaRegisterPost = async (data) => {
    try {
        const response = await ReplicaService.CallReplicaRegister(data);
        return response
    } catch (error) {
        return error.message
    }
}

export const ReplicaContactUpload = async (data) => {
    try {
        const response = await ReplicaService.replicaContactUpload(data);
        return response
    } catch (error) {
        return error.message
    }
}