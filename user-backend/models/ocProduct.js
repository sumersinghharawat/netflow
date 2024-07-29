import { DataTypes } from "sequelize";
import { sequelize } from "../config/db.js";

const OcProduct = sequelize.define("OcProduct",
    {
        productId:{ type:DataTypes.INTEGER, primaryKey:true},
        model: DataTypes.STRING(64),
        sku: DataTypes.STRING(64),
        upc: DataTypes.STRING(12),
        ean: DataTypes.STRING(14),
        jan: DataTypes.STRING(13),
        isbn: DataTypes.STRING(17),
        mpn: DataTypes.STRING(64),
        location: DataTypes.STRING(128),
        quantity: DataTypes.INTEGER,
        stockStatusId: DataTypes.INTEGER,
        image: DataTypes.STRING(255),
        manufacturerId: DataTypes.INTEGER,
        shipping: DataTypes.BOOLEAN,
        price: DataTypes.DECIMAL(15, 4),
        points: DataTypes.INTEGER,
        taxClassId: DataTypes.INTEGER,
        dateAvailable: DataTypes.DATEONLY,
        weight: DataTypes.DECIMAL(15, 8),
        weightClassId: DataTypes.INTEGER,
        length: DataTypes.DECIMAL(15, 8),
        width: DataTypes.DECIMAL(15, 8),
        height: DataTypes.DECIMAL(15, 8),
        lengthClassId: DataTypes.INTEGER,
        subtract: DataTypes.BOOLEAN,
        minimum: DataTypes.INTEGER,
        sortOrder: DataTypes.INTEGER,
        status: DataTypes.BOOLEAN,
        viewed: DataTypes.INTEGER,
        dateAdded: DataTypes.DATE,
        dateModified: DataTypes.DATE,
        packageId: DataTypes.INTEGER,
        referralCommission: DataTypes.INTEGER,
        validity: DataTypes.STRING(50),
        pairPrice: DataTypes.DECIMAL(15, 4),
        pairValue: DataTypes.DOUBLE,
        roi: DataTypes.INTEGER,
        days: DataTypes.DOUBLE,
        packageType: DataTypes.STRING(50),
        masterId: DataTypes.INTEGER,
        subscriptionPeriod: DataTypes.INTEGER,
        
    }, { sequelize }
);

export default OcProduct;

