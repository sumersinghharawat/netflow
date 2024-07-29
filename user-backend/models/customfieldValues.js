import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const CustomfieldValue = sequelize.define( "CustomfieldValue", {
    customfieldId: DataTypes.INTEGER,
    userId: DataTypes.INTEGER,
    value: DataTypes.STRING
}, { sequelize });

export default CustomfieldValue;