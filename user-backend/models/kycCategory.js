import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const KycCategory = sequelize.define("KycCategory",
    {
        category: DataTypes.STRING(255),
        status: DataTypes.TINYINT,
    }, { sequelize }
);
export default KycCategory;

