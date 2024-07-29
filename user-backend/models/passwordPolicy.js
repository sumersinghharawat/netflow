import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PasswordPolicy = sequelize.define("PasswordPolicy",
    {
        enablePolicy: DataTypes.TINYINT,
        mixedCase: DataTypes.TINYINT,
        number: DataTypes.TINYINT,
        spChar: DataTypes.TINYINT,
        minLength: DataTypes.TINYINT,
    },
    { sequelize }
);


export default PasswordPolicy;
