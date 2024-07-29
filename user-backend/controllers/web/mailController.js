import { consoleLog, convertTolocal, errorMessage, logger, successMessage } from "../../helper/index.js";
import Contact from "../../models/contact.js";
import MailBox from "../../models/mailBox.js";
import MailService from "../../services/mailService.js";
import getModuleStatus from "../../utils/getModuleStatus.js";


export const getMailInbox = async (req,res,next) => {
    try {
        const userId       = req.auth.user.id;
        const page 		   = parseInt(req.query.page) || 1;
		const pageSize 	   = parseInt(req.query.perPage) || 10; // length
		const offset 	   = (page - 1) * pageSize;
        const prefix       = req.prefix;
        let count          = 0;
        const moduleStatus = await getModuleStatus({attributes: ["mailboxStatus"]});

        if (!moduleStatus.mailboxStatus) {
            const response = await errorMessage({ code: 1057 });
            return res.json(response);
        }
        const inboxData         = await MailService.getSingleMails({userId, offset, limit:pageSize, prefix: req.prefix});
        inboxData.inboxUnreadCount  = await MailService.getUnReadMailCount({ type: 'inbox', toUserId: userId}); 
        inboxData.adminInboxUnreadCount  = await MailService.getUnReadMailCount({ type: 'adminMail', toUserId: userId}); 
        inboxData.replicaInboxUnreadCount = await MailService.getUnReadMailCount({ type: 'replica', toUserId: userId});
        const response = await successMessage({data: inboxData});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getMailInbox",error);
        return next(error);
    }
};

export const getAdminMail = async (req,res,next) => {
    try {
        const userId       = req.auth.user.id;
        const page 		   = parseInt(req.query.page) || 1;
		const pageSize 	   = parseInt(req.query.perPage) || 10; // length
		const offset 	   = (page - 1) * pageSize;
        const moduleStatus = await getModuleStatus({attributes: ["mailboxStatus"]});

        if (!moduleStatus.mailboxStatus) {
            const response = await errorMessage({ code: 1057 });
            return res.json(response);
        }
        const adminMail     = await MailService.getAdminMail(offset, pageSize);
        const totalCount 	= adminMail.count;
        const totalPages 	= Math.ceil(totalCount / pageSize);
        const currentPage 	= Math.floor(offset / pageSize) + 1;
        const inboxUnreadCount  = await MailService.getUnReadMailCount({ type: 'inbox', toUserId: userId}); 
        const adminInboxUnreadCount  = await MailService.getUnReadMailCount({ type: 'adminMail', toUserId: userId}); 
        const replicaInboxUnreadCount = await MailService.getUnReadMailCount({ type: 'replica', toUserId: userId}); 
        const result      = {
            inboxUnreadCount,
            adminInboxUnreadCount,
            replicaInboxUnreadCount,
            totalCount,
            totalPages,
            currentPage,
            data : adminMail.adminMail
        };

        const response = await successMessage({data: result});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getAdminMail",error);
        return next(error);
    }
};

export const getSentMail = async (req, res, next) => {
    try {
        const userId       = req.auth.user.id;
        const page 		   = parseInt(req.query.page) || 1;
		const pageSize 	   = parseInt(req.query.perPage) || 10; // length
		const offset 	   = (page - 1) * pageSize;
        const moduleStatus = await getModuleStatus({attributes: ["mailboxStatus"]});

        if (!moduleStatus.mailboxStatus) {
            const response = await errorMessage({ code: 1057 });
            return res.json(response);
        }

        const sentMailData  = await MailService.getSentMail(userId, offset, pageSize);
        const totalCount 	= sentMailData.count;
        const totalPages 	= Math.ceil(totalCount / pageSize);
        const currentPage 	= Math.floor(offset / pageSize) + 1;
        const inboxUnreadCount       = await MailService.getUnReadMailCount({ type: 'inbox', toUserId: userId}); 
        const adminInboxUnreadCount  = await MailService.getUnReadMailCount({ type: 'adminMail', toUserId: userId}); 
        const replicaInboxUnreadCount = await MailService.getUnReadMailCount({ type: 'replica', toUserId: userId});
        
        const result      = {
            inboxUnreadCount,
            adminInboxUnreadCount,
            replicaInboxUnreadCount,
            totalCount,
            totalPages,
            currentPage,
            data:sentMailData.sentMail
        };

        const response = await successMessage({data: result});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getSentMail",error);
        return next(error);
    }
};

export const getComposeMailData = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const downlineData = await MailService.getDownlinesForMail(userId);
        const downlines = downlineData.map(user => ({
            label: `${user["downlines.UserDetail.name"]} ${user["downlines.UserDetail.secondName"]} (${user["downlines.username"]})`,
            value: user["downlines.username"]
        }));

        const response = await successMessage({data: downlines});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getComposeMailData",error);
        return next(error);
    }
};

