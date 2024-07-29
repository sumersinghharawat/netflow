import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const SponsorTreepath = sequelize.define("SponsorTreepath",
    {
        ancestor: {type: DataTypes.BIGINT.UNSIGNED, primaryKey: true},
        descendant: {type: DataTypes.BIGINT.UNSIGNED, primaryKey: true},
        depth: DataTypes.INTEGER,
        
    }, { sequelize }
);

export default SponsorTreepath;


