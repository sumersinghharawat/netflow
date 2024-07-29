import { Op } from "sequelize";
import {
  User,
  Ticket,
  TicketCategory,
  TicketStatus,
  TicketPriority,
  TicketTag,
  UserDetail,
  TicketActivity,
} from "../models/association.js";
import Tag from "../models/tag.js";
import consoleLog from "../helper/consoleLog.js";
import logger from "../helper/logger.js";
import convertTolocal from "../helper/convertTolocal.js";
import TicketComment from "../models/ticketComment.js";
import TicketFaq from "../models/ticketFaq.js";
import TicketReply from "../models/ticketReply.js";

class ticketService {
	async ticketsGet({ userId, filter, offset, pageSize }) {
		try {
			let whereStatement = {};
			if (userId) whereStatement.userId = userId;
			if (filter.trackId) whereStatement.trackId = filter.trackId;
			if (filter.category.length) whereStatement.categoryId = { [Op.in]: filter.category};
			if (filter.status.length) whereStatement.statusId = { [Op.in]: filter.status };
			if (filter.priority.length) whereStatement.priorityId = { [Op.in]: filter.priority};
			let tickets = await Ticket.findAndCountAll({
							where: whereStatement,
							include: [ "TicketCategory", "TicketStatus", "TicketPriority",
								{
									model: User,
									attributes: ["username"],
								},
								{model: User, as: "Assignee", "attributes": ["username"]}
							],
							offset,
							limit: pageSize,
							order: [["createdAt", "DESC"]],
						});
			const result = tickets.rows.map( item => ({
				id : item.id,
				trackId: item.trackId,
				subject: item.subject,
				message: item.message,
				assignee: (item.Assignee) ? item.Assignee.username : "NA",
				status: item.TicketStatus?.ticketStatus,
				category: item.TicketCategory?.categoryName,
				priority: item.TicketPriority ? item.TicketPriority.priority : "NA",
				createdAt: convertTolocal(item.createdAt),
				lastUpdated: convertTolocal(item.updatedAt)
			}));
			const totalCount = tickets.count;
			const totalPages = Math.ceil(totalCount / pageSize);
			const currentPage = Math.floor(offset / pageSize) + 1;
			const response = {
				totalCount,
				totalPages,
				currentPage,
				data: result,
			};
			return response;
		} catch (error) {
			logger.error("ERROR FROM ticketsGet Service", error);
			throw error;
		}
	}
	async CreateTicket({data, userId}) {
		try {
			data.userId = userId;
			return await Ticket.create(data);
		} catch (error) {
			logger.error("Error from ticketService:- ", error);
			throw error;
		}
	}
	async getCategories() {
		const categories = await TicketCategory.findAll({where: {status: 1}});
		return categories.map( item => ({
			label : item.categoryName,
			value: item.id
		}));
	}
	async getStatus() {
		const status = await TicketStatus.findAll({where: {status: 1}});
		return status.map( item => ({
			label : item.ticketStatus,
			value: item.id
		}));
	}
	async getPriorities() {
		const priorities = await TicketPriority.findAll({where: {status: 1}});
		return priorities.map( item => ({
			label : item.priority,
			value: item.id
		}));
	}
	async getStatusID(name) {
		try {
			const status =  await TicketStatus.findOne({where:{ticketStatus: name}});
			if(!status) return { status: false};
			return {status: true, data:status.id};
		} catch (error) {
			logger.error("Error from getNewStatusID:- ", error);
			throw error;
		}
	}

	async getTicketData({trackId, userId}) {
		try {
			const ticketData 	= await Ticket.findOne({
									attributes: ["id","trackId","createdAt","updatedAt","subject","message","attachments"],
									where: { trackId, userId },
									include: ["TicketStatus", "TicketCategory", "TicketPriority",
										{
											model: User, attributes: ["username", "email"],
											include: [
												{model:UserDetail, attributes:["name","secondName","image"]}
											]
										},
										{
											model: Tag,
											as: "TicketTags",
											through:{attributes:[]},
											required: false
										},
										{
											model: User,
											as: "Assignee"
										}
									],
								});
			return {
				id: ticketData.id,
				trackId: ticketData.trackId,
				subject: ticketData.subject,
				message: ticketData.message,
				attachments: JSON.parse(ticketData.attachments),
				userId,
				username: ticketData?.User?.username,
				fullName: ticketData?.User?.UserDetail?.name +' '+ ticketData?.User?.UserDetail?.secondName,
				image: ticketData?.User?.UserDetail?.image,
				tags: ticketData.TicketTags.map( item => item.tag),
				status: ticketData.TicketStatus?.ticketStatus,
				category: ticketData.TicketCategory?.categoryName,
				priority: ticketData.TicketPriority?.priority,
				assignee: ticketData.Assignee ?? null,
				createdAt: convertTolocal(ticketData.createdAt),
				updatedAt: convertTolocal(ticketData.updatedAt),
			}
		} catch (error) {
			logger.error("Error from getTicketData service:- ", error);
			throw error;
		}
	}

