import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const EwalletPaymentDetail = sequelize.define("EwalletPaymentDetail",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        usedUser: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DOUBLE(8, 2),
        usedFor: DataTypes.STRING(255),
        transactionId: DataTypes.BIGINT.UNSIGNED,
        
    }, { sequelize }
);
export default EwalletPaymentDetail;

