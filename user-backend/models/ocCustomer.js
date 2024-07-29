import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"


const OcCustomer = sequelize.define("OcCustomer",
    {
        customerId: DataTypes.INTEGER,
        customerGroupId: DataTypes.INTEGER,
        storeId: DataTypes.INTEGER,
        languageId: DataTypes.INTEGER,
        firstname: DataTypes.STRING(32),
        lastname: DataTypes.STRING(32),
        email: DataTypes.STRING(96),
        telephone: DataTypes.STRING(32),
        password: DataTypes.STRING(255),
        customField: DataTypes.TEXT,
        wishlist: DataTypes.TEXT,
        newsletter: DataTypes.BOOLEAN,
        ip: DataTypes.STRING(40),
        status: DataTypes.BOOLEAN,
        safe: DataTypes.BOOLEAN,
        token: DataTypes.TEXT,
        code: DataTypes.STRING(40),
        dateAdded: DataTypes.DATE,
        stringToken: DataTypes.TEXT,
        customerType: DataTypes.STRING(50),

    }, { sequelize }
)
export default OcCustomer;

