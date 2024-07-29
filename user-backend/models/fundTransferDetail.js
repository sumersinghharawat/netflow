import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const FundTransferDetail = sequelize.define("FundTransferDetail",
    {
        fromId: DataTypes.BIGINT.UNSIGNED,
        toId: DataTypes.BIGINT.UNSIGNED,
        amount: DataTypes.DOUBLE(8, 2),
        notes: DataTypes.STRING(255),
        amountType: DataTypes.STRING(255),
        transFee: DataTypes.DOUBLE(8, 2),
        transactionId: DataTypes.BIGINT.UNSIGNED,
        
    },{sequelize}
  );

export default FundTransferDetail;


