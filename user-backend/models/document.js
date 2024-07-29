import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const Document = sequelize.define("Document",
    {
        fileTitle: DataTypes.STRING(255),
        fileName: DataTypes.STRING(255),
        fileDescription: DataTypes.STRING(255),
        catId: DataTypes.BIGINT.UNSIGNED,

    }, { sequelize }
);

export default Document;
