import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const TicketComment = sequelize.define("TicketComment",
    {
        ticketId:  DataTypes.BIGINT.UNSIGNED,
        commentedBy: DataTypes.BIGINT.UNSIGNED,
        comment: DataTypes.TEXT,
    },{sequelize});

export default TicketComment;
