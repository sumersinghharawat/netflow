import Sequelize from 'sequelize';
const { DataTypes } = Sequelize;
import { sequelize } from '../config/db.js';

const DemoUser = sequelize.define('DemoUser', {
	id: { type: DataTypes.BIGINT.UNSIGNED, primaryKey: true, autoIncrement: true },
	prefix: { type: DataTypes.BIGINT, allowNull: false },
	mlmPlan: { type: DataTypes.STRING(255), allowNull: false },
	apiKey: { type: DataTypes.STRING(255), allowNull: false, unique: true },
	username: { type: DataTypes.STRING(255), allowNull: false, unique: true },
	password: { type: DataTypes.STRING(255), allowNull: false },
	isPreset: { type: DataTypes.TINYINT, allowNull: false, defaultValue: 0, comment: '0: no, 1: yes' },
	accountStatus: { type: DataTypes.STRING(255), allowNull: false },
	companyName: { type: DataTypes.STRING(255), allowNull: false },
	fullName: { type: DataTypes.STRING(255), allowNull: false },
	email: { type: DataTypes.STRING(255), allowNull: false },
	phone: { type: DataTypes.STRING(255), allowNull: false },
	country: { type: DataTypes.STRING(255), allowNull: true },
	state: { type: DataTypes.STRING(255), allowNull: true },
	subscriptionStatus: { type: DataTypes.STRING(255), allowNull: false, defaultValue: 'yes', comment: 'no and yes' },
	registrationDate: { type: DataTypes.DATE, allowNull: false },
	deletedDate: { type: DataTypes.DATE, allowNull: true },
	defaultLang: { type: DataTypes.STRING(255), allowNull: false, defaultValue: 'en' }
}, {
	tableName: 'demo_users',
	timestamps: true,
	underscored: true,
	prefix: ''
});

export default DemoUser;