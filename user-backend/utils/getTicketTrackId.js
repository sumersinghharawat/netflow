import Ticket from "../models/ticket.js";
import { consoleLog, logger } from "../helper/index.js"


const getTrackId = async(next) => {
    try {
        let firstPart   = await getRandomChr(3);
        let secondPart  = await getRandomChr(5);
        let lastPart    = await getRandomChr(3, 'number');
        const trackId   = firstPart+'-'+secondPart+'-'+lastPart;
        const ticket    = await Ticket.findOne({ where:{trackId} });
        if(ticket) await this.getTrackId(next);
        return trackId;
    } catch (error) {
        logger.error("ERROR FROM getTrackId utility:- ",error)
        return next(error);
    }
}

const getRandomChr = async(length, type = null ) => {
    let characters;
    let randomString = '';
    if(type === 'number') {
        characters = '0123456789';
    } else {
        characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    }
    for (let index = 0; index < parseInt(length); index++) {
        randomString += characters[Math.floor(Math.random() * characters.length)];
    }
    return randomString;
}

export default getTrackId;