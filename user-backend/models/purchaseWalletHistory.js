import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PurchaseWalletHistory = sequelize.define("PurchaseWalletHistory",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        fromUserId: DataTypes.BIGINT.UNSIGNED,
        // ewalletRefid: DataTypes.BIGINT.UNSIGNED,
        transactionId: DataTypes.INTEGER,
        amount: DataTypes.DOUBLE,
        balance: DataTypes.DOUBLE,
        purchaseWallet: DataTypes.DOUBLE,
        amountType: DataTypes.STRING(255),
        date: DataTypes.DATEONLY,
        tds: DataTypes.DOUBLE,
        type: DataTypes.STRING(255),

    }, { sequelize }
);

export default PurchaseWalletHistory;


