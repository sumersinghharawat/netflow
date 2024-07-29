import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const Compensation = sequelize.define("Compensation",
    {
        planCommission: DataTypes.TINYINT,
        sponsorCommission: DataTypes.TINYINT,
        rankCommission: DataTypes.TINYINT,
        referralCommission: DataTypes.TINYINT,
        roiCommission: DataTypes.TINYINT,
        matchingBonus: DataTypes.TINYINT,
        poolBonus: DataTypes.TINYINT,
        fastStartBonus: DataTypes.TINYINT,
        performanceBonus: DataTypes.TINYINT,
        salesCommission: DataTypes.TINYINT,

    }, { sequelize }
);

export default Compensation;

