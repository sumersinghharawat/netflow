import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"

const ReplicaBanner = sequelize.define("ReplicaBanner",
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        image: DataTypes.STRING(255),

    }, { sequelize }
);
export default ReplicaBanner;

