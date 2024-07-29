import dotenv from "dotenv";
dotenv.config();
import express from "express";
import morgan from "morgan";
import multer from 'multer';
import fs from "fs";
import path from "path";
import cors from "cors";
import{logger} from './helper/index.js'
import { addRoutesDynamically } from './utils/importRoutes.js';
import errorHandler from './middleware/errorHandler.js';
import {apiKeyCheck} from "./middleware/checkApiKey.js";

const __dirname = path.dirname(new URL(import.meta.url).pathname);
const accessLogStream = fs.createWriteStream(
    path.join(__dirname, "./logs/access.log"),
    { flags: "a" }
);

const app = express();
app.use(cors());
app.use(express.json({ limit: "50mb" }));
app.use(express.urlencoded({ extended: true, limit: "50mb" }));
app.use(morgan("combined", { stream: accessLogStream }));
app.use(apiKeyCheck);

app.use("/node_api/uploads", express.static("uploads"));
await addRoutesDynamically(app, path.join(__dirname, './routes/', 'web'),'/node_api');


app.use(errorHandler);

app.use((req, res) => {
    res.status(200).json("Welcome to the application. No such Api.");
});

export default app;
