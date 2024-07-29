import { createSlice } from '@reduxjs/toolkit';
const initialState = {
  isAuthenticated: false,
  loginResponse: { data: {} },
  profile: { data: {} },
  selectedCurrency: {},
  selectedLanguage:{},
  conversionFactor: {
    currencies: [],
    selectedCurrency: null,
    defaultCurrency: null,
  },
};

const userSlice = createSlice({
  name: 'user',
  initialState,
  reducers: {
    setLoginResponse: (state, action) => {
      state.loginResponse = action.payload;
    },
    setIsAuthenticated: (state, action) => {
      state.isAuthenticated = action.payload;
    },
    setProfile: (state, action) => {
      state.profile = action.payload;
    },
    updateProfile: (state, action) => {
      const { profileDetails } = action.payload;
      const updatedProfileDetails = {
        ...state.profile.personalDetails,
        name: profileDetails.name,
        secondName: profileDetails.secondName,
        gender: profileDetails.gender
      }
      state.profile.personalDetails = updatedProfileDetails
    },
    updateContact: (state, action) => {
      const { contactDetails } = action.payload;
      const updatedContactDetails = {
        ...state.profile.contactDetails,
        address: contactDetails.address,
        address2: contactDetails.address2,
        country: contactDetails.country,
        state: contactDetails.state,
        city: contactDetails.city,
        zipCode: contactDetails.zipCode,
        email: contactDetails.email,
        mobile: contactDetails.mobile
      }
      state.profile.contactDetails = updatedContactDetails
    },
    updateBank: (state, action) => {
      const { bankDetails } = action.payload;
      const updatedBankDetails = {
        ...state.profile.bankDetails,
        bankName: bankDetails.bankName,
        branchName: bankDetails.branchName,
        holderName: bankDetails.holderName,
        accountNo: bankDetails.accountNo,
        ifsc: bankDetails.ifsc,
        pan: bankDetails.pan
      }
      state.profile.bankDetails = updatedBankDetails;
    },
    setSelectedCurrency: (state, action) => {
      if(action.payload === null) {
        state.selectedCurrency = JSON.parse(state?.loginResponse?.defaultCurrency);
      } else {
        state.selectedCurrency = action.payload;
      }
    },
    setSelectedLanguage: (state, action) => {
      if(action.payload === null) { 
        state.selectedLanguage = JSON.parse(state?.loginResponse?.defaultLanguage);
        localStorage.setItem("userLanguage",state?.loginResponse?.defaultLanguage);
      } else {
        state.selectedLanguage = action.payload
        localStorage.setItem("userLanguage",JSON.stringify(action.payload));
      }
    },
    setConversionFactors: (state, action) => {
      const { currencies, selectedCurrency, defaultCurrency } = action.payload;
      state.conversionFactor.currencies = currencies;
      state.conversionFactor.selectedCurrency = selectedCurrency;
      state.conversionFactor.defaultCurrency = defaultCurrency;
    },
    updateConversionFactors: (state, action) => {
      state.conversionFactor.selectedCurrency = action.payload;
    }
  },
});

export const { setLoginResponse, setIsAuthenticated, setProfile, updateProfile, updateContact, updateBank, setSelectedCurrency, setSelectedLanguage, setConversionFactors, updateConversionFactors } = userSlice.actions;

export default userSlice.reducer;
