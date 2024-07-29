import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const BinaryBonus = sequelize.define("BinaryBonus",
    {
        commissionType: DataTypes.STRING,
    },
    { sequelize }
);

export default BinaryBonus;

