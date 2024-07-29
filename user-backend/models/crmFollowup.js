import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"


const CrmFollowup = sequelize.define("CrmFollowup",
    {
        leadId: DataTypes.BIGINT.UNSIGNED,
        followupEnteredBy: DataTypes.BIGINT.UNSIGNED,
        description: DataTypes.TEXT,
        image: DataTypes.STRING(255),
        followupDate: DataTypes.DATEONLY,

    }, { sequelize }
);

export default CrmFollowup;

