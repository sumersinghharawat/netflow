import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Transaction = sequelize.define("Transaction",
    {
        transactionId: DataTypes.STRING,
    }, { sequelize });

export default Transaction;