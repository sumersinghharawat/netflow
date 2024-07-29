import { Op, Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const PinAmountDetail = sequelize.define("PinAmountDetail",
    {
        amount: DataTypes.INTEGER,

    }, { sequelize }
);
export default PinAmountDetail;

