import crypto from "crypto";
import dotenv from "dotenv";

dotenv.config();

const secretKey = crypto.scryptSync(process.env.SECRETKEY, 'salt', 32);
const algorithm = 'aes-256-cbc';
// const iv        = crypto.randomBytes(16); //
const iv        = "j96gdcrej7h6fi7d"

export const encrypt = async (value) => {
    console.log('from encrypt')
    const cipher = crypto.createCipheriv(algorithm, secretKey, iv);
    let encryptedValue = cipher.update(value, 'utf8', 'hex');
    encryptedValue += cipher.final('hex');
    return encryptedValue;
}

export const decrypt = async (encryptedValue) => {
    console.log("from decrypt",typeof encryptedValue)
    const decipher = crypto.createDecipheriv(algorithm, secretKey, iv);
    let decryptedValue = decipher.update(encryptedValue, 'hex', 'utf8');
    decryptedValue += decipher.final('utf8');
    return decryptedValue;
}
