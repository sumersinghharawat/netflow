import { uploadFile } from "../../utils/fileUpload.js";
import TicketCategory from "../../models/ticketCategory.js";
import TicketStatus from "../../models/ticketStatus.js";
import ticketPriority from "../../models/ticketPriority.js";
import { consoleLog, errorMessage, successMessage } from "../../helper/index.js";
import ticketService from "../../services/ticketService.js";
import { User, Ticket } from "../../models/association.js";
import TicketActivity from "../../models/ticketActivity.js"
import { logger } from "../../helper/index.js";
import generateTrackId from "../../utils/getTicketTrackId.js";
import _ from "lodash";

export const getAllTickets = async (req, res, next) => {
    try {
        let userId      = req.auth.user.id;
        const trackId   = req.query.trackId || "";
        const category  = (req.query.category && req.query.category != "") ? req.query.category.split(','): [];
        const priority  = (req.query.priority && req.query.priority != "") ? req.query.priority.split(',') : [];
        const status    = (req.query.status && req.query.status != "") ? req.query.status.split(',') : [];

        const page 		   = parseInt(req.query.page) || 1;
        const pageSize 	   = parseInt(req.query.perPage) || 10; // length
        const offset 	   = (page - 1) * pageSize;
        let filter = { trackId,category,status,priority};
        let tickets         = await ticketService.ticketsGet({userId, filter, offset, pageSize});
        const response      = await successMessage({ data: tickets });
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getAllTickets",error);
        next(error);
    }
};

export const CreateTicket = async (req, res, next) => {
    try {
        req.query.type      = 'tickets';
        const userId        = req.auth.user.id;
		const statusId 		= await ticketService.getStatusID('New');
		if(!statusId.status) {
			const response = await errorMessage({code:1129, statusCode: 422 });
			return res.status(response.code).json(response.data);
		}
        uploadFile(req, res)
        .then(async (data) => {
            try {
                const validatedData         = req.body;
                const uploadedFiles         = data.file.map( item => process.env.IMAGE_URL + item.path);
				validatedData.attachments   = JSON.stringify(uploadedFiles);
				validatedData.statusId = statusId.data;
                const ticket           = await ticketService.CreateTicket({data: validatedData, userId});

                await ticketService.insertToHistory({ticketId: ticket.id, userId, activity:"ticket created", message:validatedData.message});
                const response = await successMessage({data: "ticket_created_successfully"});
                return res.status(response.code).json(response.data);
            } catch(error) {
                logger.error("Error from CreateTicket:- ", error);
                return next(error);
            }
        }).catch(async (error) => {
            try {
				if (error.message==="no_file_selected") {
					const validatedData = req.body;
					validatedData.statusId = statusId.data;
					const ticket = await ticketService.CreateTicket({data: validatedData, userId});
					await ticketService.insertToHistory({ticketId: ticket.id, userId, activity:"ticket created", message:validatedData.message});
					const response = await successMessage({data: "ticket_created_successfully"});
					return res.status(response.code).json(response.data);
				} else {
					const result = await errorMessage({ code: error.error, statusCode: 422 });
        			return res.status(result.code).json(result.data); 
				}
                
            } catch (error) {
                logger.error("Error from CreateTicket:- ", error)
                return next(error);
            }
        });
    } catch (error) {
        logger.log("Error from CreateTicket:- ", error)
        return next(error);
    }
};

export const getTicketPartials = async(req, res, next) => {
    try {
        const [categories, status, priorities] = await Promise.all([
            ticketService.getCategories(),
            ticketService.getStatus(),
            ticketService.getPriorities()
        ]);
        const response = await successMessage({data: {categories, status, priorities}});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.log("Error from getTicketPartials:- ", error)
        return next(error);
    }
};
export const getTicketDetailByID = async (req, res, next) => {
	try {
		let trackId  = req.params.id;
		const userId = req.auth.user.id;
		if(!trackId || trackId === '') {
			const response  = await errorMessage({code: 1128, statusCode: 422});
			return res.status(response.code).json(response.data);
		}

		let details = await ticketService.getTicketData({trackId, userId});
		const response = await successMessage({data: details});
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("Error from getTicketDetailByID:- ", error);
		next(error);
	}
};

