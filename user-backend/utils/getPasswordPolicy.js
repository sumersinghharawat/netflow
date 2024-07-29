import PasswordPolicy from "../models/passwordPolicy.js";


export default async () => await PasswordPolicy.findOne({
    attributes: ['id', 'mixedCase', 'number', 'spChar', 'minLength', 'enablePolicy'],
    raw: true
});