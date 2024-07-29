import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PinRequest = sequelize.define("PinRequest",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        requestedPinCount: DataTypes.INTEGER,
        allottedPinCount: DataTypes.INTEGER,
        requestedDate: DataTypes.DATE,
        expiryDate: DataTypes.DATE,
        status: DataTypes.INTEGER,
        remarks: DataTypes.STRING(255),
        pinAmount: DataTypes.INTEGER,
        readStatus: DataTypes.INTEGER,

    }, { sequelize }
);
export default PinRequest;

