import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const Country = sequelize.define(
    "Country",
    {
        name: DataTypes.STRING(255),
        code: DataTypes.STRING(255),
        phoneCode: DataTypes.STRING(255),
        isoCode: DataTypes.STRING(255),
        status: DataTypes.TINYINT
    }, { sequelize }
);

export default Country;
