import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';


const EwalletTransferHistory = sequelize.define("EwalletTransferHistory",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        fundTransferId: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DECIMAL(14, 4),
        balance: DataTypes.DECIMAL(14, 4),
        amountType: DataTypes.STRING(255),
        type: DataTypes.STRING(255),
        dateAdded: DataTypes.DATE,
        transactionId: DataTypes.TEXT,
        transactionNote: DataTypes.TEXT,
        transactionFee: DataTypes.FLOAT,

    }, { sequelize }
);

export default EwalletTransferHistory;


