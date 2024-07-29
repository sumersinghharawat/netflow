import nodemailer from "nodemailer";
import { Op, Sequelize } from "sequelize";
import {Contact, MailBox, Treepath, User, UserDetail} from "../models/association.js";
import logger from "../helper/logger.js";
import convertTolocal from "../helper/convertTolocal.js";
import {sequelize} from "../config/db.js";
import CommonMailSetting from "../models/commonMailSetting.js";
import MailSetting from "../models/mailSetting.js";
import { mailConfig } from "../utils/nodeMailer.js";
import _ from "lodash";
import CompanyProfile from "../models/companyProfile.js";
import utilityService from "./utilityService.js";
import consoleLog from "../helper/consoleLog.js";
import moment from "moment/moment.js";
class MailService {
    async getThreadsInbox(prefix, userId, offset, pageSize) {
        try {
            const threadData = await MailBox.findAll({
                where: {
                    id: {
                        [Op.in] : Sequelize.literal(`(
                            SELECT MAX(mailbox.id) 
                            FROM ${prefix}_mail_boxes AS mailbox
                            INNER JOIN ${prefix}_mail_boxes AS mb 
                            ON mailbox.thread = mb.id AND mailbox.to_user_id = :userId 
                            GROUP BY mailbox.thread
                            )`)
                    }
                },
                replacements: {userId},
                include: [
                    {
                        model:User,
                        as: "fromUser",
                        attributes:["id","username"],
                        include:[{
                            model: UserDetail,
                            attributes:["name","secondName","image"]
                        }]
                    },
                    {
                        model: User,
                        as: "toUser",
                        attributes:["id","username"],
                        include:[{
                            model: UserDetail,
                            attributes:["name","secondName","image"]
                        }]
                    }
                ],
                raw:true
            });
            // console.log("threadData",threadData)
            
            // find the parent of each thread
            const threadIds   = threadData.map(mail => mail.thread);
            const threadHeads = await MailBox.findAll({
                where: {
                    id: {[Op.in]: threadIds}
                },
                raw:true
            });
            // find the length of each thread
            const threadCounts = await MailBox.findAll({
                attributes: [
                    "thread",
                    [Sequelize.fn("count", Sequelize.col("thread")), "count"]
                ],
                where: {
                    inboxDeleteStatus: 0,
                    thread: threadIds
                },
                group: ["MailBox.thread"],
                raw: true
            });
            return {threadData,threadIds,threadHeads, threadCounts};
        } catch (error) {
            logger.error("ERROR IN getThreadsInbox");
            throw error;
        }}

    async getSingleMails({userId, limit, offset, prefix}) {
        const query = `WITH LatestMail AS (
                SELECT
                thread,
                MAX(id) AS "latest_id"
                FROM
                ${prefix}_mail_boxes
                WHERE
                to_user_id = :userId AND inbox_delete_status = 0
                GROUP BY thread
            )
    
            SELECT
                MailBox.id,
                MailBox.subject,
                MailBox.message,
                MailBox.thread,
                MailBox.inbox_delete_status,
                MailBox.date,
                MailBox.read_status,
                MailBox.sent_delete_status,
                FromUser.username AS fromUsername,
                CONCAT(FromUserDetails.name,FromUserDetails.second_name) AS fromUserFullName,
                FromUserDetails.image AS fromUserImage,
                MailBox.created_at AS createdAt
            FROM
                ${prefix}_mail_boxes AS MailBox
            JOIN LatestMail
            ON
                MailBox.id = LatestMail.latest_id
            LEFT JOIN ${prefix}_users AS FromUser
            ON
                MailBox.from_user_id = FromUser.id
            LEFT JOIN ${prefix}_user_details AS FromUserDetails
            ON
                FromUserDetails.user_id = FromUser.id
            WHERE
                MailBox.to_user_id = :userId AND MailBox.inbox_delete_status = 0
            ORDER BY
                createdAt DESC`
        const queryLimiter = ` LIMIT :limit OFFSET :offset;`
        const singleMailData = await sequelize.query(query+queryLimiter, {
            replacements: {
                userId,
                limit,
                offset
            },
            type: sequelize.QueryTypes.SELECT,
        });
        const mailCount = await sequelize.query(query, {
            replacements: {
                userId,
            },
            type: sequelize.QueryTypes.SELECT,
        });
        let data = singleMailData.map( item => ({
            id            : item.id,
            subject       : item.subject,
            message       : item.message,
            name          : item.fromUserFullName,
            fromUsername  : item.fromUsername,
            fromUserImage : item.fromUserImage,
            // toUser      : item.toUser,
            inboxDeleteStatus : item.inbox_delete_status,
            sentDeleteStatus  : item.sent_delete_status,
            thread        : item.thread,
            readStatus    : item.read_status,
            createdAt     : convertTolocal(item.createdAt)
        }));
        const totalCount 	= mailCount.length;
        const totalPages 	= Math.ceil(totalCount / limit);
        const currentPage 	= Math.floor(offset / limit) + 1;
        return {
            totalCount,
            totalPages,
            currentPage,
            data
        };
    }

