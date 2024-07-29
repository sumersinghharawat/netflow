import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const AmountPaid = sequelize.define("AmountPaid",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DOUBLE(8, 2),
        date: DataTypes.DATE,
        type: DataTypes.STRING(255),
        payoutFee: DataTypes.DOUBLE(8, 2),
        transactionId: DataTypes.BIGINT.UNSIGNED,
        status: DataTypes.ENUM("0", "1"),
        paymentMethod: DataTypes.STRING(255),
        requestId: DataTypes.BIGINT,
    },
    { sequelize }
);

export default AmountPaid;

