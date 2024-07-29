import { DataTypes, Model } from 'sequelize';
import { sequelize } from '../config/db.js';

const SignupField = sequelize.define("SignupField",
    {
        name: DataTypes.STRING(255),
        type: DataTypes.STRING(255),
        deafult_value: DataTypes.STRING(255),
        required: DataTypes.TINYINT,
        sortOrder: DataTypes.INTEGER,
        status: DataTypes.TINYINT,
        editable: DataTypes.TINYINT,
        isCustom: DataTypes.TINYINT
    }, { sequelize }
);

export default SignupField;
