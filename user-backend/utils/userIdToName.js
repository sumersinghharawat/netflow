import User from "../models/user.js";

export default async (id) => {
    const user =  await User.findOne( { 
        where: { id, active: 1, userType: 'user' },
        attributes: [ "id", "username" ]
    });
    if(user) return user.username;
    return false;
}