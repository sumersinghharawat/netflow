import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const StripePaymentDetail = sequelize.define("StripePaymentDetail",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        chargeId: DataTypes.STRING(255),
        productId: DataTypes.BIGINT.UNSIGNED,
        totalAmount: DataTypes.DOUBLE(8, 2),
        paymentMethod: DataTypes.STRING(255),
        stripeResponse: DataTypes.TEXT,
        orderId: DataTypes.INTEGER,

    }, { sequelize }
);

export default StripePaymentDetail;

