import {logger} from "../helper/index.js";

const errorHandler = ( err, req, res, next) => {
    logger.warn(
        `----------ERROR LOG----------\n
from error handler middleware: ${err}\n
----------ERROR LOG----------\n`
        );
    res.status(err.statusCode || 500).json({
        message: err.message,
        error: err.name
    });
};
export default errorHandler;