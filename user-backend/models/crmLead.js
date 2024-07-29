import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"


const CrmLead = sequelize.define("CrmLead",
    {
        firstName: DataTypes.STRING(255),
        lastName: DataTypes.STRING(255),
        addedBy: DataTypes.BIGINT.UNSIGNED,
        emailId: DataTypes.STRING(255),
        skypeId: DataTypes.STRING(255),
        mobileNo: DataTypes.STRING(255),
        countryId: DataTypes.BIGINT.UNSIGNED,
        description: DataTypes.TEXT,
        interestStatus: DataTypes.TINYINT,
        followupDate: DataTypes.DATEONLY,
        nextFollowupDate: DataTypes.DATEONLY,
        leadStatus: DataTypes.TINYINT,
        confirmationDate: DataTypes.DATE,

    }, { sequelize }
);
export default CrmLead;

