import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const CartPaymentReceipt = sequelize.define("CartPaymentReceipt",
    {
        image: DataTypes.STRING(255),
        userId: DataTypes.BIGINT.UNSIGNED,
        orderId: DataTypes.BIGINT.UNSIGNED,
        
    }, { sequelize }
);

export default CartPaymentReceipt;