import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const CurrencyDetail = sequelize.define( 'CurrencyDetail',
	{
		title: DataTypes.STRING(255),
		code: DataTypes.STRING(255),
		value: DataTypes.DOUBLE(8, 2),
		symbolLeft: DataTypes.STRING(255),
		symbolRight: DataTypes.STRING(255),
		status: DataTypes.TINYINT,
		default: DataTypes.INTEGER,
		deleteStatus: DataTypes.STRING(255),
	}, { sequelize }
);

export default CurrencyDetail;
