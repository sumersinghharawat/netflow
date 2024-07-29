import { Op, Sequelize } from "sequelize";
import { consoleLog, convertToUTC, logger, successMessage, errorMessage, convertTolocal } from "../helper/index.js";
import CrmLead from "../models/crmLead.js";
import CrmFollowup from "../models/crmFollowup.js";
import User from "../models/user.js";
import Country from "../models/countries.js";

class CRMService {
    async getCRMTiles(userId) {
        try {
        const dayStart    = new Date();
        dayStart.setHours(0,0,0);

        const crmLeads = await CrmLead.findOne({
            attributes:[
                [Sequelize.fn("COUNT", Sequelize.literal("CASE when lead_status=0 THEN 1 END")),"rejectedLeads"],
                [Sequelize.fn("COUNT", Sequelize.literal("CASE when lead_status=1 THEN 1 END")),"ongoingLeads"],
                [Sequelize.fn("COUNT", Sequelize.literal("CASE when lead_status=2 THEN 1 END")),"acceptedLeads"],
                // [Sequelize.fn("COUNT", Sequelize.literal("CASE when lead_status=1 AND created_at>= :dayStart THEN 1 END")),"ongoingLeadsToday"],
            ],
            where: {
                addedBy: userId,
            },
            replacements: { dayStart },
            // raw: true
        });
        return crmLeads;
        } catch (error) {
            logger.error("ERROR FROM getCRMTiles",error);
            throw error;
        }
    }
    async getCRMFollowups({offset, pageSize, whereCondition}) {
        const rows = await CrmLead.findAll({
            include:[{
                model: CrmFollowup,
                required: false
            }],
            where: whereCondition,
            offset: offset,
            limit: pageSize
        });
        const count = await CrmLead.count({
            where: whereCondition,
        })
        return {rows, count};
    }

    async addCRMLead({ userId, crmData}) {
        crmData.addedBy             = userId;
        crmData.confirmationDate    = null;
        crmData.followupDate        = (crmData.followupDate) ? convertToUTC(crmData.followupDate) : null;
        crmData.interestStatus      = (crmData.interestStatus) ? crmData.interestStatus : 1;
        crmData.leadStatus          = (crmData.leadStatus) ? crmData.leadStatus : 1;

        await CrmLead.create(crmData);
        return true;
    }
    async getLeadsByMonth(crmLeads, currentDate) {
        const graphFormat = {"Jan":0, "Feb":0, "Mar":0, "Apr":0, "May":0, "Jun":0, 
            "Jul":0, "Aug":0, "Sep":0, "Oct":0, "Nov":0, "Dec":0};
        const currentYear = currentDate.getFullYear();
        const leadCountByMonth = {
            accepted: { ...graphFormat },
            ongoing: { ...graphFormat },
            rejected: { ...graphFormat }
        };
        crmLeads.forEach(lead => {
            const date = new Date(lead.createdAt);
            if (date.getFullYear()==currentYear) {
                const month = date.toLocaleString("default", {month:"long"}).substring(0,3);
                if (lead.leadStatus === 0) {
                    leadCountByMonth.rejected[month] += 1;
                } else if (lead.leadStatus === 1) {
                    leadCountByMonth.ongoing[month] += 1;
                } else if (lead.leadStatus === 2) {
                    leadCountByMonth.accepted[month] += 1;
                }
            }
        });
    
        return leadCountByMonth;
    }

    async getLeadsByDay(crmLeads, currentDate) {
        //  By passing 0 as the day parameter, 
        // it represents the day before the 1st day of the next month. 
        const daysInMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0).getDate();
        const currentMonth = currentDate.getMonth();
        const leadCountByDay = {
            accepted: {},
            ongoing: {},
            rejected: {}
        };

        // initialise the dict
        for (let day = 1; day <= daysInMonth; day++) {
            leadCountByDay.accepted[day] = 0;
            leadCountByDay.ongoing[day] = 0;
            leadCountByDay.rejected[day] = 0;
        }
    
        crmLeads.forEach(lead => {
            const date = new Date(lead.createdAt);
            if (date.getMonth() === currentMonth) {
                const day = date.getDate();
                if (lead.leadStatus === 0) {
                    leadCountByDay.rejected[day] += 1;
                } else if (lead.leadStatus === 1) {
                    leadCountByDay.ongoing[day] += 1;
                } else if (lead.leadStatus === 2) {
                    leadCountByDay.accepted[day] += 1;
                }
            }
        });

