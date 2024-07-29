import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const Language = sequelize.define( 'Language',
    {
        code: DataTypes.STRING(255),
        name: DataTypes.STRING(255),
        nameInEnglish: DataTypes.STRING(255),
        flagImage: DataTypes.STRING(255),
        status: DataTypes.INTEGER,
        default: DataTypes.INTEGER,
    },
    { sequelize }
);
export default Language;


