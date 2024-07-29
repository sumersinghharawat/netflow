import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const OcAddress = sequelize.define("OcAddress",
    {
        addressId: DataTypes.INTEGER,
        customerId: DataTypes.INTEGER,
        firstname: DataTypes.STRING(32),
        lastname: DataTypes.STRING(32),
        company: DataTypes.STRING(60),
        address_1: DataTypes.STRING(128),
        address_2: DataTypes.STRING(128),
        city: DataTypes.STRING(128),
        postcode: DataTypes.STRING(10),
        countryId: DataTypes.INTEGER,
        zoneId: DataTypes.INTEGER,
        customField: DataTypes.TEXT,
        // default:  DataTypes.BOOLEAN,

    }, { sequelize }
)
export default OcAddress;

