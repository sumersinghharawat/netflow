import { createSlice } from '@reduxjs/toolkit';
const initialState = {
  termsAndPolicy: { data: {} },
  companyDetails: { data: {} },
  registerLink: { data: {} }
};

const userSlice = createSlice({
  name: 'termsAndPolicy',
  initialState,
  reducers: {
    setTermsAndPolicy: (state, action) => {
      state.termsAndPolicy = { terms: action.payload?.terms, policy: action.payload?.policy };
    },
    setCompanyDetails: (state, action) => {
      state.companyDetails = action.payload
    },
    setRegisterLink: (state, action) => {
      state.registerLink = action.payload
    }
  },
});

export const { setTermsAndPolicy, setCompanyDetails, setRegisterLink } = userSlice.actions;

export default userSlice.reducer;
