import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const InfPresetDemoVisiter = sequelize.define("InfPresetDemoVisiter",
    {
        userFullName: DataTypes.STRING(200),
        userEmail: DataTypes.STRING(200),
        mobile: DataTypes.INTEGER,
        country: DataTypes.STRING(300),
        ip: DataTypes.STRING(100),
        date: DataTypes.DATEONLY,

    }, {
        tableName: "inf_preset_demo_visiter",
        timestamps: false,
        underscored: true,
        prefix: ""
    }
);
export default InfPresetDemoVisiter;