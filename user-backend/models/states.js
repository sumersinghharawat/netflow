import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const State = sequelize.define("State",
    {
        countryId: DataTypes.BIGINT.UNSIGNED,
        name: DataTypes.STRING(255),
        code: DataTypes.STRING(255),
        status: DataTypes.INTEGER
    },
    { sequelize }
);

export default State;
