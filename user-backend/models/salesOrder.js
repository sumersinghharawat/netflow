import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const SalesOrder = sequelize.define("SalesOrder",
    {
        invoiceNo: DataTypes.STRING,
        userId: DataTypes.BIGINT.UNSIGNED,
        productId: DataTypes.BIGINT.UNSIGNED,
        ocProductId: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.FLOAT,
        productPv: DataTypes.FLOAT,
        paymentMethod: DataTypes.BIGINT.UNSIGNED,
        pendingUserId: DataTypes.BIGINT.UNSIGNED,
        regAmount: DataTypes.FLOAT
    }
);

export default SalesOrder;