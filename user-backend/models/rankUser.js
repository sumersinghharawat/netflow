import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const RankUser = sequelize.define( "RankUser",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        rankId: DataTypes.BIGINT.UNSIGNED,
        status: DataTypes.TINYINT,
        joinDate: DataTypes.DATE
    }, { sequelize }
);
export default RankUser;
