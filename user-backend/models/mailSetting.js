import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const MailSetting = sequelize.define("MailSetting",
    {
        fromName: DataTypes.STRING(255),
        fromEmail: DataTypes.STRING(255),
        smtpHost: DataTypes.STRING(255),
        smtpUsername: DataTypes.STRING(255),
        smtpPassword: DataTypes.STRING(255),
        smtpPort: DataTypes.STRING(255),
        smtpTimeout: DataTypes.STRING(255),
        regMailstatus: DataTypes.ENUM("0", "1"),
        regMailcontent: DataTypes.TEXT,
        regMailtype: DataTypes.STRING(255),
        smtpAuthentication: DataTypes.ENUM("0", "1"),
        smtpProtocol: DataTypes.STRING(255),

    }, { sequelize }
);
export default MailSetting;