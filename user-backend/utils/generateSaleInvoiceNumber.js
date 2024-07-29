import consoleLog from "../helper/consoleLog.js";
import SalesOrder from "../models/salesOrder.js";
import { sequelize } from "../config/db.js";

export default async () => {
    const invoiceNo = await SalesOrder.findOne({ attributes: [
        [sequelize.literal('MAX(id)'), 'maxId']
    ], raw: true});
    return `SALE ${1000 + parseInt(invoiceNo.maxId ?? 0)}`;
}