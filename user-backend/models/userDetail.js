import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';


const UserDetail = sequelize.define('UserDetail',
    {
        userId: DataTypes.BIGINT.UNSIGNED,
        sponsorId: DataTypes.BIGINT.UNSIGNED,
        countryId: DataTypes.BIGINT.UNSIGNED,
        stateId: DataTypes.BIGINT.UNSIGNED,
        name: DataTypes.STRING(255),
        secondName: DataTypes.STRING(255),
        address: DataTypes.TEXT,
        address2: DataTypes.TEXT,
        city: DataTypes.STRING(255),
        pin: DataTypes.STRING(255),
        mobile: DataTypes.INTEGER,
        landPhone: DataTypes.TEXT,
        dob: DataTypes.DATEONLY,
        gender: DataTypes.STRING(255),
        bitcoinAddress: DataTypes.TEXT,
        accountNumber: DataTypes.STRING(255),
        ifsc: DataTypes.STRING(255),
        bank: DataTypes.STRING(255),
        nacctHolder: DataTypes.STRING(255),
        branch: DataTypes.STRING(255),
        pan: DataTypes.STRING(255),
        joinDate: DataTypes.DATE,
        image: DataTypes.STRING(255),
        facebbok: DataTypes.TEXT,
        twitter: DataTypes.TEXT,
        bankInfoRequired: DataTypes.STRING(255),
        paypal: DataTypes.TEXT,
        stripe: DataTypes.TEXT,
        blockchain: DataTypes.TEXT,
        bitgoWallet: DataTypes.TEXT,
        uploadCount: DataTypes.INTEGER,
        kycStatus: DataTypes.STRING(255),
        payoutType: DataTypes.INTEGER,
        banner: DataTypes.STRING(255),
        readDocCount: DataTypes.INTEGER,
        readNewsCount: DataTypes.INTEGER,
    }, { sequelize }
);

export default UserDetail;


