import { sequelize } from "../config/db.js";
import { DataTypes } from "sequelize";

const UserPlacement = sequelize.define('UserPlacement', {
    userId: DataTypes.BIGINT.UNSIGNED,
    branchParent: DataTypes.BIGINT.UNSIGNED,
    leftMost: DataTypes.BIGINT.UNSIGNED,
    rightMost: DataTypes.BIGINT.UNSIGNED
}, { sequelize, timestamps: false });

export default UserPlacement;