import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const InfiniteMlmLeadsConfig = sequelize.define("InfiniteMlmLeadsConfig",
    {
        emailReuseCount: DataTypes.INTEGER,
        phoneReuseCount: DataTypes.INTEGER,
        otpTimeout: DataTypes.INTEGER,
        indianPresetDemoTimeout: DataTypes.INTEGER,
        otherPresetDemoTimeout: DataTypes.INTEGER,
        indianCustomDemoTimeout: DataTypes.INTEGER,
        otherCustomDemoTimeout: DataTypes.INTEGER,
        customDemoDeleteTimeout: DataTypes.INTEGER,
        unlimitedEmails: DataTypes.TEXT,
        unlimitedPhones: DataTypes.TEXT,

    }, {
        tableName: "infinite_mlm_lead_configs",
        timestamps: true,
        underscored: true,
        prefix: ""
    }
);

export default InfiniteMlmLeadsConfig;