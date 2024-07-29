import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const RankConfiguration = sequelize.define( "RankConfiguration",
    {
        name: DataTypes.STRING,
        slug: DataTypes.STRING,
        calculation: DataTypes.STRING,
        status: DataTypes.TINYINT,
        isproductDependent: DataTypes.TINYINT
    }, { sequelize }
);
export default RankConfiguration;