	async checkTicketOwner({userId, trackId}) {
		try {
			return await Ticket.findOne({where: {userId, trackId}});
		} catch (error) {
			logger.error("Error from checkTicketOwner service:- ", error);
			throw error;
		}
	}

	async sendMsg({userId, ticketId, validatedData}){
		try {
			return await TicketReply.create({
				ticketId, userId, message: validatedData.msg, image: validatedData.attachments
			});
		} catch (error) {
			logger.error("Error from sendMsg service:- ", error);
			throw error;
		}
	}
	async getComments({ticketId}) {
		try {
			const comments = await TicketComment.findAll({
				where:{ ticketId},
				order: ["id"]
				// include:[{
				// 	model: User,
				// 	as: "CommentedBy",
				// 	through:{ attributes:[]},
				// 	attributes:["username"]
				// }]
			});
			return comments.map( item => ({
				id: item.id,
				ticketId: item.ticketId,
				message: item.comment,
				isLeft: false,
				image: null,
				// commentedBy: item.CommentedBy[0].username,
				createdAt: convertTolocal(item.createdAt)
			}));
		} catch (error) {
			logger.error("Error from getComments service:- ", error);
			throw error;
		}
	}
	async getReplies({ticketId, userId}) {
		try {
			const replies = await TicketReply.findAll({
				where: {ticketId},
				order: ["id"],
				include:[
					{
						model:User, 
						attributes:["id", "username"], 
						include:[{model:UserDetail, attributes:["name", "secondName", "image"]}]
					}
				]
			});
			return replies.map(item => {
				let attachments;
				try {
					attachments = JSON.parse(item.image);
					if (!Array.isArray(attachments)) {
						throw new Error('Parsed value is not an array');
					}
				} catch (error) {
					attachments = item.image ? [item.image] : null;
				}

				return {
					id: item.id,
					username: item.User.username,
					name: item.User.UserDetail.name + item.User.UserDetail.secondName,
					image: item.User?.UserDetail.image,
					ticketId: item.ticketId,
					message: item.message,
					isLeft: (item.userId === userId) ? true : false,
					attachments: attachments,
					createdAt: convertTolocal(item.createdAt)
				}
			});
		} catch (error) {
			logger.error("Error from getReplies service:- ", error);
			throw error;
		}
	}

	async getTicketId(ticket_id) {
		try {
			let result = await Ticket.findOne({
			attributes: ["id"],
			where: { track_id: ticket_id },
			});
			return result.id;
		} catch (error) {
			console.log(error.message);
		}
	}

	async replyTicket(ticketId, message, user_id, file_name, t) {
	try {
		let replies = await TicketReplies.create({
		ticket_id: ticketId,
		user_id: user_id,
		message: message,
		image: file_name ? file_name : "",
		status: 1,
		});

		return true;
	} catch (error) {
		console.log(error);
		return false;
	}
	}

	async insertToHistory({ticketId, userId, activity, message}) {
		try {
			return await TicketActivity.create({
				ticketId,
				doneby: userId,
				donebyUsertype: "user",
				activity: activity,
				ifReply: message,
			});
		} catch (error) {
			logger.log("Error From insertToHistory:- ", error);
			throw error;
		}
	}
	async getFaq() {
		try {
			return await TicketFaq.findAll({
				where: { status: 1},
				include: ["TicketCategory"]
			})
		} catch (error) {
			logger.log("Error From getFaq:- ", error);
			throw error;
		}
	}
	async getTicketActivityHistory({trackId}) {
		const ticket = await Ticket.findOne({
							attributes: ["id", "trackId"],
							where: { trackId },
							include:[
								{
									model:TicketActivity,
									order: ["createdAt"]
								}
							]
						});
		let data = {
			id: ticket.id,
			trackId: ticket.trackId
		};
		data.TicketActivity = ticket.TicketActivities.map( item => ({
			id: item.id,
			doneByUserType: item.doneByUserType?? 'admin',
			activity: item.activity,
			comment: (item.doneByUserType || item.doneByUserType == 'user') ? item.ifReply : item.ifComment,
			date: convertTolocal(item.createdAt)
		}));
		return data;
	}
}

export default new ticketService();