    async getAdminMail(offset, pageSize) {
        const adminMailData = await MailBox.findAndCountAll({ 
            where: { toAll: 1, sentDeleteStatus: 0 },
            include:[{
                model: User,
                as: "fromUser",
                attributes: ["username"],
                include:[{
                    model: UserDetail,
                    attributes:["name","secondName","image"]
                }] 
            }],
            offset: offset,
            limit: pageSize,
            order: [["createdAt","DESC"]],
            raw:true
        });

        const adminMail = adminMailData.rows.map(mail => ({
            id: mail.id,
            subject: mail.subject,
            message: mail.message,
            createdAt: (mail.createdAt),
            toAll: mail.toAll,
            fromUsername: mail["fromUser.username"],
            fromUser: `${mail["fromUser.UserDetail.name"]} ${mail["fromUser.UserDetail.secondName"]}`,
            fromUserImage: mail["fromUser.UserDetail.image"],
        }));
        return {adminMail, count: adminMailData.count};
    }

    async getSentMail(userId, offset, pageSize) {
        const sentMailData = await MailBox.findAndCountAll({
            where: {fromUserId: userId, sentDeleteStatus: 0},
            include: [
                {
                    model: User,
                    as: "toUser",
                    attributes:["username"],
                    include:[{
                        model: UserDetail,
                        attributes:["name","secondName","image"]
                    }]
                }
            ],
            offset: offset,
            limit: pageSize,
            order: [["createdAt","DESC"]],
            raw:true
        });
        const sentMail = sentMailData.rows.map(mail => ({
            id: mail.id,
            subject: mail.subject,
            message: mail.message,
            createdAt: convertTolocal(mail.createdAt),
            toUsername: mail["toUser.username"],
            toUser: `${mail["toUser.UserDetail.name"]} ${mail["toUser.UserDetail.secondName"]}`,
            toUserImage: mail["toUser.UserDetail.image"],
        }));
        return {count: sentMailData.count,sentMail};
    }

    async getDownlinesForMail(userId) {
        const downlineData = await Treepath.findAll({
            attributes:[],
            where:{ ancestor:userId, [Op.not]: [{descendant:userId}] },
            include: [
                {
                    model: User,
                    as: "downlines",
                    attributes: ["username"],
                    include:[{
                        model: UserDetail,
                        attributes:["name","secondName"]
                    }]
                }
            ],
            raw:true
        });
        return downlineData;
    }

    async sendInternalMailToUser(userId, mailData) {
        const toUserId = await User.findOne({ attributes: ["id"], where: { username: mailData.username } });
        const currentDate = new Date();
        if (!toUserId) {
            return false;
        }
        const mail = await MailBox.create({
            fromUserId: userId,
            toUserId: toUserId.id,
            toAll: 0,
            subject: mailData.subject,
            message: mailData.message,
            date: currentDate,
            inboxDeleteStatus: 0,
            readStatus: 0,
        });
        mail.update({ thread: mail.id });
        return true;
    }

    async sendInternalMailToTeam(userId, mailData) {
        const currentDate = new Date();
        const downlineData = await this.getDownlinesForMail(userId);
        
        const mailBoxData = downlineData.map(user => ({
            fromUserId: userId,
            toUserId: user["downlines.UserDetail.id"],
            toAll: 0,
            subject: mailData.subject,
            message: mailData.message,
            date: currentDate,
            inboxDeleteStatus: 0,
            readStatus: 0,
        }));
        
        await MailBox.bulkCreate(mailBoxData);
        return true;
    }

    async sendInternalMailToAdmin(userId, mailData) {
        const currentDate = new Date;
        const { id: adminId } = await User.findOne({ attributes:["id"], where:{userType:"admin"}, raw:true});
        
        await MailBox.create({
            fromUserId: userId,
            toUserId: adminId,
            toAll: 0,
            subject: mailData.subject,
            message: mailData.message,
            date: currentDate,
            inboxDeleteStatus: 0,
            readStatus: 0,
        });
        return true;
    }

    async deleteMail(userId, mailId, type) {
        if (type === "contacts") {
            await Contact.update({ status: 0 }, { where: { id: { [Op.in]: mailId }, ownerId: userId } });
            return true;
        }
        let mails = await MailBox.findAll({ where: { id: { [Op.in]: mailId } } });
        mails.forEach(async mail => {
            if (mail.fromUserId == userId) {
                await MailBox.update({ sentDeleteStatus: 1 }, { where: { id: mail.id } });
            } else if (mail.toUserId == userId) {
                await MailBox.update({ inboxDeleteStatus: 1 }, { where: { id: mail.id } });
            }
        })
        
        return true;
    }

