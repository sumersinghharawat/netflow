import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PaypalSubscription = sequelize.define("PaypalSubscription",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        productId: DataTypes.BIGINT.UNSIGNED,
        planId: DataTypes.STRING(200),
        subscriptionId: DataTypes.STRING(200),
        subscriptionData: DataTypes.TEXT,
        status: DataTypes.TINYINT,
        amount: DataTypes.DECIMAL(14, 4),
    }, { sequelize });

export default PaypalSubscription;