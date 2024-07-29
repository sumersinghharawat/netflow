import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"


const PaymentReceipt = sequelize.define("PaymentReceipt",
    {
        pendingRegistrationsId: DataTypes.BIGINT.UNSIGNED,
        receipt: DataTypes.STRING(255),
        username: DataTypes.STRING(255),
        userId: DataTypes.BIGINT.UNSIGNED,
        orderId: DataTypes.BIGINT.UNSIGNED,
        type: DataTypes.STRING(255),

    }, { sequelize }
);
export default PaymentReceipt;



