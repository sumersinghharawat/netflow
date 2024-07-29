import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PinUsed = sequelize.define("PinUsed",
    {
        epinId: DataTypes.BIGINT.UNSIGNED,
        usedBy: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DOUBLE,
        usedFor: DataTypes.STRING(),

    }, { sequelize }
);
export default PinUsed;
