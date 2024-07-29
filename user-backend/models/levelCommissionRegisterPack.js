import { sequelize } from "../config/db.js";
import { DataTypes } from "sequelize";

const LevelCommissionRegisterPacks = sequelize.define('LevelCommissionRegisterPacks', {
    packageId: DataTypes.BIGINT.UNSIGNED,
    ocProductId: DataTypes.BIGINT.UNSIGNED,
    level: DataTypes.INTEGER,
    commission: DataTypes.FLOAT,
    percentage: DataTypes.FLOAT
}, { sequelize} );

export default LevelCommissionRegisterPacks;