export const getTrackId = async (req, res, next) => {
	const response = await successMessage({data: await generateTrackId(next)});
	return res.status(response.code).json(response.data);
}

export const sendMsg = async(req, res, next) => {
	try {
		const trackId 	= req.params.id;
		const userId 	= req.auth.user.id;
		const username = req.auth.user.username;
		req.query.type  = 'tickets';
		const checkTicketOwner = await ticketService.checkTicketOwner({userId, trackId});
		if(!checkTicketOwner) {
			const response = await errorMessage({code:1128, statusCode: 422});
			return res.status(response.code).json(response.data);
		}
		uploadFile(req, res)
        .then(async (data) => {
            try {
                const validatedData         = req.body;
                const uploadedFiles         = data.file.map( item => process.env.IMAGE_URL + item.path);
                validatedData.attachments   = JSON.stringify(uploadedFiles);
				await ticketService.sendMsg({userId, ticketId: checkTicketOwner.id, validatedData});
				await ticketService.insertToHistory({ticketId: checkTicketOwner.id, userId, activity:username + " replied to ticket", message:validatedData.msg});
				const response = await successMessage({data: "Reply successfully added"});
				return res.status(response.code).json(response.data);
            } catch(error) {
                logger.error("Error from RplyTicket:- ", error);
                return next(error);
            }
        }).catch(async (error) => {
            try {
				if (error.message==="no_file_selected") {
					const validatedData         = req.body;
					validatedData.attachments   = null;
					await ticketService.sendMsg({userId, ticketId: checkTicketOwner.id, validatedData});
					await ticketService.insertToHistory({ticketId: checkTicketOwner.id, userId, activity:username + " replied to ticket", message:validatedData.msg});
					const response = await successMessage({data: "Reply successfully added"});
					return res.status(response.code).json(response.data);
				} else {
					const result = await errorMessage({ code: error.error, statusCode: 422 });
        			return res.status(result.code).json(result.data);
				}
            } catch (error) {
                logger.error("Error from RplyTicket:- ", error)
                return next(error);
            }
        });
	} catch (error) {
		logger.error("Error from sendMsg:- ", error);
		next(error)
	}
}

export const getReplies = async(req, res, next) => {
	try {
		const userId  = req.auth.user.id;
		const trackId = req.params.id;
		const checkTicketOwner = await ticketService.checkTicketOwner({userId, trackId});
		if(!checkTicketOwner) {
			const response = await errorMessage({code:1128, statusCode: 422});
			return res.status(response.code).json(response.data);
		}
		const replies 			= await ticketService.getReplies({ticketId: checkTicketOwner.id, userId});
		const response 			= await successMessage({data: _.sortBy(replies, ['createdAt'])});
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("Error from getComments:- ", error);
		next(error)
	}
} 
export const ticketFaq = async(req, res, next) => {
	try {
		const faq 	= await ticketService.getFaq();
		const response 	= await successMessage({data: faq});
		return res.status(response.code).json(response.data);
	} catch (error) {
		logger.error("Error from ticketFaq:- ", error);
		next(error)
	}
}
export const ticketTimeline = async (req, res, next) => {
	try {
		const trackId 		= req.params.id;
		const userId 		= req.auth.user.id;
		const checkTicketOwner = await ticketService.checkTicketOwner({userId, trackId});
		if(!checkTicketOwner) {
			const response = await errorMessage({code:1128, statusCode: 422});
			return res.status(response.code).json(response.data);
		}
		let activityHistory = await ticketService.getTicketActivityHistory({trackId});
		const response 		= await successMessage({data: activityHistory});
		return res.status(200).json(response.data);
	} catch (error) {
		logger.error("Error from ticketTimeline:- ", error);
		next(error)
	}
};
