import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Cart = sequelize.define("Cart",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        packageId: DataTypes.BIGINT.UNSIGNED,
        quantity: DataTypes.BIGINT.UNSIGNED,
        
    }, { sequelize }
);

export default Cart;


