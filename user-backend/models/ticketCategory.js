import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const TicketCategory = sequelize.define("TicketCategory",
    {
      categoryName: DataTypes.STRING(255),
      ticketCount: DataTypes.INTEGER,
      status: DataTypes.TINYINT,
      assigneeId: DataTypes.BIGINT.UNSIGNED,
    },
    { sequelize}
);

export default TicketCategory;
