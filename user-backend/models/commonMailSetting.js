import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const CommonMailSetting = sequelize.define("CommonMailSetting",
    {
        mailType: DataTypes.STRING(255),
        subject: DataTypes.STRING(255),
        mailContent: DataTypes.TEXT,
        status: DataTypes.TINYINT,

    }, { sequelize }
);
export default CommonMailSetting;