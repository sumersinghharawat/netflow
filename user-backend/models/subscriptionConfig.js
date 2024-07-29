import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"

const SubscriptionConfig = sequelize.define("SubscriptionConfig",
    {
        basedOn: DataTypes.STRING(255),
        regStatus: DataTypes.INTEGER,
        commissionStatus: DataTypes.INTEGER,
        payoutStatus: DataTypes.INTEGER,
        fixedAmount: DataTypes.INTEGER,
        subscriptionPeriod: DataTypes.INTEGER,

    }, { sequelize }
);
export default SubscriptionConfig;

