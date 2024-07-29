import cryptoJs from "crypto-js";
import consoleLog from "./consoleLog.js";

export const makeHash = async({ param, hashKey }) => {
    return cryptoJs.HmacSHA256(param, hashKey).toString(cryptoJs.enc.Hex);
}

export const getLeadCompletion = (lead) => {
    const leadCompletion = Math.min(
        (lead.firstName ? 15 : 0) +
        (lead.lastName ? 10 : 0) +
        (lead.emailId ? 15 : 0) +
        (lead.skypeId ? 15 : 0) +
        (lead.mobileNo ? 15 : 0) +
        (lead.countryId ? 15 : 0) +
        (lead.description ? 15 : 0),
        100
    );
    const colour = leadCompletion <= 50
            ? "rgba(245, 107, 107, 1)"
            : leadCompletion <= 75
            ? "rgba(245, 135, 10, 1)"
            : "rgba(50, 200, 150, 1)";
    return {leadCompletion, colour}
}