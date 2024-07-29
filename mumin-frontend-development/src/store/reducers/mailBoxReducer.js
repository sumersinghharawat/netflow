import { createSlice } from "@reduxjs/toolkit";
const initialState = {
  mailList: [],
};

const mailSlice = createSlice({
  name: "mail",
  initialState,
  reducers: {
    setMails: (state, action) => {
      if (action.payload) {
        state.mailList = action?.payload;
      }
    },
    addMail: (state, action) => {
      if (action?.payload) {
        state.mailList.push(...action?.payload);
      }
    },
  },
});

export const { setMails, addMail } = mailSlice.actions;

export default mailSlice.reducer;
