import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PaymentGatewayDetail = sequelize.define("PaymentGatewayDetail",
    {
        paymentGatewayId: DataTypes.BIGINT.UNSIGNED,
        publicKey: DataTypes.TEXT,
        secretKey: DataTypes.TEXT,

    }, { sequelize }
);
export default PaymentGatewayDetail;


