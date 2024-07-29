import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";
const Ticket = sequelize.define("Ticket",{
    trackId: DataTypes.STRING(255),
    userId: DataTypes.BIGINT.UNSIGNED,
    assigneeId: DataTypes.BIGINT.UNSIGNED,
    assigneeReadTicket: DataTypes.TINYINT,
    name: DataTypes.STRING(255),
    categoryId: DataTypes.BIGINT.UNSIGNED,
    priorityId: DataTypes.BIGINT.UNSIGNED,
    subject: DataTypes.TEXT,
    message: DataTypes.TEXT,
    ip: DataTypes.STRING(255),
    statusId: DataTypes.BIGINT.UNSIGNED,
    archive: DataTypes.TINYINT,
    attachments: DataTypes.STRING(255),
  },{ sequelize });

export default Ticket;
