import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";


const MailBox = sequelize.define("MailBox",
    {
        fromUserId: DataTypes.BIGINT.UNSIGNED,
        toUserId: DataTypes.BIGINT.UNSIGNED,
        toAll: DataTypes.STRING(255),
        subject: DataTypes.TEXT,
        message: DataTypes.TEXT,
        date: DataTypes.DATEONLY,
        inboxDeleteStatus: DataTypes.STRING(255),
        sentDeleteStatus: DataTypes.STRING(255),
        deletedBy: DataTypes.STRING(255),
        parentUserMailId: DataTypes.STRING(255),
        thread: DataTypes.BIGINT.UNSIGNED,
        readStatus: DataTypes.TINYINT,
    }, { sequelize });

export default MailBox;
