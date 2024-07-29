import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"


const PaypalProduct = sequelize.define("PaypalProduct",
    {
        productId: DataTypes.BIGINT.UNSIGNED,
        paypalProductId: DataTypes.STRING(200),
        planId: DataTypes.STRING(200),
        productData: DataTypes.TEXT,
        planData: DataTypes.TEXT,
        status: DataTypes.TINYINT,
        amount: DataTypes.DECIMAL(14, 4),
        type: DataTypes.STRING(255),

    }, { sequelize }
)
export default PaypalProduct;