export const sendInternalMail = async (req, res, next) => {
    try {
        const userId = req.auth.user.id;
        const mailData = req.body;
        const moduleStatus = await getModuleStatus({attributes: ["mailboxStatus"]});

        if (!moduleStatus.mailboxStatus) {
            const response = await errorMessage({ code: 1057 });
            return res.json(response);
        }
        if (mailData.message == '<p><br></p>') {
            const response = await errorMessage({ code: 1123 });
            return res.status(422).json(response); 
        }

        if (mailData.type == "individual") {
            if ( req.body.username == '' || req.body.username == req.auth.user.username) {
                const response = await errorMessage({ code: 1070 });
                return res.status(422).json(response);
            }

            const result = await MailService.sendInternalMailToUser(userId, mailData);
            if (!result) {
                const response = await errorMessage({ code: 1070 });
                return res.status(422).json(response);
            }
        } else if (mailData.type == "team") {
            const result = await MailService.sendInternalMailToTeam(userId, mailData);
        } else if (mailData.type == "admin") {
            const result = await MailService.sendInternalMailToAdmin(userId, mailData);
        }

        const response = await successMessage({data: "Mail sent successfully."});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM sendInternalMail",error);
        return next(error);
    }
};

export const deleteMail = async (req,res,next) => {
    try {
        logger.info("req.body",req.body)
        const userId = req.auth.user.id;
        const mailId = req.body.mailId;
        const type   = req.body.type;
        logger.info("mailId",mailId)
        await MailService.deleteMail(userId, mailId, type);

        const response = await successMessage({data: "Mail deleted successfully."});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM deleteMail",error);
        return next(error);
    }
};

export const updateAllMail = async(req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const action = req.query.action || "read-all"
        await MailService.updateAllMail(userId, action);

        const response = await successMessage({data: "Mail deleted successfully."});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM deleteAllMail",error);
        return next(error);
    }
};

export const viewSingleMailThread = async(req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const mailId = req.query.mailId;
        const type   = req.query.type || 'inbox';
        if(!mailId || mailId === ''){
            const response = await errorMessage({ code: 1119, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const mail = await MailService.readMail({userId,mailId, type});
        if(!mail) {
            const response = await errorMessage({ code: 1048, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        if(type === 'inbox' || type === 'replica') await MailService.setReadStatus(mailId,type);
        mail.createdAt = convertTolocal(mail.createdAt);
        const response = await successMessage({data: mail});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM viewSingleMailThread",error);
        return next(error);
    }
};

export const replyToMail = async (req, res, next) => {
    try {
        const userId = req.auth.user.id;
        const mailData = req.body;
        const {subject,message,parentMailId} = mailData;
        const moduleStatus = await getModuleStatus({attributes: ["mailboxStatus"]});

        if (!moduleStatus.mailboxStatus) {
            const response = await errorMessage({ code: 1057 });
            return res.json(response);
        }
        await MailService.replyMail(userId,subject,message,parentMailId);

        const response = await successMessage({data: "Replied to mail."});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM replyToMail",error);
        return next(error);
    }
};

export const getReplicaContacts = async (req, res, next) => {
    try {
        const moduleStatus = await getModuleStatus({attributes: ["replicatedSiteStatus"]});
        if(!moduleStatus) {
            const response = await errorMessage({ code: 1057 });
            return res.json(response);
        }
        const page 		   = parseInt(req.query.page) || 1;
		const pageSize 	   = parseInt(req.query.perPage) || 10; // length
		const offset 	   = (page - 1) * pageSize;
        const userId       = req.auth.user.id;
        const data         = await MailService.getReplicaContacts({userId, offset, limit:pageSize});
        const totalCount   = data.count;
        const totalPages   = Math.ceil(totalCount / pageSize);
        const currentPage  = Math.floor(offset / pageSize) + 1;
        const inboxUnreadCount       = await MailService.getUnReadMailCount({ type: 'inbox', toUserId: userId}); 
        const adminInboxUnreadCount  = await MailService.getUnReadMailCount({ type: 'adminMail', toUserId: userId}); 
        const replicaInboxUnreadCount  = await MailService.getUnReadMailCount({ type: 'replica', toUserId: userId}); 
        
        const result       = {
            inboxUnreadCount,
            adminInboxUnreadCount,
            replicaInboxUnreadCount,
            totalCount,
            totalPages,
            currentPage,
            data:data.rows.map( item => ({
                id: item.id,
                subject: 'Replica Contact',
                message: `<p>name: ${item.name}</p><p>email: ${item.email}</p><p>address: ${item.address}</p>phone: ${item.phone}<p></p>
                            <p>other info: ${item.contactInfo}</p>`,
                name: item.name,
                createdAt: convertTolocal(item.createdAt)
            }))
        };
       
        const response = await successMessage({data: result});
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getReplicaContacts",error);
        return next(error);
    }
}