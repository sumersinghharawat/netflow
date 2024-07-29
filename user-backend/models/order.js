import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Order = sequelize.define("Order",
    {
        invoiceNo: DataTypes.STRING(255),
        userId: DataTypes.BIGINT.UNSIGNED,
        orderAddressId: DataTypes.BIGINT.UNSIGNED,
        orderDate: DataTypes.DATE,
        totalAmount: DataTypes.DOUBLE,
        totalPv: DataTypes.DOUBLE,
        orderStatus: DataTypes.ENUM("0", "1"),
        paymentMethod: DataTypes.STRING(255),
        
    }, { sequelize }
);
export default Order;
