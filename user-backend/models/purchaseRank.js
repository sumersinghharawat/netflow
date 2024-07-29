import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const PurchaseRank = sequelize.define( "PurchaseRank",
    {
        rankId: DataTypes.BIGINT.UNSIGNED,
        packageId: DataTypes.BIGINT.UNSIGNED,
        ocProductProductId: DataTypes.BIGINT.UNSIGNED,
        count: DataTypes.INTEGER,
    }, { sequelize }
);
export default PurchaseRank;
