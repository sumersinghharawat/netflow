import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PaypalHistory = sequelize.define("PaypalHistory",
    {
        webhookEventId: DataTypes.STRING(200),
        data: DataTypes.TEXT,
        eventType: DataTypes.STRING(200),
        subscriptionId: DataTypes.STRING(200),
        verificationStatus: DataTypes.INTEGER
    }, { sequelize });

export default PaypalHistory;