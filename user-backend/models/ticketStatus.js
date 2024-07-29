import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

  const TicketStatus = sequelize.define("TicketStatus",
    {
      ticketStatus: DataTypes.STRING(255),
      status: DataTypes.TINYINT,
    },
    {sequelize }
);

export default TicketStatus
