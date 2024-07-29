import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const Package = sequelize.define("Package",
    {
        name: DataTypes.STRING(255),
        type: DataTypes.STRING(255),
        active: DataTypes.TINYINT,
        productId: DataTypes.STRING(255),
        price: DataTypes.DOUBLE(8, 2),
        bvValue: DataTypes.DOUBLE(8, 2),
        pairValue: DataTypes.DOUBLE(8, 2),
        quantity: DataTypes.INTEGER,
        referralCommission: DataTypes.DOUBLE(8, 2),
        pairPrice: DataTypes.DOUBLE(8, 2),
        image: DataTypes.STRING(255),
        roi: DataTypes.DOUBLE(8, 2),
        description: DataTypes.TEXT,
        days: DataTypes.INTEGER,
        validity: DataTypes.INTEGER,
        joineeCommission: DataTypes.DOUBLE(8, 2),
        categoryId: DataTypes.BIGINT.UNSIGNED,
        treeIcon: DataTypes.STRING(255),

    }, { sequelize }
);
export default Package;


