import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const TicketFaq = sequelize.define("TicketFaq", {
    question: DataTypes.TEXT,
    answer: DataTypes.TEXT,
    status:  DataTypes.TINYINT,
    categoryId: DataTypes.BIGINT.UNSIGNED
    },{sequelize}
);
  
export default TicketFaq
  
