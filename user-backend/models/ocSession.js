import { Model, DataTypes } from 'sequelize';
import { sequelize } from '../config/db.js';

const OcSession = sequelize.define('OcSession',
    {
        sessionId: {
            type: DataTypes.STRING(32),
            primaryKey: true, // Specify sessionId as the primary key
        },
        data: DataTypes.TEXT,
        expire: DataTypes.DATE,
        customerId: DataTypes.INTEGER,
        
    }, { sequelize, timestamps: false }
)
export default OcSession;


