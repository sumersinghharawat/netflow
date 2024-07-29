import { Op } from "sequelize";
import { consoleLog, convertTolocal, errorMessage, logger, successMessage } from "../../helper/index.js";
import CRMService from "../../services/crmService.js";
import utilityService from "../../services/utilityService.js";
import { getLeadCompletion } from "../../helper/utility.js";
import Country from "../../models/countries.js";
import State from "../../models/states.js";
import { uploadFile } from "../../utils/fileUpload.js";
import { sequelize } from "../../config/db.js";

export const getCRMTiles= async (req,res,next) => {
    try {
        const userId   = req.auth.user.id;
        const crmTiles = await CRMService.getCRMTiles(userId);
        const countries = await utilityService.getAllCountriesAndStates(req,res,next);
        const response =  await successMessage({ data: {crmTiles, countries} });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getCRMTiles",error);
        return next(error);
    }
};

// export const getCRMDashboard= async (req,res,next) => {
//     try {

//         const currentDate = new Date();
//         currentDate.setUTCHours(0,0,0,0);
//         const crmFollowUps = await CRMService.getCRMFollowups(userId, offset, pageSize);

//         let [followupsToday, missedFollowups, recentLeads] =[[], [], []]; 
//         crmFollowUps.forEach((lead) => {
//             lead.createdAt     = convertTolocal(lead.createdAt);
//             const followupDate = new Date(lead.followupDate);
//             console.log("followupDate",followupDate);
//             if (followupDate.getTime()===currentDate.getTime()) followupsToday.push(lead);
//             if (followupDate.getTime()<currentDate.getTime()) missedFollowups.push(lead);
//             recentLeads.push(lead);
//         });

//         const response =  await successMessage({ data: {followupsToday, missedFollowups, recentLeads} });
// 		return res.status(response.code).json(response.data);
//     } catch (error) {
//         logger.error("ERROR FROM getCRMFollowups",error);
//         return next(error);
//     }
// };

export const getCRMFollowupsToday = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const page 		= parseInt(req.query.page) || 1;
		const pageSize 	= parseInt(req.query.perPage) || 10; // length
		const offset 	= (page - 1) * pageSize;
        const currentDate = new Date();
        const whereCondition = {
            addedBy: userId,
            leadStatus: 1,
            [Op.or]: {followupDate: currentDate, nextFollowupDate:currentDate}
            
        }
        const data = await CRMService.getCRMFollowups({ offset, pageSize, whereCondition });
        const crmFollowups = data.rows.map(lead => ({
            id: lead.id,
            firstName: lead.firstName,
            lastName: lead.lastName ? lead.lastName : null,
            skypeId: lead.skypeId ? lead.skypeId : null,
            emailId: lead.emailId ? lead.emailId : null,
            mobileNo: lead.mobileNo ? lead.mobileNo : null,
            countryId: lead.countryId ? lead.countryId : null,
            description: lead.description ? lead.description : null,
            interestStatus: lead.interestStatus,
            leadStatus: lead.leadStatus,

        }))
        const response =  await successMessage({ data: {
            totalCount: data.count,
			totalPages: Math.ceil(data.count / pageSize),
			currentPage: page,
			data: crmFollowups,
        } });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getCRMFollowupsToday",error);
        return next(error); 
    }
};

