import { sequelize } from "../config/db.js";
import { DataTypes } from "sequelize";

const Letterconfig = sequelize.define('Letterconfig', {
    content: DataTypes.TEXT(),
    languageId: DataTypes.BIGINT.UNSIGNED
}, { sequelize} );

export default Letterconfig;