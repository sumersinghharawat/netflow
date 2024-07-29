import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const AccessKey = sequelize.define( 'AccessKey',
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        token: DataTypes.STRING(255),
        mobileToken: DataTypes.STRING(255),
        ip: DataTypes.STRING(255),
        expiry: DataTypes.BIGINT,
    },
    { sequelize }
);

export default AccessKey;


