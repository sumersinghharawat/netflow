import { DataTypes, Model } from 'sequelize';
import { sequelize } from '../config/db.js';

const SignupSettings = sequelize.define("SignupSettings",
    {
        registrationAllowed: DataTypes.STRING(255),
        sponsorRequired: DataTypes.STRING(255),
        mailNotification: DataTypes.STRING(255),
        binaryLeg: DataTypes.STRING(255),
        ageLimit: DataTypes.INTEGER,
        bankInfoRequired: DataTypes.STRING(255),
        compressionCommission: DataTypes.STRING(255),
        defaultCountry: DataTypes.INTEGER,
        emailVerification: DataTypes.STRING(255),
        loginUnapproved: DataTypes.STRING(255),
    },
    { sequelize }
);

export default SignupSettings;
