import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const RankDetail = sequelize.define("RankDetail",
    {
        rankId: DataTypes.BIGINT.UNSIGNED,
        referralCount: DataTypes.INTEGER,
        partyComm: DataTypes.INTEGER,
        personalPv: DataTypes.INTEGER,
        groupPv: DataTypes.INTEGER,
        downlineCount: DataTypes.INTEGER,
        referralCommission: DataTypes.DOUBLE,
        teamMemberCount: DataTypes.INTEGER,
        poolStatus: DataTypes.STRING(255),
        status: DataTypes.STRING(255),
        deleteStatus: DataTypes.STRING(255),

    }, { sequelize }
);

export default RankDetail;

