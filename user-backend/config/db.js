import { Sequelize, DataTypes } from "sequelize";
import { sqlQueryLogger, logger } from "../helper/index.js";
import dotenv from "dotenv";
import fs from "fs";
import { fileURLToPath } from "url";
import path, { dirname } from "path";

dotenv.config();
const currentFileUrl = import.meta.url;
const currentPath = fileURLToPath(currentFileUrl);
const currentDir = dirname(currentPath);
const modelPath = path.join(currentDir, "..", "/models");

const sequelize = new Sequelize(
    process.env.MYSQL_DB,
    process.env.MYSQL_USER,
    process.env.MYSQL_PASS,
    {
        host: process.env.MYSQL_HOST || "localhost",
        port: process.env.MYSQL_PORT || 3306,
        dialect: "mysql",
        // logging: false,
        logging: (msg) =>
            process.env.NODE_ENV == "development"
                ? sqlQueryLogger.silly(msg)
                : false,
        define: {
            underscored: true,
            freezeTableName: true,
            timestamps: true,
        },
        pool: {
            max: 50,
            min: 10,
            idle: 10000,
        },
        options: {
            raw: true,
            prefix: "", // set the prefix option to empty string by default
			useUTC: false,
        },
        dialectOptions: {
        },

    }
);

const checkConnection = async () => {
    const retry     = 3;
    let currentTry  = 0;

    while (currentTry < retry) {
        try {
            await sequelize.authenticate();
            await sequelize.query(
                "SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))"
            );
            const [results] = await sequelize.query("SELECT @@sql_mode;");
            console.log("Current SQL mode:", results[0]['@@sql_mode']);
            console.log("database connection established.");
            return;
        } catch (error) {
            currentTry++;
            if (currentTry < retry) {
                console.log(`Connection attempt ${currentTry} failed. Retrying in 5 seconds...`);
                await new Promise(resolve => setTimeout(resolve, 5000)); // Delay for 5 seconds
            } else {
                console.error(`Max retries (${retry}) reached. Unable to establish a connection.`);
                throw error;
            }
        }
    }
};

export { sequelize, checkConnection };
