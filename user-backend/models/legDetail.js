import { DataTypes, Model } from "sequelize";
import { sequelize } from "../config/db.js";

const LegDetail = sequelize.define('LegDetail', {
    userId: DataTypes.BIGINT.UNSIGNED,
    totalLeftCount: DataTypes.INTEGER,
    totalRightCount: DataTypes.INTEGER,
    totalLeftCarry: DataTypes.INTEGER,
    totalRightCarry: DataTypes.INTEGER,
    totalActive: DataTypes.INTEGER,
    leftCarryForward: DataTypes.INTEGER,
    rightCarryForward: DataTypes.INTEGER,
}, { sequelize });

export default LegDetail;