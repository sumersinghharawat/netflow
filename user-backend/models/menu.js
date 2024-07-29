import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const Menu = sequelize.define('Menu', 
    {
        title : DataTypes.STRING,
        slug : DataTypes.STRING,
        react : DataTypes.INTEGER,
        adminOnly : DataTypes.INTEGER,
        parentId : DataTypes.INTEGER.UNSIGNED,
        userIcon : DataTypes.STRING,
        isChild : DataTypes.INTEGER
    }, { sequelize }
);

export default Menu;