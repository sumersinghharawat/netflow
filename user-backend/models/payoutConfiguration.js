import { Model, DataTypes } from 'sequelize';
import { sequelize } from "../config/db.js";

const PayoutConfiguration = sequelize.define("PayoutConfiguration",
    {
        releaseType: DataTypes.STRING(255),
        minPayout:DataTypes.INTEGER,
        requestValidity: DataTypes.INTEGER,
        maxPayout:DataTypes.INTEGER,
        mailStatus: DataTypes.TINYINT,
        feeAmount: DataTypes.INTEGER,
        feeMode: DataTypes.ENUM("flat", "percentage"),

    }, { sequelize }
);

export default PayoutConfiguration;


