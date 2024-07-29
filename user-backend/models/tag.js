
import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Tag = sequelize.define("Tag",
    {
      tag: DataTypes.STRING(255),
      status: DataTypes.TINYINT,
    },
    {sequelize}
);
export default Tag;
