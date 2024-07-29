import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"

const ReplicaContent = sequelize.define("ReplicaContent",
    {
        key: DataTypes.STRING(255),
        value: DataTypes.TEXT,
        userId: DataTypes.INTEGER,
        langId: DataTypes.INTEGER,

    }, { sequelize }
);
export default ReplicaContent;

