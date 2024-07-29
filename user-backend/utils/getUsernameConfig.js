import UsernameConfig from "../models/usernameConfig.js";


export default async () => await UsernameConfig.findOne({ raw: true });