import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"

const UpgradeSalesOrder = sequelize.define("UpgradeSalesOrder",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        packageId: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DOUBLE(8, 2),
        totalPv: DataTypes.DOUBLE(8, 2),
        paymentMethod: DataTypes.STRING,

    }, { sequelize }

);
export default UpgradeSalesOrder;
