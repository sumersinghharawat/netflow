import Configuration from "../models/configuration.js";

export default async () => await Configuration.findOne({ raw: true });