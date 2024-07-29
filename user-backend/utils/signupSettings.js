import SignupSettings from "../models/signupSettings.js";

export const signupSettings = async () =>  await SignupSettings.findOne({raw:true});
