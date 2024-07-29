import { DataTypes, Model } from "sequelize";
import { sequelize } from "../config/db.js";

const Treepath = sequelize.define('Treepath', {
    ancestor: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true },
    descendant: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true },
    depth: DataTypes.INTEGER
}, { sequelize });

export default Treepath;