import { RegisterService } from "../../services/register/register";

export const RegisterFields = async () => {
  try {
    const response = await RegisterService.callRegisterFields();
    return response;
  } catch (error) {
    return error.message;
  }
};

export const RegisterFieldCheck = async (field, value) => {
  try {
    const response = await RegisterService.callRegisterFieldCheck(field, value);
    return response;
  } catch (error) {
    console.log(error);
  }
};
export const CreatePaymentIntent=async(data)=>{
  try {
      const response = await RegisterService.createPaymentIntent(data)
      return response
  } catch (error) {
      return error.response.data
  }
}
export const GetPaymentGatewayKey=async(paymentId)=>{
  try {
      const response = await RegisterService.getPaymentGatewayKey(paymentId)
      return response
  } catch (error) {
      return error.response.data
  }
}
export const TranssPassCheck = async (value, totalAmount) => {
  try {
    const body = {
      transPassword: value,
      totalAmount: totalAmount,
    };
    const response = await RegisterService.callTransPassCheck(body);
    return response;
  } catch (error) {
    console.log(error);
  }
};

export const RegisterUser = async (value) => {
  try {
    const response = await RegisterService.callRegisterUser(value);
    return response;
  } catch (error) {
    console.log(error);
  }
};
export const CreateStoreLink = async () => {
  try {
    const response = await RegisterService.callEcomStoreLink();
    return response;
  } catch (error) {
    console.log(error);
  }
};

export const CreateRegisterLink = async (regFromTreePayload) => {
  try {
    const response = await RegisterService.callEcomRegisterLink(
      regFromTreePayload
    );
    return response;
  } catch (error) {
    console.log(error);
  }
};

export const BankUpload = async (data, username, type) => {
  try {
    const response = await RegisterService.callBankUpload(data, username, type);
    return response;
  } catch (error) {
    return error.response.data;
  }
};

export const deleteBankReceipt = async (data) => {
  try {
    const response = await RegisterService.deleteBankReceipt(data);
    return response;
  } catch (error) {
    return error.response.data;
  }
};

export const LetterPreview = async (username) => {
  try {
    const response = await RegisterService.callLetterPreview(username);
    return response;
  } catch (error) {
    return error.response.data;
  }
};
