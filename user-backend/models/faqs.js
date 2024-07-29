import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"

const FAQ = sequelize.define("FAQ",
    {
        question: DataTypes.TEXT,
        answer: DataTypes.TEXT,
        status: DataTypes.TINYINT,
        sortOrder: DataTypes.INTEGER,

    }, { sequelize }
);
export default FAQ;

