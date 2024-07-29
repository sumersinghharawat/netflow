import moment from "moment-timezone";
import dotenv from "dotenv";

dotenv.config();
// all dates are inserted into db and retrieved from db in UTC TZ (createdAt,dateAdded etc)
// convert dates to LOCAL TZ using convertTolocal function only when passing to front end for display
// dates passed from frontend will be in LOCAL TZ. convert to UTC TZ before performing queries with db

// NOTE: Dates printed as a string will be displayed in system/server tz (not process.env.TIME_ZONE). 
// This is a visual quirk and does not indicate a change in the date object.
// The Date object itself doesn't store a time zone since it is the count of the number of seconds since unix epoch time. it can be displayed in different tz.
// eg. const currentDate = new Date();
// console.log(currentDate) -> 2023-07-28T12:50:47.085Z
// console.log(`${currentDate}`) -> Fri Jul 28 2023 18:20:47 GMT+0530 (India Standard Time)

// Taking a date from the front end:
// const startDate = req.query.startDate -> "2023-06-07" (taken to be process.env.TIME_ZONE time)
// convertToUTC(startDate) -> "2023-06-06T18:30:00Z" (string)
// const formattedStartDate = new Date(convertToUTC(startDate)) -> 2023-06-06T18:30:00.000Z
// console.log(`${formattedStartDate}`) -> Wed Jun 07 2023 04:00:00 GMT+0930 (Australian Central Standard Time) -> system/server tz
// where:{createdAt:{[Op.gte]:formattedStartDate}} -> `TableName`.`created_at` >= '2023-06-06 18:30:00'

export default (utcTimestamp) =>  {
    const timeZone = process.env.TIME_ZONE;
    return moment.utc(utcTimestamp).tz(timeZone).format("YYYY-MM-DD HH:mm:ss");
}

export const convertToUTC = (localTimestamp) => {
    const timeZone = process.env.TIME_ZONE;
    return moment.tz(localTimestamp,timeZone).utc().format();
}