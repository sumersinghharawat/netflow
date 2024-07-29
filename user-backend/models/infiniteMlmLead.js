import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const InfiniteMlmLead = sequelize.define("InfiniteMlmLead",
    {
        name: DataTypes.STRING(255),
        email: DataTypes.STRING(255),
        phone: DataTypes.STRING(255),
        country: DataTypes.STRING(255),
        ipAddress: DataTypes.STRING(255),
        demoType: DataTypes.STRING(255),
        demoRefId: DataTypes.STRING(255),
        status: DataTypes.STRING(255),
        addedDate: DataTypes.DATE,
        emailOtp: DataTypes.STRING(255),
        phoneOtp: DataTypes.STRING(255),
        otpExpiry: DataTypes.DATE,
        accessExpiry: DataTypes.DATE,
        supportUserId: DataTypes.BIGINT.UNSIGNED,
        warningMailSent: DataTypes.TINYINT,

    }, {
        tableName: "infinite_mlm_leads",
        timestamps: false,
        underscored: true,
        prefix: ""
    }
);

export default InfiniteMlmLead;