        return leadCountByDay;
    }

    async getCRMLeads({req, userId}) {
        try {
            const page 		= parseInt(req.query.page) || 1;
            const pageSize 	= parseInt(req.query.perPage) || 10; // length
            const offset 	= (page - 1) * pageSize;
            const { 
                tag, status, addedDateFrom = '', addedDateTo = '',
                levelofInterest = '', statusChangeDateFrom = '', statusChangeDateTo = '',
                country, nextFollowupFromDate = '', nextFollowupToDate = ''
            } = req.query;
            let whereCondition = { addedBy : userId};
            if([0,1,2].includes(parseInt(status)))  whereCondition.leadStatus = status;
            if([0,1,2].includes(parseInt(levelofInterest)))  whereCondition.interestStatus = levelofInterest;
            if(country && country != '') whereCondition.countryId = country;
            if(tag && tag != '') {
                whereCondition[Op.or] = [
                    {firstName: {[Op.like]: `%${tag}%`}},
                    {lastName: {[Op.like]: `%${tag}%`}},
                    {emailId: {[Op.like]: `%${tag}%`}},
                    {skypeId: {[Op.like]: `%${tag}%`}},
                    {mobileNo: {[Op.like]: `%${tag}%`}}
                ] 
            };
            if(addedDateFrom != '' && addedDateTo === ''){
                const formatedFromDate = new Date(convertToUTC(addedDateFrom));
                whereCondition.createdAt = {[Op.gte] : formatedFromDate};
            } else if(addedDateFrom == '' && addedDateTo != '') {
                const formatedToDate = new Date(convertToUTC(addedDateTo));
                whereCondition.createdAt = {[Op.lte] : formatedToDate};
            } else if( addedDateFrom != '' && addedDateTo != '') {
                const formatedFromDate   = new Date(convertToUTC(addedDateFrom));
                const formatedToDate     = new Date(convertToUTC(addedDateTo));
                formatedToDate.setDate(formatedToDate.getDate() + 1);
                whereCondition.createdAt = {[Op.between] : [formatedFromDate,formatedToDate]};
            }

            if(nextFollowupFromDate != '' && nextFollowupToDate === ''){
                const formatedFromDate = new Date(convertToUTC(nextFollowupFromDate));
                whereCondition.followupDate = {[Op.gte] : formatedFromDate};
            } else if(nextFollowupFromDate == '' && nextFollowupToDate != '') {
                const formatedToDate = new Date(convertToUTC(nextFollowupToDate));
                whereCondition.followupDate = {[Op.lte] : formatedToDate};
            } else if( nextFollowupFromDate != '' && nextFollowupToDate != '') {
                const formatedFromDate   = new Date(convertToUTC(nextFollowupFromDate));
                const formatedToDate     = new Date(convertToUTC(nextFollowupToDate));
                formatedToDate.setDate(formatedToDate.getDate() + 1);
                whereCondition.followupDate = {[Op.between] : [formatedFromDate,formatedToDate]};
            }

            if(statusChangeDateFrom != '' && statusChangeDateTo === ''){
                const formatedFromDate = new Date(convertToUTC(statusChangeDateFrom));
                whereCondition.confirmationDate = {[Op.gte] : formatedFromDate};
            } else if(statusChangeDateFrom == '' && statusChangeDateTo != '') {
                const formatedToDate = new Date(convertToUTC(statusChangeDateTo));
                whereCondition.confirmationDate = {[Op.lte] : formatedToDate};
            } else if( statusChangeDateFrom != '' && statusChangeDateTo != '') {
                const formatedFromDate   = new Date(convertToUTC(statusChangeDateFrom));
                const formatedToDate     = new Date(convertToUTC(statusChangeDateTo));
                formatedToDate.setDate(formatedToDate.getDate() + 1);
                whereCondition.confirmationDate = {[Op.between] : [formatedFromDate,formatedToDate]};
            }

            const leads     = await CrmLead.findAndCountAll({
                where: whereCondition,
                offset,
                limit: pageSize,
                raw: true
            });

            leads.rows.forEach( (item) => {
                item.followupDate       = item.followupDate ? convertTolocal(item.followupDate) : null;
                item.confirmationDate   = convertTolocal(item.confirmationDate);
                item.leadStatus         = item.leadStatus;
                item.interestStatus     = item.interestStatus;
                item.createdAt          = convertTolocal(item.createdAt);
                item.updatedAt          = convertTolocal(item.updatedAt);
            });
            const totalCount 	= leads.count;
            const totalPages 	= Math.ceil(totalCount / pageSize);
            const currentPage 	= Math.floor(offset / pageSize) + 1;
            const response      = {
                totalCount,
                totalPages,
                currentPage,
                rows : leads.rows
            };
            return response;
        } catch (error) {
            logger.error('Error from CRM view:- ', error);
            throw error;
        }
        
    }
    async updateLead({transaction, leadId, updateData }) {
        try {
            const options = transaction ? { where: { id: leadId }, transaction } : { where: { id: leadId } };
            await CrmLead.update(updateData, options);
        } catch (error) {
            logger.error("ERROR FROM updateLead service")
            throw error;
        }
    }
    async checkLead({userId, leadId}) {
        return await CrmLead.findOne({
            where: {
                addedBy: userId,
                id: parseInt(leadId)
            },
            include: [
                {
                    model: Country,
                    attributes:["name"]

                }
            ]
        })
    }
    async searchLead({userId, query}) {
        const data  = await CrmLead.findAll({
            where: {
                addedBy: userId,
                [Op.or] : [
                    {firstName: {[Op.like] : `%${query}%`}},
                    {lastName: {[Op.like] : `%${query}%`}},
                    {emailId: {[Op.like] : `%${query}%`}},
                ]
            }
        });
        data.forEach( (item) => {
            item.followupDate       = convertTolocal(item.followupDate);
            item.confirmationDate   = convertTolocal(item.confirmationDate);
            item.leadStatus         = item.leadStatus;
            item.interestStatus     = item.interestStatus;
            item.createdAt          = convertTolocal(item.createdAt);
            item.updatedAt          = convertTolocal(item.updatedAt);
        });
        return await successMessage({data});
    }

    async addCRMFollowup({transaction, leadId, userId, description, image, followupDate}) {
        const options = transaction ? {transaction} : {};
        return await CrmFollowup.create({
            leadId: leadId,
            followupEnteredBy: userId,
            description: description ?? null,
            image: image ?? null,
            followupDate: followupDate,
        }, options)
    }

    async getCRMHistory(userId, leadId) {
        return await CrmLead.findOne({
            // attributes: ["id","firstName", "addedBy", "createdAt", "description"],
            include: [
                {
                    model: CrmFollowup,
                    attributes: ["followupEnteredBy", "description", "image", "followupDate","createdAt"],
                    required: false,
                    include: [
                        {
                            model: User,
                            attributes: ["username"]
                        },
                    ]
                },
                {
                    model: User,
                    attributes: ["username"]
                },
            ],
            where: { addedBy: userId, id: leadId }
        });
    }


}

export default new CRMService;