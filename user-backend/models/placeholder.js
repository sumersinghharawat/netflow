import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const Placeholder = sequelize.define("Placeholder", {
    name: DataTypes.STRING,
    placeholder: DataTypes.STRING
}, { sequelize });
export default Placeholder;
