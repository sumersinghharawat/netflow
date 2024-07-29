import { Op, Model, DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const PinNumber = sequelize.define("PinNumber",
    {
        numbers: DataTypes.STRING(255),
        allocDate: DataTypes.DATE,
        purchaseStatus: DataTypes.STRING(11),
        status: DataTypes.STRING(255),
        // usedUser: DataTypes.BIGINT.UNSIGNED,
        generatedUser: DataTypes.BIGINT.UNSIGNED,
        allocatedUser: DataTypes.BIGINT.UNSIGNED,
        uploadedDate: DataTypes.DATE,
        expiryDate: DataTypes.DATE,
        amount: DataTypes.DOUBLE,
        balanceAmount: DataTypes.DOUBLE,
        transactionId: DataTypes.TEXT,
    }, { sequelize }
);


PinNumber.addScope("isNotExpired", {
    where: {
        expiry_date: {
            [Op.gt]: Date.now(),
        },
    },
});
PinNumber.addScope("isActivePin", {
    where: {
        [Op.and]: {
            status: "active",
        },
    },
});
PinNumber.addScope("isPurchasePin", {
    where: {
        purchase_status: 1,
    },
});

export default PinNumber;


