import { Op } from "sequelize";
import User from "../models/user.js";

export default async (username) => {
    const user =  await User.findOne( { 
        where: { username, active: 1, userType: {[Op.ne]:"employee"} },
        attributes: [ "id", "username" ]
    });
    if(user) return user;
    return false;
}