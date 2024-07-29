import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const AggregateUserCommissionAndIncome = sequelize.define("AggregateUserCommissionAndIncome",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        amountType: DataTypes.STRING(255),
        totalAmount: DataTypes.DOUBLE(14, 4),
        amountPayable: DataTypes.DOUBLE(14, 4),
        purchaseWallet: DataTypes.DOUBLE(14, 4),
        tds: DataTypes.DOUBLE(14, 4),
        serviceCharge: DataTypes.DOUBLE(14, 4),
    }, { sequelize }
)


export default AggregateUserCommissionAndIncome;