export const getCRMMissedFollowups = async (req,res,next) => {
    try {
        const userId    = req.auth.user.id;
        const page 		= parseInt(req.query.page) || 1;
		const pageSize 	= parseInt(req.query.perPage) || 10; // length
		const offset 	= (page - 1) * pageSize;
        const currentDate = new Date();
        const whereCondition = {
            addedBy: userId,
            leadStatus: 1,
            followupDate: { [Op.lt]: currentDate }
        }
        const data = await CRMService.getCRMFollowups({ offset, pageSize, whereCondition });
        const crmFollowups = data.rows.map(lead => ({
            id: lead.id,
            firstName: lead.firstName,
            lastName: lead.lastName ? lead.lastName : null,
            skypeId: lead.skypeId ? lead.skypeId : null,
            emailId: lead.emailId ? lead.emailId : null,
            mobileNo: lead.mobileNo ? lead.mobileNo : null,
            countryId: lead.countryId ?? null,
            description: lead.description ? lead.description : null,
            interestStatus: lead.interestStatus,
            leadStatus: lead.leadStatus,
        }))
        const response =  await successMessage({ data: {
            totalCount: data.count,
			totalPages: Math.ceil(data.count / pageSize),
			currentPage: page,
			data: crmFollowups,
        } });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getCRMMissedFollowups",error);
        return next(error); 
    }
};

export const getCRMRecentLeads = async (req,res,next) => {
    try {
        const userId    = req.auth.user.id;
        const page 		= parseInt(req.query.page) || 1;
		const pageSize 	= parseInt(req.query.perPage) || 10; // length
		const offset 	= (page - 1) * pageSize;
        const whereCondition = {
            addedBy: userId,
            leadStatus: 1,
        }
        const data = await CRMService.getCRMFollowups({ offset, pageSize, whereCondition });
        const crmFollowups = data.rows.map(lead => ({
            id: lead.id,
            firstName: lead.firstName ? lead.firstName : null,
            lastName: lead.lastName ? lead.lastName : null,
            skypeId: lead.skypeId ? lead.skypeId : null,
            emailId: lead.emailId ? lead.emailId : null,
            mobileNo: lead.mobileNo ? lead.mobileNo : null,
            countryId: lead.countryId ?? null,
            description: lead.description ? lead.description : null,
            followupDate: lead.followupDate ? convertTolocal(lead.followupDate) : null,
            interestStatus: lead.interestStatus,
            leadStatus: lead.leadStatus,
            leadCompletion: getLeadCompletion(lead),
            dateAdded: convertTolocal(lead.createdAt)
        }))
        const response =  await successMessage({ data: {
            totalCount: data.count,
			totalPages: Math.ceil(data.count / pageSize),
			currentPage: page,
			data: crmFollowups,
        } });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getCRMRecentLeads",error);
        return next(error); 
    }
};

export const addCRMLead = async(req,res,next) => {
    try {
        const crmData = req.body;
        const userId = req.auth.user.id;
        const result = await CRMService.addCRMLead({userId,crmData});
        const response =  await successMessage({ data: "CRM lead added successfully." });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM addCRMLead",error);
        return next(error);
    }
};

export const viewCRMLeads = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const {totalCount,totalPages,currentPage,rows} = await CRMService.getCRMLeads({req,userId});
        let countries = await Country.findAll({raw: true});
        countries = countries.map(element => ({
            label : element.name,
            value : element.id
        }));
        const data = rows.map(lead => ({
            id: lead.id,
            firstName: lead.firstName,
            lastName: lead.lastName ? lead.lastName : null,
            skypeId: lead.skypeId ? lead.skypeId : null,
            emailId: lead.emailId ? lead.emailId : null,
            mobileNo: lead.mobileNo ? lead.mobileNo : null,
            countryId: lead.countryId ?? null,
            description: lead.description ? lead.description : null,
            followupDate: lead.followupDate ? convertTolocal(lead.followupDate) : null,
            interestStatus: lead.interestStatus,
            leadStatus: lead.leadStatus,
            leadCompletion: getLeadCompletion(lead),
            dateAdded: convertTolocal(lead.createdAt)
        }))
        // const data = rows.map(lead => {
        //     // math.min to ensure the sum never exceeds 100
        //     const leadCompletion = Math.min(
        //         (lead.firstName ? 15 : 0) +
        //         (lead.lastName ? 15 : 0) +
        //         (lead.emailId ? 15 : 0) +
        //         (lead.skypeId ? 15 : 0) +
        //         (lead.mobileNo ? 15 : 0) +
        //         (lead.countryId ? 15 : 0) +
        //         (lead.description ? 15 : 0),
        //         100
        //     );
        
        //     const colour = leadCompletion <= 50
        //         ? "#f56b6b"
        //         : leadCompletion <= 75
        //         ? "#F5870A"
        //         : "rgba(50, 200, 150, 1)";
        //     return {
        //         fullName: `${lead.firstName} ${lead.lastName}`,
        //         leadCompletion: leadCompletion,
        //         colour,
        //         email: lead.emailId
        //     };
        // });

        const response =  await successMessage({ data: {countries,totalCount,totalPages,currentPage,data} });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM viewCRMLeads",error);
        return next(error);
    }
};

