import { createSlice } from '@reduxjs/toolkit';
const initialState = {
  appLayout: { data: {} },
  dashboardOne: null,
};

const userSlice = createSlice({
  name: 'dashboard',
  initialState,
  reducers: {
    setAppLayout: (state, action) => {
      state.appLayout = action.payload;
    },
    setDashboardOne: (state, action) => {
      if(typeof action.payload === 'object') {
        state.dashboardOne = action.payload;
      }
    },
  },
});

export const { setAppLayout, setDashboardOne } = userSlice.actions;

export default userSlice.reducer;
