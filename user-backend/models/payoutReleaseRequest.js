import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PayoutReleaseRequest = sequelize.define("PayoutReleaseRequest",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DOUBLE(8, 2),
        balanceAmount: DataTypes.DOUBLE(8, 2),
        status: DataTypes.TINYINT,
        readStatus: DataTypes.ENUM("0", "1"),
        payoutFee: DataTypes.DOUBLE(8, 2),
        paymentMethod: DataTypes.STRING(255),

    }, { sequelize }
);

export default PayoutReleaseRequest;


