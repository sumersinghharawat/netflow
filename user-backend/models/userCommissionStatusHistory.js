import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';


const UserCommissionStatusHistory = sequelize.define("UserCommissionStatusHistory",
    {
        parentId: DataTypes.INTEGER,
        commission: DataTypes.STRING(255),
        userId: DataTypes.BIGINT.UNSIGNED,
        data: DataTypes.STRING(255),
        status: DataTypes.TINYINT,
        date: DataTypes.DATE,
        
    }, { sequelize }
);

export default UserCommissionStatusHistory;

