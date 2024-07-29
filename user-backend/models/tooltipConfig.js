import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const ToolipConfig = sequelize.define('TooltipConfig', {
    name : DataTypes.STRING,
    status : DataTypes.INTEGER,
    slug : DataTypes.STRING
}, { sequelize });

export default ToolipConfig;