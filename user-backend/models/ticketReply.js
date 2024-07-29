import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const TicketReply = sequelize.define("TicketReply",
    {
        ticketId: DataTypes.BIGINT.UNSIGNED,
        userId: DataTypes.BIGINT.UNSIGNED,
        message: DataTypes.TEXT,
        image: DataTypes.STRING(255),
        status: DataTypes.TINYINT,
    },
    { sequelize }
);
export default TicketReply
  
