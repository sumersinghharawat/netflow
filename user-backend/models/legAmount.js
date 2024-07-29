import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const LegAmount = sequelize.define("LegAmount",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        fromId: DataTypes.BIGINT.UNSIGNED,
        totalLeg: DataTypes.INTEGER,
        leftLeg: DataTypes.INTEGER,
        rightLeg: DataTypes.INTEGER,
        totalAmount: DataTypes.DOUBLE(8, 2),
        amountPayable: DataTypes.DOUBLE(8, 2),
        purchaseWallet: DataTypes.DOUBLE,
        amountType: DataTypes.STRING(255),
        tds: DataTypes.DOUBLE(8, 2),
        serviceCharge: DataTypes.DOUBLE(8, 2),
        userLevel: DataTypes.INTEGER,
        productId: DataTypes.BIGINT.UNSIGNED,
        pairValue: DataTypes.INTEGER,
        productValue: DataTypes.INTEGER,
        dateOfSubmission: DataTypes.DATE,
    }, { sequelize, }
);


export default LegAmount;


