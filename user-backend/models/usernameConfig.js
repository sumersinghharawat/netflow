import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const UsernameConfig = sequelize.define("UsernameConfig",
    {
        length: DataTypes.STRING(255),
        prefixStatus: DataTypes.STRING(255),
        prefix: DataTypes.STRING(255),
        userNameType: DataTypes.STRING(255),
    },
    { sequelize }
);

export default UsernameConfig;
