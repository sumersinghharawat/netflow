import PendingRegistration from '../models/pendingRegistration.js';
import { getJwtToken } from './jwtToken.js';


export default async (req, res, next) => {
    try {
        let { username, password } = req.body;
        let pendingUser = await PendingRegistration.findOne({ where: { username: username } });

        if (pendingUser) {
            let pendingDetails = JSON.parse(pendingUser.data)
            if (password != pendingDetails.password) {
                return { status: false,code: 1021 };
            }
            let tokenData = {
                id              : pendingUser.id,
                username        : username,
                approveStatus   : pendingUser.status
            }
            let accessToken = await getJwtToken(tokenData);
            await pendingUser.update({ user_tokens: accessToken }, {})
            return { status: true, data: { accessToken, user: tokenData }};
        } else {
            return { status : false };
        }
    } catch (error) {
        return next(error)
    }
}
