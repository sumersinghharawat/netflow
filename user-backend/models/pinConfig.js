import { Op, Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const PinConfig = sequelize.define("PinConfig",
    {
        amount: DataTypes.INTEGER,
        length: DataTypes.INTEGER,
        type: DataTypes.STRING(255),
        characterSet: DataTypes.STRING(255),
        maxCount: DataTypes.INTEGER, 

    }, { sequelize }
);
export default PinConfig;