export const getCRMTimeline = async (req,res,next) => {
    try {
        const userId = req.auth.user.id;
        const leadId = req.query.id;
        const crmHistory = await CRMService.getCRMHistory(userId, leadId)
        const companyDetails = await utilityService.getCompanyProfile();
        const data = {
            id: crmHistory.id,
            addedBy: crmHistory.User.username,
            createdAt: convertTolocal(crmHistory.createdAt),
            description: crmHistory.description,
            leadCompletion: getLeadCompletion(crmHistory),
            firstEntry: true,
            companyName: companyDetails.name,
            followups: crmHistory.CrmFollowups?.map((item, index) => ({
                followupEnteredBy: item.User.username,
                description: item.description,
                image: item.image,
                followupDate: convertTolocal(item.followupDate).split(" ")[0],
                createdAt: convertTolocal(item.createdAt).split(" ")[0],
                firstEntry: false,
                direction: index % 2 === 0 ? 'direction-l' : 'direction-r',
            }))
        }

        const response =  await successMessage({ data: data });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getCRMTimeline",error);
        return next(error);
    }
};

export const getCRMGraph = async (req,res,next) => {
    try {
        const userId      = req.auth.user.id;
        const currentDate = new Date();
        const whereCondition = {
            addedBy: userId
        }
        const crmLeads    = await CRMService.getCRMFollowups({userId, whereCondition});
        const leadCountByMonth = await CRMService.getLeadsByMonth(crmLeads.rows, currentDate);

        const leadCountByDay = await CRMService.getLeadsByDay(crmLeads.rows, currentDate);

        const response =  await successMessage({ data: {leadCountByMonth, leadCountByDay} });
		return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM getCRMGraph",error);
        return next(error);
    }
};

