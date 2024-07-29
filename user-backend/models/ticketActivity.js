import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const TicketActivity = sequelize.define("TicketActivity",
    {
        ticketId:  DataTypes.BIGINT.UNSIGNED,
        doneby: DataTypes.BIGINT.UNSIGNED,
        donebyUsertype: DataTypes.STRING(255),
        activity: DataTypes.TEXT,
        ifComment: DataTypes.TEXT,
        ifReply: DataTypes.TEXT,
    },{sequelize});

export default TicketActivity;
