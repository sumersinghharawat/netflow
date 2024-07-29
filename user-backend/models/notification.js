
import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Notification = sequelize.define("Notification",

    {
        type: DataTypes.STRING(255),
        notifiableType: DataTypes.STRING(255),
        notifiableId: DataTypes.BIGINT.UNSIGNED,
        data: DataTypes.TEXT,
        readAt: DataTypes.DATE,

    }, { sequelize }
);

export default Notification;

