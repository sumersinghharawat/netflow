import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PendingRegistration = sequelize.define('PendingRegistration',
	{
		username: DataTypes.STRING(255),
		updatedId: DataTypes.BIGINT.UNSIGNED,
		email: DataTypes.STRING,
		packageId: DataTypes.BIGINT.UNSIGNED,
		sponsorId: DataTypes.BIGINT.UNSIGNED,
		paymentMethod: DataTypes.STRING(255),
		data: DataTypes.TEXT,
		status: DataTypes.STRING(255),
		dateAdded: DataTypes.DATE,
		dateModified: DataTypes.DATE,
		emailVerificationStatus: DataTypes.STRING(255),
		defaultCurrency: DataTypes.STRING(255),
		userTokens: DataTypes.STRING(255),
		failedReason: DataTypes.STRING(255),
		orderId: DataTypes.BIGINT.UNSIGNED
	},
	{ sequelize }
);

export default PendingRegistration;