export const updateLead = async (req, res, next) => {
    try {
        const userId        = req.auth.user.id;
        const leadId        = req.body.id;
        const updateData    = req.body;
        const checkLead     = await CRMService.checkLead({ userId, leadId});
        if(!checkLead) {
            const response  = await errorMessage({ code: 1111, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        await CRMService.updateLead({ leadId, updateData});
        const response = await successMessage({data: "Lead_Updated_Successfully"});
        return res.status(response.code).json(response.data);
        
    } catch (error) {
        logger.error("ERROR FROM updateLead",error);
        return next(error);
    }
};

export const addCRMFollowup = async (req, res, next) => {
    const type = req.query.type;
    const userId = req.auth.user.id;
    uploadFile(req, res)
        .then(async (data) => {
            logger.info("try block")
            try {
                let filepath = process.env.IMAGE_URL + data.file[0].path;
                const leadId = req.body.id;
                const description = req.body.description;
                const followupDate = convertTolocal(req.body.followupDate);
                let transaction = await sequelize.transaction();
                logger.info("followupDate",followupDate)
                await CRMService.addCRMFollowup({ transaction, leadId, userId: req.auth.user.id, description, image: filepath, followupDate })
                await CRMService.updateLead({ transaction, leadId, updateData: { followupDate } })
                await transaction.commit();
                const response = await successMessage({data: "Followup_Added_Successfully"});
                return res.status(response.code).json(response.data);
            } catch (error) {
                await transaction.rollback();
                logger.error("ERROR FROM addCRMFollowup try block", error);
                return next(error);
            }
        })
        .catch(async (error) => {
            logger.info("catch block")
            let transaction = await sequelize.transaction();
            try {
                if (error.message === "no_file_selected") {
                    const leadId = req.body.id;
                    const description = req.body.description;
                    const followupDate = convertTolocal(req.body.followupDate);
                    logger.info("req.body.followupDate",req.body.followupDate)
                    logger.info("followupDate",followupDate)
                    await CRMService.addCRMFollowup({ transaction, leadId, userId: req.auth.user.id, description,followupDate })
                    await CRMService.updateLead({ transaction, leadId, updateData: { followupDate } })
                    await transaction.commit();
                    const response = await successMessage({data: "Followup_Added_Successfully"});
                    return res.status(response.code).json(response.data);
                } else {
                    await transaction.rollback();
                    const result = await errorMessage({ code: error.error, statusCode: 422 });
                    return res.status(result.code).json(result.data);
                }
            } catch (error) {
                logger.info("inside the internal catch block")
                await transaction.rollback();
                logger.error("ERROR FROM addCRMFollowup catch block", error);
                const result = await errorMessage({ code: error.error, statusCode: 422 });
                return res.status(result.code).json(result.data);
            }
        })

};

export const addNextCRMFollowup = async (req, res, next) => {
    try {
        const leadId = req.body.id;
        const nextFollowupDate = convertTolocal(req.body.nextFollowupDate);

        await CRMService.addCRMFollowup({ leadId, userId: req.auth.user.id, followupDate:nextFollowupDate });
        await CRMService.updateLead({ leadId, updateData: { nextFollowupDate } })
        
        const response = await successMessage({ data: "Followup_Added_Successfully" });
        return res.status(response.code).json(response.data);
    } catch (error) {
        // await transaction.rollback();
        logger.error("ERROR FROM addNextCRMFollowup", error);
        return next(error);
    }
};

export const viewSingleLead = async(req, res, next) => {
    try {
        const leadId = req.query.id;
        const userId = req.auth.user.id;
        const countries = await utilityService.getAllCountriesAndStates(req,res,next);

        const lead = await CRMService.checkLead({userId, leadId});
        if (!lead) {
            logger.error("INVALID LEAD")
            const result = await errorMessage({ code: 1061, statusCode: 422 });
            return res.status(result.code).json(result.data);
        }
        const response = await successMessage({
            data: {
                details: {
                    id: lead.id,
                    firstName:        lead.firstName,
                    lastName:         lead.lastName ? lead.lastName : null,
                    skypeId:          lead.skypeId ? lead.skypeId : null,
                    emailId:          lead.emailId ? lead.emailId : null,
                    mobileNo:         lead.mobileNo ? lead.mobileNo : null,
                    country:          lead.countryId ? lead.Country.name : null,
                    countryId:        lead.countryId ? lead.countryId : null,
                    description:      lead.description ? lead.description : null,
                    followupDate:     lead.followupDate ? convertTolocal(lead.followupDate).split(" ")[0] : null,
                    nextFollowupDate: lead.nextFollowupDate ? convertTolocal(lead.nextFollowupDate).split(" ")[0] : null,
                    interestStatus:   lead.interestStatus,
                    leadStatus:       lead.leadStatus,
                    dateAdded:        convertTolocal(lead.createdAt)
                },
                leadCompletion: getLeadCompletion(lead),
                leadCompletionValues: {
                    "firstName": "15",
                    "lastName": "10",
                    "skypeId": "15",
                    "emailId": "15",
                    "mobileNo": "15",
                    "country": "15",
                    "description": "15",
                },
                countries
            }
        });
        return res.status(response.code).json(response.data);
    } catch (error) {
        logger.error("ERROR FROM viewLead", error);
        return next(error);
    }
}

