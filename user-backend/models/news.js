import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js"

const News = sequelize.define("News",
    {

        title: DataTypes.TEXT,
        description: DataTypes.TEXT,
        image: DataTypes.TEXT,
        
    },{ sequelize }
);
export default News;

