import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const TicketPriority = sequelize.define("TicketPriority",
    {
        priority: DataTypes.STRING(255),
        status: DataTypes.TINYINT,
        flagImage: DataTypes.STRING(255),
    },
    {sequelize}
);

export default TicketPriority
