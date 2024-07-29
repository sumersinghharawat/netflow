import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const PasswordReset = sequelize.define("passwordReset",
    {
        userId: DataTypes.BIGINT,
        token: DataTypes.TEXT,
        status: DataTypes.TINYINT,
    }, { sequelize }
);

export default PasswordReset;