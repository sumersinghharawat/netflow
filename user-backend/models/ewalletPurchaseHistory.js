import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const EwalletPurchaseHistory = sequelize.define("EwalletPurchaseHistory",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        referenceId: DataTypes.BIGINT.UNSIGNED,
        ewalletType: DataTypes.STRING(255),
        amount: DataTypes.DECIMAL(14, 4),
        balance: DataTypes.DECIMAL(14, 4),
        amountType: DataTypes.STRING(255),
        type: DataTypes.STRING(255),
        dateAdded: DataTypes.DATE,

    }, { sequelize }
)

export default EwalletPurchaseHistory;


