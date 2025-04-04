import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const ModuleStatus = sequelize.define('ModuleStatus',
	{
		mlmPlan: DataTypes.STRING(255),
		firstPair: DataTypes.STRING(255),
		pinStatus: DataTypes.TINYINT,
		productStatus: DataTypes.TINYINT,
		smsStatus: DataTypes.TINYINT,
		mailboxStatus: DataTypes.TINYINT,
		referralStatus: DataTypes.TINYINT,
		ewalletStatus: DataTypes.TINYINT,
		employeeStatus: DataTypes.TINYINT,
		payoutReleaseStatus: DataTypes.STRING(255),
		uploadStatus: DataTypes.TINYINT,
		sponsorTreeStatus: DataTypes.TINYINT,
		rankStatus: DataTypes.TINYINT,
		rankStatusDemo: DataTypes.TINYINT,
		langStatus: DataTypes.TINYINT,
		helpStatus: DataTypes.TINYINT,
		shuffleStatus: DataTypes.TINYINT,
		footerDemoStatus: DataTypes.TINYINT,
		captchaStatus: DataTypes.TINYINT,
		sponsorCommissionStatus: DataTypes.TINYINT,
		multiCurrencyStatus: DataTypes.TINYINT,
		leadCaptureStatus: DataTypes.TINYINT,
		ticketSystemStatus: DataTypes.TINYINT,
		currencyConversionStatus: DataTypes.TINYINT,
		ecomStatus: DataTypes.TINYINT,
		// liveChatStatus: DataTypes.TINYINT,
		ecomStatusDemo: DataTypes.TINYINT,
		leadCaptureStatusDemo: DataTypes.TINYINT,
		ticketSystemStatusDemo: DataTypes.TINYINT,
		autoresponderStatus: DataTypes.TINYINT,
		autoresponderStatusDemo: DataTypes.TINYINT,
		// tableStatus: DataTypes.TINYINT,
		lcpType: DataTypes.STRING(255),
		paymentGatewayStatus: DataTypes.TINYINT,
		bitcoinStatus: DataTypes.TINYINT,
		repurchaseStatus: DataTypes.TINYINT,
		repurchaseStatusDemo: DataTypes.TINYINT,
		googleAuthStatus: DataTypes.TINYINT,
		packageUpgrade: DataTypes.TINYINT,
		packageUpgradeDemo: DataTypes.TINYINT,
		// maintenanceStatusDemo: DataTypes.TINYINT,
		// maintenanceStatus: DataTypes.TINYINT,
		langStatusDemo: DataTypes.TINYINT,
		employeeStatusDemo: DataTypes.TINYINT,
		// smsStatusDemo: DataTypes.TINYINT,
		pinStatusDemo: DataTypes.TINYINT,
		roiStatus: DataTypes.TINYINT,
		basicDemoStatus: DataTypes.TINYINT,
		xupStatus: DataTypes.TINYINT,
		hyipStatus: DataTypes.TINYINT,
		groupPv: DataTypes.TINYINT,
		personalPv: DataTypes.TINYINT,
		kycStatus: DataTypes.TINYINT,
		signupConfig: DataTypes.TINYINT,
		downlineCountRank: DataTypes.TINYINT,
		downlinePurchaseRank: DataTypes.TINYINT,
		purchaseWallet: DataTypes.TINYINT,
		crowdFund: DataTypes.TINYINT,
		// compressionStatus: DataTypes.TINYINT,
		promotionStatus: DataTypes.TINYINT,
		promotionStatusDemo: DataTypes.TINYINT,
		subscriptionStatus: DataTypes.TINYINT,
		subscriptionStatusDemo: DataTypes.TINYINT,
		treeUpdation: DataTypes.TINYINT,
		// cacheStatus: DataTypes.TINYINT,
		multilangStatus: DataTypes.TINYINT,
		defaultLangCode: DataTypes.STRING(255),
		multiCurrencyStatus: DataTypes.TINYINT,
		defaultCurrencyCode: DataTypes.STRING(255),
		replicatedSiteStatus: DataTypes.TINYINT,
		replicatedSiteStatusDemo: DataTypes.TINYINT,
	}, { sequelize }
);

export default ModuleStatus;


