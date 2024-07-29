import { sequelize } from "../config/db.js";
import { Model, DataTypes } from "sequelize";

const TransactionPassword = sequelize.define("TransactionPassword", 
{
    userId: DataTypes.INTEGER,
    password: DataTypes.STRING,
}, { sequelize });

export default TransactionPassword;