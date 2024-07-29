import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"

const PackageValidityExtendHistory = sequelize.define("PackageValidityExtendHistory",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        packageId: DataTypes.BIGINT.UNSIGNED,
        invoiceId: DataTypes.STRING,
        totalAmount: DataTypes.DOUBLE(8, 2),
        productPv: DataTypes.DOUBLE(8, 2),
        paymentType: DataTypes.STRING,
        payType: DataTypes.STRING,
        renewalDetails: DataTypes.TEXT,
        renewalStatus: DataTypes.TEXT,
        receipt: DataTypes.TEXT,

    }, { sequelize }
);
export default PackageValidityExtendHistory;
