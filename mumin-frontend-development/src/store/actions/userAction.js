import LoginService from "../../services/auth/Login";
import DashboardService from "../../services/dashboard/Dashboard";
import ProfileService from "../../services/profile/profile";

export const loginUser = async (data) => {
  try {
    const response = await LoginService.authAccess(data);
    if (response.status) {
      localStorage.setItem("access-token", response.data.accessToken);
      localStorage.setItem("api-key", response.data.apiKey);
      localStorage.setItem("user", JSON.stringify(response.data.user));
      localStorage.setItem(
        "defaultCurrency",
        JSON.stringify(response.data.defaultCurrency)
      );
      localStorage.setItem(
        "defaultLanguage",
        JSON.stringify(response.data.defaultLanguage)
      );
      return { status: response.status, data: response };
    } else if (response?.code === 1003) {
      return { status: false, code: response?.code, data: response?.description };
    } else if (response?.code === 1042) {
      return { status: false, data: response?.description };
    } else if (response?.code) {
      return { status: false, data: response?.description };
    } else {
      return { status: false, data: response?.message }
    }
  } catch (error) {
    return error;
  }
};

export const fetchProfile = async () => {
  try {
    const response = await ProfileService.getProfile();
    return response;
  } catch (error) {
    return error.message;
  }
};

export const PersonalDetailsUpdate = async (data) => {
  try {
    const response = await ProfileService.setPersonalData(data);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const ContactDetailsUpdate = async (data) => {
  try {
    const response = await ProfileService.setContactDetails(data);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const BankDetailsUpdate = async (data) => {
  try {
    const response = await ProfileService.updateBankDetails(data);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const updateCurrency = async (body) => {
  try {
    const response = await DashboardService.multiCurrencyUpdation(body);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const updateLanguage = async (body) => {
  try {
    const response = await DashboardService.multiLanguageUpdation(body);
    return response;
  } catch (error) {
    return error.message;
  }
};

export const updateProfileAvatar = async (body) => {
  try {
    const response = await ProfileService.updateUserProfilePic(body);
    return response;
  } catch (error) {
    return error.message;
  }
}

export const logout = async () => {
  try {
    const response = await LoginService.logout();
    return response;
  } catch (error) {
    return error.message;
  }
}

export const AdditionalDetails = async (data) => {
  try {
    const response = await ProfileService.updateAdditionalDetails(data)
    return response
  } catch (error) {
    return error.message
  }
}

export const PaymentDetails = async (data) => {
  try {
    const response = await ProfileService.updatePaymentDetails(data)
    return response
  } catch (error) {
    return error.message
  }
}

export const KycDetails = async () => {
  try {
    const response = await ProfileService.getKycDetails()
    return response
  } catch (error) {
    return error.message
  }
}

export const KycUpload = async (files) => {
  try {
    const response = await ProfileService.getkycUploads(files.files, files.category, files.type)
    return response
  } catch (error) {
    return error.message
  }
}

export const deleteKycFile = async (filesId) => {
  try {
    const response = await ProfileService.deleteKycFile(filesId)
    return response
  } catch (error) {
    return error.message
  }
}

export const deleteProfileAvatar = async () => {
  try {
    const response = await ProfileService.deleteProfileAvatar()
    return response
  } catch (error) {
    return error.message
  }
}

export const changePassword = async (body) => {
  try {
    const response = await ProfileService.changePassword(body)
    return response
  } catch (error) {
    return error.message
  }
}

export const changeTransactionPassword = async (body) => {
  try {
    const response = await ProfileService.changeTransactionPassword(body)
    return response
  } catch (error) {
    return error.message
  }
}