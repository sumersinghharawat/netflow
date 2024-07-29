import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const RankDownlineRank = sequelize.define( "RankDownlineRank",
    {
        downlineRankId: DataTypes.BIGINT.UNSIGNED,
        rankId: DataTypes.BIGINT.UNSIGNED,
        count: DataTypes.INTEGER,
    }, { sequelize }
);
export default RankDownlineRank;
