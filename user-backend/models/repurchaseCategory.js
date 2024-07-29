import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const RepurchaseCategory = sequelize.define("RepurchaseCategory",
  {
    name: DataTypes.STRING(255),
    image: DataTypes.STRING(255),
    status: DataTypes.TINYINT,
    dateAdded: DataTypes.DATE,
  }, { sequelize }
);

export default RepurchaseCategory;
