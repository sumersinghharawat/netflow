import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const OrderDetail = sequelize.define("OrderDetail",
    {
        orderId: DataTypes.BIGINT.UNSIGNED,
        packageId: DataTypes.BIGINT.UNSIGNED,
        quantity: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DOUBLE,
        productPv: DataTypes.DOUBLE,
        orderStatus: DataTypes.ENUM("0", "1"),
        
    }, { sequelize }
);
export default OrderDetail;
