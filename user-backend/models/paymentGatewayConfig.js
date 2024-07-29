import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const PaymentGatewayConfig = sequelize.define("PaymentGatewayConfig",
    {
        name: DataTypes.STRING(255),
        slug: DataTypes.STRING(255),
        status: DataTypes.TINYINT,
        logo: DataTypes.STRING(255),
        sortOrder: DataTypes.INTEGER,
        mode: DataTypes.STRING(255),
        payoutStatus: DataTypes.TINYINT,
        payoutSortOrder: DataTypes.TINYINT,
        registration: DataTypes.TINYINT,
        repurchase: DataTypes.TINYINT,
        membershipRenewal: DataTypes.TINYINT,
        adminOnly: DataTypes.TINYINT,
        gateWay: DataTypes.TINYINT,
        paymentOnly: DataTypes.TINYINT,
        upgradation: DataTypes.TINYINT,
        regPendingStatus: DataTypes.TINYINT,

    }, { sequelize, }
);

export default PaymentGatewayConfig;

