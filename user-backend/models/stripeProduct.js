import { DataTypes } from "sequelize"
import { sequelize } from "../config/db.js"

const StripeProduct = sequelize.define("StripeProduct",
    {
        productId: DataTypes.BIGINT.UNSIGNED,
        stripeProductId: DataTypes.STRING(200),
        priceId: DataTypes.STRING(200),
        productData: DataTypes.TEXT,
        priceData: DataTypes.TEXT,
        status: DataTypes.TINYINT,
        amount: DataTypes.DECIMAL(14, 4),
        type: DataTypes.STRING(255),

    }, { sequelize }
)
export default StripeProduct;

