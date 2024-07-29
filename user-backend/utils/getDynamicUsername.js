import User from "../models/user.js";
import logger from "../helper/logger.js";


const getDynamicUsername = async (next,usernameSettings) => {
    try {
        let username;
        let number = Math.floor(Math.random() * 1000000);
        if (usernameSettings.prefixStatus) {
            let prefix = usernameSettings.prefix;
            username = `${prefix}${number}`;
        }
        let check = await User.findOne({ where: { username } });
        if (check) {
            await this.getDynamicUsername(next,usernameSettings)
        } else {
            return username;
        }
    } catch (error) {
        logger.error("ERROR FROM getDynamicUsername",error)
        return next(error)
    }
}

export default getDynamicUsername