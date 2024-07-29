import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const UsersRegistration = sequelize.define('UsersRegistration', {
    userId  : DataTypes.BIGINT.UNSIGNED,
    username : DataTypes.STRING,
    name : DataTypes.STRING,
    secondName : DataTypes.STRING,
    address : DataTypes.TEXT,
    address2: DataTypes.TEXT,
    countryId : DataTypes.BIGINT.UNSIGNED,
    countryName : DataTypes.STRING,
    stateId : DataTypes.BIGINT.UNSIGNED,
    stateName : DataTypes.STRING,
    city : DataTypes.STRING,
    email : DataTypes.STRING,
    productId : DataTypes.BIGINT.UNSIGNED,
    productPv : DataTypes.INTEGER,
    productAmount : DataTypes.FLOAT,
    regAmount : DataTypes.FLOAT,
    totalAmount : DataTypes.FLOAT,
    paymentMethod : DataTypes.BIGINT.UNSIGNED,
    ocProductId : DataTypes.INTEGER
}, { sequelize });

export default UsersRegistration;