import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"


const Contact = sequelize.define("Contact",
    {
        name: DataTypes.STRING(255),
        email: DataTypes.STRING(255),
        address: DataTypes.TEXT,
        phone: DataTypes.STRING(255),
        contactInfo: DataTypes.TEXT,
        ownerId: DataTypes.BIGINT.UNSIGNED,
        status: DataTypes.STRING(255),
        mailAddedDate: DataTypes.STRING(255),
        readMsg: DataTypes.STRING(255),
    }, { sequelize }
);
export default Contact;

