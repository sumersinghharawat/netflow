import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const EwalletCommissionHistory = sequelize.define("EwalletCommissionHistory",
    {

        userId: DataTypes.BIGINT.UNSIGNED,
        fromId: DataTypes.BIGINT.UNSIGNED,
        legAmountId: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DECIMAL(14, 4),
        purchaseWallet: DataTypes.DECIMAL(14, 4),
        balance: DataTypes.DECIMAL(14, 4),
        amountType: DataTypes.STRING(255),
        dateAdded: DataTypes.DATE,

    }, { sequelize }
);

export default EwalletCommissionHistory;


