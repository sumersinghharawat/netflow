import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"

const PackageUpgradeHistory = sequelize.define("PackageUpgradeHistory",
  {
    userId: DataTypes.BIGINT.UNSIGNED,
    currentPackageId: DataTypes.BIGINT.UNSIGNED,
    newPackageId: DataTypes.BIGINT.UNSIGNED,
    pvDifference: DataTypes.DOUBLE(8, 2),
    paymentAmount: DataTypes.DOUBLE(8, 2),
    paymentType: DataTypes.BIGINT.UNSIGNED,
    doneBy: DataTypes.BIGINT.UNSIGNED,
    status: DataTypes.TINYINT,
    paymentReceipt: DataTypes.STRING,
    description: DataTypes.TEXT,
    ocCurrentPackageId: DataTypes.BIGINT.UNSIGNED,
    ocNewPackageId: DataTypes.BIGINT.UNSIGNED,

  }, { sequelize }
);
export default PackageUpgradeHistory;
