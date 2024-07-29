import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const CompanyProfile = sequelize.define( 'CompanyProfile',
    {
        name: DataTypes.STRING,
        logo: DataTypes.STRING,
        favicon: DataTypes.STRING,
        phone: DataTypes.STRING,
        email: DataTypes.STRING,
        address: DataTypes.TEXT('long')
    }, { sequelize }
);

export default CompanyProfile;