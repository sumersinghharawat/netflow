import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const TicketTag = sequelize.define(
    "TicketTag",
    {
        ticketId: DataTypes.BIGINT.UNSIGNED,
        tagId: DataTypes.BIGINT.UNSIGNED,
    },
    {sequelize}
);


export default TicketTag
