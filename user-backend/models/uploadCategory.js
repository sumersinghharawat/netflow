import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const UploadCategory = sequelize.define("UploadCategory",
    {
        type: DataTypes.STRING(255),

    }, { sequelize }
);

export default UploadCategory;
