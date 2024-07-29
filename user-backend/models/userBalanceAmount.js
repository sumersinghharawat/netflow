import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const UserBalanceAmount = sequelize.define("UserBalanceAmount",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        balanceAmount: DataTypes.DOUBLE,
        purchaseWallet: DataTypes.DOUBLE,
    }, { sequelize }
);

export default UserBalanceAmount;


