import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const KycDoc = sequelize.define("KycDoc",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        fileName: DataTypes.STRING(255),
        type: DataTypes.STRING(255),
        status: DataTypes.STRING(255),
        reason: DataTypes.STRING(255),
        date: DataTypes.DATEONLY,
    }, { sequelize }
);
export default KycDoc;

