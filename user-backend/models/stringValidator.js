import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"


const StringValidator = sequelize.define("StringValidator",
    {
        userId: DataTypes.INTEGER,
        string: DataTypes.TEXT,
        status: DataTypes.TINYINT,

    }, { sequelize }
);

export default StringValidator;

