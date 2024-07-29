import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Activity = sequelize.define('Activity', {
    userId: DataTypes.BIGINT.UNSIGNED,
    ip: DataTypes.STRING,
    activity: DataTypes.STRING,
    userType: DataTypes.STRING,
    description: DataTypes.STRING,
    data: DataTypes.TEXT,
});

export default Activity;
