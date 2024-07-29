import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const EpinTransferHistory = sequelize.define("EpinTransferHistory",
    {

        toUser: DataTypes.BIGINT.UNSIGNED,
        fromUser: DataTypes.BIGINT.UNSIGNED,
        epinId: DataTypes.BIGINT.UNSIGNED,
        ip: DataTypes.STRING(255),
        doneBy: DataTypes.BIGINT.UNSIGNED,
        date: DataTypes.DATE,
        activity: DataTypes.TEXT,

    }, { sequelize }
);

export default EpinTransferHistory;


