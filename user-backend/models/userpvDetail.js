import { DataTypes, Model } from "sequelize";
import { sequelize } from "../config/db.js";

const UserpvDetail = sequelize.define('UserpvDetail', {
    userId: DataTypes.BIGINT.UNSIGNED,
    totalPv: DataTypes.INTEGER,
    totalGpv: DataTypes.INTEGER
});

export default UserpvDetail;