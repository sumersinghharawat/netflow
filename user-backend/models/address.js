import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Address = sequelize.define("Address",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        name: DataTypes.STRING(255),
        address: DataTypes.TEXT,
        zip: DataTypes.STRING(255),
        city: DataTypes.STRING(255),
        mobile: DataTypes.STRING(255),
        isDefault: DataTypes.ENUM("0", "1"),
        deletedAt: DataTypes.DATE
    }, { sequelize }
);
export default Address;

