import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const TermsAndCondition = sequelize.define("TermsAndCondition",
    {
        termsAndConditions: DataTypes.TEXT,
        languageId: DataTypes.BIGINT.UNSIGNED,
    },
    { sequelize }
);

export default TermsAndCondition;
