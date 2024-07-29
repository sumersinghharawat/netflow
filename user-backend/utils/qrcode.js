import User from "../models/user.js";
import QRCode from 'qrcode';
import speakeasy from 'speakeasy';

export const generateQRCode = async(req, res, next) => {
    try {
        let QRStatus, secretKey, qrcode;
        let username = req.body.username;
        let { "goc_key": userGocKey } = await User.findOne({ attributes: ['goc_key'], where: { username: username }, raw: true })
        if (userGocKey) {
            QRStatus    = 0;
            secretKey   = userGocKey;
        } else {
            QRStatus    = 1;
            let secret  = speakeasy.generateSecret(username);
            qrcode      = await QRCode.toDataURL(secret.otpauth_url)
            secretKey   = secret.base32
        }
        let data = {
            '2faStatus': true,
            showQr: (QRStatus) ? true : false,
            secretKey: secretKey,
            authQrCode: (qrcode) ? qrcode : ''
        }
        return data;
    } catch (error) {
        return next(error);
    }

}