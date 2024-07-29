import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const CustomfieldLang = sequelize.define( 'CustomfieldLang', 
    {
        customfieldId: DataTypes.BIGINT.UNSIGNED,
        languageId: DataTypes.BIGINT.UNSIGNED,
        value: DataTypes.TEXT,
    }, { sequelize }
)

export default CustomfieldLang;