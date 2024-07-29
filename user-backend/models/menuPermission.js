import { Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const MenuPermission = sequelize.define( 'MenuPermission',
    {
        menuId : DataTypes.INTEGER.UNSIGNED,
        userPermission : DataTypes.INTEGER
    }, { sequelize }
);

export default MenuPermission;