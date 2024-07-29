import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PaypalOrder = sequelize.define("PaypalOrder",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        orderId: DataTypes.TEXT,
        packageId: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DOUBLE,
        currency: DataTypes.STRING(255),
        type: DataTypes.TEXT,
        status: DataTypes.TINYINT,
        
    }, { sequelize });

export default PaypalOrder;