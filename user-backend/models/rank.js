import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Rank = sequelize.define( "Rank",
    {
        name: DataTypes.STRING(),
        color: DataTypes.STRING(),
        image: DataTypes.STRING(),
        treeIcon: DataTypes.STRING(),
        commission: DataTypes.DECIMAL(12, 2),
        packageId: DataTypes.BIGINT.UNSIGNED,
        status: DataTypes.TINYINT,
    }, { sequelize }
);
export default Rank;