    async updateAllMail(userId, action) {
        let condition = {};
        if (action == "delete-all") {
            condition = { inboxDeleteStatus: 1 }
        } else if (action == "read-all") {
            condition = { readStatus: 1 }
        }
        await MailBox.update(condition, {
            where: {
                toUserId: userId
            }
        });
    }

    async readMail({ userId,mailId, type }) {
        try {
            const whereCondition = (type === 'inbox')
                                    ? { id: mailId, toUserId: userId, inboxDeleteStatus: 0}
                                    : (type === 'sent') ? { id: mailId, fromUserId: userId, sentDeleteStatus: 0}: { id: mailId, toAll:1 };
            let mail = await MailBox.findAll({
                where: whereCondition
            });
            if(!mail[0]) return false;
            if(mail[0].thread) {
                const mailData = await MailBox.findAll({
                    where: {
                        [Op.or]: [
                            {thread: mail[0].thread},
                            {id: mail[0].id},
                        ]
                    },
                    include: [
                        {
                            model: User,
                            as : "fromUser",
                            attributes: ["username", "email"],
                            include: [{
                                model: UserDetail,
                                attributes:["id","image"]
                            }]
                        },
                        {
                            model: User,
                            as : "toUser",
                            attributes: ["username", "email"],
                            include: [{
                                model: UserDetail,
                                attributes:["id","image"]
                            }]
                        }
                    ],
                    order: [['createdAt', 'ASC']],
                    raw: true
                });
                logger.info("mailData",mailData)
                mail = mailData.map(mail => ({
                    ...mail,
                    createdAt     : convertTolocal(mail.createdAt),
                    fromUsername  : mail["fromUser.username"],
                    fromUserMail  : mail["fromUser.email"],
                    fromUserImage : mail["fromUser.UserDetail.image"],
                    toUsername    : mail["toUser.username"],
                    toUserMail    : mail["toUser.email"],
                    toUserImage   : mail["toUser.UserDetail.image"],
                    display       : mail.id==mailId
                }))
            }
            return mail;
        } catch (error) {
            logger.error("ERROR FROM readMail Service",error);
            throw error;
        }
    }

    async setReadStatus(mailId,type) {
        if (type === "replica") {
            await Contact.update({ readMsg: 1 }, { where: { id: mailId } })
        } else {
            await MailBox.update({readStatus:1}, {where: {id: mailId}});
        }
    }

    async replyMail(userId, subject,message,parentMailId) {
        const currentDate = new Date();
        const parentMail = await MailBox.findOne({where: {id: parentMailId}});

        // TODO can frontend pass threadId instead
        const threadId = parentMail.thread ?? parentMail.id;

        await MailBox.create({
            fromUserId: userId,
            toUserId: parentMail.fromUserId,
            toAll: 0,
            subject: subject,
            message: message,
            date: currentDate,
            inboxDeleteStatus: 0,
            thread:threadId,
            readStatus: 0,
        });
    }


    async getUnReadMailCount({ type, toUserId}) {
        const currentDate = moment();
        const prevDate    = moment().subtract(2, 'days');
        if (type === "replica") {
            return await Contact.count({ where: { ownerId: toUserId, readMsg: 0, status: 1 } })
        }
        let where = ( type === 'inbox')
                    ? {readStatus: 0, inboxDeleteStatus : 0, toAll: 0, toUserId }
                    : {toAll: 1, date: { [Op.between] : [prevDate, currentDate]}};
        return await MailBox.count({where});
    }

    async sentNotificationMail({mailType, toData, authUser, mailDetails}) {
        try {
            const mailSettings      = await MailSetting.findOne();
            const Mail              = await mailConfig(mailSettings);
            let mailOptions         = await utilityService.getMailOptions({mailSettings, email:toData.to, type:mailType, toData, authUser, mailDetails});
            Mail.sendMail(mailOptions)
                .then(data => {
                    logger.info("Mail sent successfully");
                }).catch(error => {
                    logger.error("ERROR FROM sendMail", error);
                })
        } catch (error) {
            logger.error("External Mail error:- \n", error);
        }
        return true;
    }
    async getReplicaContacts({userId, offset, limit}) {
        try {
            return await Contact.findAndCountAll({
                where: { ownerId: userId, status: 1 },
                offset: offset,
                limit,
                order: [['createdAt', 'DESC']],
                raw: true
            });
        } catch (error) {
            logger.error("Error from getReplicaContacts Service:- \n", error);
            throw error;
        }
    }
}

export default new MailService;