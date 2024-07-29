import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"

const RoiOrder = sequelize.define("RoiOrder",
    {
        packageId: DataTypes.BIGINT.UNSIGNED,
        userId: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DOUBLE(8, 2),
        dateSubmission: DataTypes.DATE,
        paymentMethod: DataTypes.BIGINT.UNSIGNED,
        pendingStatus: DataTypes.TINYINT,
        roi: DataTypes.DECIMAL(11, 2),
        days: DataTypes.INTEGER,

    }, { sequelize }
);
export default RoiOrder;

