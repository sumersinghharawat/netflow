import _ from 'lodash';
import { Op, col, literal, fn } from "sequelize";
import { sequelize } from "../config/db.js";
import { consoleLog, convertTolocal, logger, successMessage } from "../helper/index.js";
import { getConfiguration, getModuleStatus } from "../utils/index.js";
import { LegDetail, Package, Rank, SponsorTreepath, Treepath, User, UserDetail, UserpvDetail } from "../models/association.js";
class treeService {
    async getGenealogy({ userId, tree, prefix, startPosition, moreTree = false }) {
        prefix  = prefix+'_';
        const { treeDepth, treeWidth, treeIconBased } = await getConfiguration();
        const moduleStatus  = await getModuleStatus({});
        const UserData      = await User.findByPk(userId);
        if(!UserData) return false;

        let query           = "SELECT ";
        query += `${prefix}users.id, ${prefix}users.username, (${prefix}users.user_level - ${UserData.userLevel}) AS userLevel,${prefix}users.position, ${prefix}users.user_rank_id rankId, ${prefix}users.active, ${prefix}users.leg_position legPosition, ${prefix}users.father_id fatherId, ${prefix}users.sponsor_id sponsorId, (${prefix}users.sponsor_level - ${UserData.sponsorLevel}) As sponsorLevel, ${prefix}users.date_of_joining dateOfJoining, treepath1.depth as depth, ${prefix}users.personal_pv, ${prefix}users.group_pv, CONCAT(ANY_VALUE(userDetails.name), ANY_VALUE(userDetails.second_name)) as fullName,${prefix}users.sponsor_index as sponsorIndex`;
        if(moduleStatus.rankStatus) {
            query   += `,ranks.name rankName, ranks.color rankColor, ranks.image rankImage`;
        }

        if(treeIconBased === 'member_pack' && moduleStatus.productStatus){
            query   += `,ANY_VALUE(package.tree_icon) as treeIcon, 0 as treeIconActive`;
        } else if(treeIconBased === 'profile_image'){
            query   += `, ANY_VALUE(userDetails.image) as treeIcon, 0 as treeIconActive`;
        } else if(treeIconBased === 'rank' && moduleStatus.rankStatus){
            query   += `,ANY_VALUE(ranks.image) as treeIcon, 0 as treeIconActive`;
        } else if(treeIconBased === 'member_status'){
            query   += `,1 as treeIconActive`;
        } else {
            query   += `,NULL as treeIcon,0 as treeIconActive`;
        }

        if(moduleStatus.productStatus && !moduleStatus.ecomStatus) {
            query += `,package.id AS packageId,package.name AS packName`;
        }
        if(moduleStatus.ecomStatus) {
            query += `,package.product_id AS packageId,package.model AS packName`;
        } 
       
        if(moduleStatus.mlmPlan == "Binary") {
            query   += `,ANY_VALUE(leg.total_left_count) totalLeftCount, ANY_VALUE(leg.total_right_count) totalRightCount, ANY_VALUE(leg.total_left_carry) totalLeftCarry, ANY_VALUE(leg.total_right_carry) totalRightCarry`;
            if(tree === 'genealogy') {
                query   += ` ,(select 
                                count(*) 
                            from 
                            ${prefix}users as leftchildren 
                            JOIN ${prefix}treepaths AS leftCount ON leftCount.ancestor = leftchildren.id
                            where leftchildren.father_id = ${prefix}users.id AND leftchildren.leg_position = 1
                        ) as leftChildrenCount`; 
                query   += ` ,(select 
                        count(*) 
                    from 
                    ${prefix}users as rightChildren
                    JOIN ${prefix}treepaths AS rightUser ON rightUser.ancestor = rightChildren.id 
                    WHERE rightChildren.father_id = ${prefix}users.id
                    and rightChildren.leg_position = 2
                ) as rightChildrenCount`; 
            }
        } 
        query += `,COUNT(DISTINCTROW(firstLevelChildren.id)) AS firstLevelChildrenCount`;
        if(tree === 'genealogy') {
            query += `,GROUP_CONCAT(DISTINCT CONCAT(uplines.user_level - ${UserData.userLevel}, LPAD(uplines.leg_position, 8, 0)) ORDER BY uplines.user_level SEPARATOR '-') AS treeRank`;
            if(moduleStatus.mlmPlan === "Unilevel") {
                query += `,MAX(uplinesUni.leg_position) AS legPositionRank`;
            }
            query +=  `, (
                select 
                  count(*) 
                from 
                  ${prefix}treepaths as children
                where 
                  ${prefix}users.id = children.ancestor 
                  and children.descendant != children.ancestor
              ) as childrenCount`; 
        } else if(tree === 'sponsorTree') {
            query += `,GROUP_CONCAT(DISTINCT CONCAT(uplines.sponsor_level - ${UserData.sponsorLevel}, LPAD(uplines.leg_position, 8, 0), treepath2.ancestor ) ORDER BY uplines.sponsor_level SEPARATOR '-') AS treeRank, MAX(uplinesMore.sponsor_index) as spIndex`;
        }
        query += ` FROM ${prefix}users`;

        if(tree === 'genealogy') {
            query += ` JOIN ${prefix}treepaths treepath1 ON treepath1.descendant = ${prefix}users.id
            JOIN ${prefix}treepaths treepath2 ON treepath2.descendant = treepath1.descendant 
            JOIN ${prefix}users uplines ON uplines.id = treepath2.ancestor AND uplines.user_level >= :userLevel`;
            if(moduleStatus.mlmPlan === 'Unilevel') {
                query += ` LEFT JOIN ${prefix}users AS uplinesUni ON uplinesUni.id = ${prefix}users.id AND uplinesUni.id != :userId`;
            }
            query += ` LEFT JOIN ${prefix}users AS firstLevelChildren ON ${prefix}users.id = firstLevelChildren.father_id`;
        } else if(tree === 'sponsorTree') {
            query += ` JOIN ${prefix}sponsor_treepaths treepath1 ON treepath1.descendant = ${prefix}users.id
            JOIN ${prefix}sponsor_treepaths treepath2 ON treepath2.descendant = treepath1.descendant 
            JOIN ${prefix}users uplines ON uplines.id = treepath2.ancestor AND uplines.sponsor_level >= :sponsorLevel`;
            query += ` LEFT JOIN ${prefix}users AS firstLevelChildren ON ${prefix}users.id = firstLevelChildren.sponsor_id`;
            query += ` LEFT JOIN ${prefix}users AS uplinesMore ON uplinesMore.id = ${prefix}users.id AND uplinesMore.id != :userId`;

        }
        if(moduleStatus.mlmPlan == "Binary") {
            query += ` LEFT JOIN ${prefix}leg_details AS leg ON leg.user_id = ${prefix}users.id`;
        }
        query += ` LEFT JOIN ( SELECT name, second_name, image, user_id FROM ${prefix}user_details
                    ) AS userDetails ON userDetails.user_id = ${prefix}users.id`;

        if(moduleStatus.productStatus && !moduleStatus.ecomStatus) {
            query += ` LEFT JOIN (
                SELECT id,tree_icon,name FROM ${prefix}packages
            ) AS package ON package.id = ${prefix}users.product_id`;
        }
        if(moduleStatus.ecomStatus) {
            query += ` LEFT JOIN (
                SELECT product_id,model FROM ${prefix}oc_product
            ) AS package ON package.product_id=${prefix}users.oc_product_id`;
        }
        if(moduleStatus.rankStatus) {
            query += ` LEFT JOIN (
                SELECT id,name,image,color FROM ${prefix}ranks
                ) AS ranks ON ranks.id = ${prefix}users.user_rank_id`;
        }
        query += ` WHERE treepath1.depth < :treeDepth `;
        query += ` AND treepath1.ancestor = :userId GROUP BY ${prefix}users.id`;
        if(moduleStatus.mlmPlan === "Unilevel") {
            query += ` HAVING legPositionRank <= :treeWidth OR legPositionRank IS NULL`;
        }
        if(tree === 'sponsorTree') {
            query += ` HAVING spIndex <= :sponsorIndex OR spIndex IS NULL`;
        }
        query += ` ORDER BY treeRank`;

        const data  = await sequelize.query(query, {
            replacements:{
                userId,
                userLevel: UserData.userLevel,
                treeDepth,
                treeWidth,
                sponsorLevel: UserData.sponsorLevel,
                startPosition,
                sponsorIndex: treeWidth
            },
            type: sequelize.QueryTypes.SELECT,
        });
        
        let resultArray     = data;
        if(tree === 'sponsorTree') {
            const userIds           = _.map(data, 'id');
            const childrenQuery     = ` SELECT TP.ancestor as id, COUNT(*) as childrenCount
                                        FROM ${prefix}sponsor_treepaths as TP
                                        WHERE TP.ancestor IN (:userIds)
                                        GROUP BY TP.ancestor`;
    
            const childrenCounts = await sequelize.query(childrenQuery, {
                                        replacements: { userIds: userIds },
                                        type: sequelize.QueryTypes.SELECT
                                    })
                                    
            const updatedChildrenCounts = childrenCounts.map( item => ({
                    id: item.id, childrenCount: parseInt(item.childrenCount) - 1
                }));

            const mergedArray = _.merge(_.keyBy(data, 'id'), _.keyBy(updatedChildrenCounts, 'id'));
            resultArray = _.values(mergedArray);
        }
        return resultArray.map( item => ({
            ...item, username: item.username?.toUpperCase(), dateOfJoining: convertTolocal(item.dateOfJoining)
        }));
    }

    async formatTree({ treeData, parentId, type, tooltipConfig, authUser, subTree }) {
        const treeStructure = treeData.find( (item) => item.id === parseInt(parentId));
        const moduleStatus  = await getModuleStatus({});
        const { treeDepth, treeWidth, treeIconBased, widthCeiling } = await getConfiguration();
        if(moduleStatus.mlmPlan === 'Unilevel'){
            treeData = treeData.filter( item => item.legPosition <= treeWidth);
        }
        const tooltipData   = this.getTooltipData({ tooltipConfig, treeStructure, moduleStatus });
        const { username, ...attributes } = treeStructure;
        if(moduleStatus.rankStatus) {
            delete attributes['rankName'];
            delete attributes['rankColor'];
            delete attributes['rankImage'];
        }
        const propertiesToKeep = ['username', 'id', 'userLevel', 'position', 'legPosition', 'treeIcon', 'leftChildrenCount', 'rightChildrenCount', 'depth', 'childrenCount', 'firstLevelChildrenCount', 'sponsorId', 'fatherId', 'treeIconActive', 'sponsorIndex'];
        for (const prop in treeStructure) {
            if (!propertiesToKeep.includes(prop)) {
                delete attributes[prop];
            }
        }
        attributes.username = username;

        const updatedArray = this.nstedChildren({data:treeData, parentId, moduleStatus, type, tooltipConfig, treeDepth, treeWidth, widthCeiling});
        if(type === 'genealogy') {
            const updatedNewArray = await this.appendNode({ moduleStatus, attributes, updatedArray, treeDepth, treeWidth, authUser, widthCeiling })
            return await successMessage({ data: { username, attributes, tooltipData, isPlaceholder: false, subTree, children: updatedNewArray }});
        } else {
            const updatedNewArray = await this.appendMoreNode({ moduleStatus, attributes, updatedArray, treeDepth, treeWidth, authUser, widthCeiling });
            return await successMessage({ data: { username, attributes, tooltipData, isPlaceholder: false, children: updatedNewArray }});

        }
    }

    nstedChildren({data, parentId, moduleStatus, type, tooltipConfig, treeDepth, treeWidth, widthCeiling}){
        const children = [];
        data.forEach((item) => {
            if ((type === 'genealogy') ? item.fatherId === parseInt(parentId) : item.sponsorId === parseInt(parentId)) {
                const tooltipData =  this.getTooltipData({ tooltipConfig, treeStructure: item, moduleStatus });
                const { username, ...attributes } = item;
                const child = { username, attributes };
                if(moduleStatus.rankStatus) {
                    delete attributes['rankName'];
                    delete attributes['rankColor'];
                    delete attributes['rankImage'];
                }
                const propertiesToKeep = ['username', 'id', 'userLevel', 'position', 'legPosition', 'treeIcon', 'leftChildrenCount', 'rightChildrenCount', 'depth', 'childrenCount', 'sponsorId', 'fatherId', 'firstLevelChildrenCount', 'sponsorIndex'];
                for (const prop in item) {
                    if (!propertiesToKeep.includes(prop)) {
                        delete attributes[prop];
                    }
                }
                attributes.username = username;
                child.tooltipData       = tooltipData;
                child.isPlaceholder     = false;
                const nestedChildren    = this.nstedChildren({data, parentId: item.id, moduleStatus, type, tooltipConfig, treeDepth, treeWidth, widthCeiling});
                children.push(this.nestedExtraNode({moduleStatus, item, nestedChildren, attributes, child, treeDepth, treeWidth, widthCeiling, type}));
               
            }
        });

        return children;
    }
    async getTreeView({ userId, prefix, moduleStatus, tooltipConfig, authUser}) {
        const dynamicIncludes = [{ model : UserDetail,attributes: ["name", "secondName", "image"]}];
        const authUserLevel   = await User.findByPk(authUser);
        if(moduleStatus.mlmPlan === 'Binary') {
            dynamicIncludes.push({ model: LegDetail, attributes:["totalLeftCount", "totalRightCount", "totalRightCarry", "totalLeftCarry"] });
        }
        if(moduleStatus.rankStatus) {
            dynamicIncludes.push({ model: Rank });
        }
        dynamicIncludes.push({model: User, as: 'children', attributes: ["id"]});
        const data  = await User.findByPk( userId, {
            include: [
                { model : UserDetail,attributes: ["name", "secondName", "image"]},
                { 
                    model: User, 
                    as: 'children',
                    include: dynamicIncludes,
                    attributes: [ "username", "id", "userLevel", "personalPv", "groupPv", "dateOfJoining"]
                }
            ],
            attributes: [ "username", "id", "userLevel", "personalPv", "groupPv", "dateOfJoining"]
        });
        let responseData = [];

        data.children.forEach( item => {
            let childrenData = {
                id : item.id,
                title : item.username.toUpperCase(),
                fullName : item.UserDetail?.secondName !== null ? item.UserDetail?.name +' '+ item.UserDetail?.secondName : item.UserDetail?.name,
                profilePic : item.UserDetail?.image || null,
                level : item.userLevel - authUserLevel.userLevel,
                hasChildren : !!item.children.length,
                tooltipData : this.getTooltipDataTreeView({ tooltipConfig, treeStructure: item, moduleStatus }),
                children : []
            }
            
            responseData.push(childrenData)
        });
        return await successMessage({ data: responseData });
    }

    async getDownlineHeader({userId, prefix}) {
        const headerData =  await Treepath.findAll({
            attributes: [
              [fn('MAX', col('depth')), 'maxDepth'],
            ],
            where: {
              ancestor: userId,
            },
            raw:true
        });
        return await successMessage({ data : { totalLevel: headerData[0].maxDepth}});
    }

    async getDownlines({ depth, offset, userId, prefix, pageSize }) {
        let where = { ancestor: userId, 
            descendant: { [Op.ne] : col('ancestor')},
        };
        if(depth) {
            where.depth = depth
        };
        const parentData        = await User.findByPk(userId);
        const downlineData      = await Treepath.findAndCountAll({
            offset,
            limit: pageSize,
            where,
            attributes: ["descendant", "depth"],
            include: [
                { 
                    model: User, 
                    as: "downlines", 
                    attributes: ["id", "username", "userLevel",  [
                        literal(`downlines.user_level - ${parentData.userLevel}`),
                        "childLevel"
                    ],],
                    include: [
                        { model: User, as: "father", attributes:["username", "id"]},
                        { model: User, as: "sponsor", attributes:["username", "id"]},
                        { model: UserDetail, attributes:["name", "secondName", "image"]},
                    ],
                }
            ],
        });
        let data = [];
        downlineData.rows.forEach( item => {
            data.push({
                username : item.downlines.username,
                fullName : item.downlines.UserDetail.secondName !== null ? item.downlines.UserDetail.name +' '+ item.downlines.UserDetail.secondName : item.downlines.UserDetail.name,
                childLevel : item.downlines.dataValues.childLevel,
                placement : item.downlines.father.username,
                sponsor : item.downlines.sponsor.username,
                image : item.downlines.UserDetail.image,
                depth: item.depth
            });
            
        }) 
        const totalCount 	= downlineData.count;
        const totalPages 	= Math.ceil(totalCount / pageSize);
        const currentPage 	= Math.floor(offset / pageSize) + 1;
        const response      = {
            totalCount,
            totalPages,
            currentPage,
            data
        }
        return await successMessage({ data: response });
    }

    async getreferralHeader({userId, prefix}) {
        const headerData =  await SponsorTreepath.findAll({
            attributes: [
              [fn('MAX', col('depth')), 'maxDepth'],
            ],
            where: {
              ancestor: userId,
            },
            raw:true
        });
        return await successMessage({ data : { totalLevel: headerData[0].maxDepth}});
    }

    async getReferrals({ depth, offset, userId, prefix, pageSize}) {
        const parentData        = await User.findByPk(userId);
        let where =  { ancestor: userId, 
            descendant: { [Op.ne] : col('ancestor')}
        };
        if(depth) {
            where.depth = depth
        };
        const downlineData      = await SponsorTreepath.findAndCountAll({
            offset,
            limit: pageSize,
            where,
            attributes: ["descendant", "depth"],
            include: [
                { 
                    model: User, 
                    as: "sponsorDescendantUser", 
                    attributes: ["id", "username", "sponsorLevel",[
                                literal(`sponsorDescendantUser.sponsor_level - ${parentData.sponsorLevel}`),
                                "childLevel"
                            ],],
                    include: [
                        { model: User, as: "father", attributes:["username", "id"]},
                        { model: User, as: "sponsor", attributes:["username", "id","sponsorLevel"]},
                        { model: UserDetail, attributes:["name", "secondName", "image"]},
                    ]
                },
            ],
        });
        let data = [];

        downlineData.rows.forEach( item => {
            data.push({
                username : item.sponsorDescendantUser.username,
                fullName : item.sponsorDescendantUser.UserDetail.secondName !== null ? item.sponsorDescendantUser.UserDetail.name +' '+ item.sponsorDescendantUser.UserDetail.secondName : item.sponsorDescendantUser.UserDetail.name,
                childLevel : item.sponsorDescendantUser.dataValues.childLevel,
                placement : item.sponsorDescendantUser.father.username,
                sponsor : item.sponsorDescendantUser.sponsor.username,
                image : item.sponsorDescendantUser.UserDetail.image,
                depth: item.depth,
            });
        });
        const totalCount 	= downlineData.count;
        const totalPages 	= Math.ceil(totalCount / pageSize);
        const currentPage 	= Math.floor(offset / pageSize) + 1;
        const response      = {
            totalCount,
            totalPages,
            currentPage,
            data
        }
        return await successMessage({ data: response });
    }

    async getTooltipConfig({prefix}) {
        const query = `SELECT * FROM ${prefix}tooltips_config WHERE status=:status`;
        return await sequelize.query(query, {
            replacements: {
                status : 1
            },
            type: sequelize.QueryTypes.SELECT,
        })
    }

    getTooltipData({ tooltipConfig, treeStructure, moduleStatus })
    {
        const tooltipData = {
            username : treeStructure?.username,
            profilePic : treeStructure?.treeIcon,
            tableData : {}
        }
        const checkFirstName = tooltipConfig.find( item => item.slug === 'first-name'); 
        if(checkFirstName) tooltipData.fullName = treeStructure.fullName;

        const checkJoinDate = tooltipConfig.find( item => item.slug === 'join-date'); 
        if(checkJoinDate) tooltipData.tableData.joinDate = convertTolocal(treeStructure.dateOfJoining);

        const checkPersonalPv = tooltipConfig.find( item => item.slug === 'personal-pv'); 
        if(checkPersonalPv) tooltipData.tableData.personalPv = treeStructure.personal_pv;

        const checkGroupPv = tooltipConfig.find( item => item.slug === 'group-pv'); 
        if(checkGroupPv) tooltipData.tableData.groupPv = treeStructure.group_pv;

        const checkLeft = tooltipConfig.find( item => item.slug === 'left'); 
        if(checkLeft) tooltipData.tableData.left = treeStructure.totalLeftCount;

        const checkRight = tooltipConfig.find( item => item.slug === 'right'); 
        if(checkRight) tooltipData.tableData.right = treeStructure.totalRightCount;

        const checkLeftCarry = tooltipConfig.find( item => item.slug === 'left-carry'); 
        if(checkLeftCarry) tooltipData.tableData.totalLeftCarry = treeStructure.totalLeftCarry;

        const checkRightCarry = tooltipConfig.find( item => item.slug === 'right-carry'); 
        if(checkRightCarry) tooltipData.tableData.totalRightCarry = treeStructure.totalRightCarry;

        const checkRank = tooltipConfig.find( item => item.slug === 'rank-status'); 
        if(checkRank) {
            tooltipData.rankDetails = {
                name : treeStructure.rankName,
                color: treeStructure.rankColor,
                image: treeStructure.rankImage
            }

        }
        return tooltipData;
    }

    getTooltipDataTreeView({ tooltipConfig, treeStructure, moduleStatus }){
        const tooltipData = {
            username : treeStructure.username,
            profilePic : treeStructure.UserDetail?.image,
            tableData : {}
        }
        const checkFirstName = tooltipConfig.find( item => item.slug === 'first-name'); 
        if(checkFirstName) tooltipData.fullName = treeStructure.UserDetail?.name + treeStructure.UserDetail?.secondName;

        const checkJoinDate = tooltipConfig.find( item => item.slug === 'join-date'); 
        if(checkJoinDate) tooltipData.tableData.joinDate = convertTolocal(treeStructure.dateOfJoining);

        const checkPersonalPv = tooltipConfig.find( item => item.slug === 'personal-pv'); 
        if(checkPersonalPv) tooltipData.tableData.personalPv = treeStructure.personalPv;

        const checkGroupPv = tooltipConfig.find( item => item.slug === 'group-pv'); 
        if(checkGroupPv) tooltipData.tableData.groupPv = treeStructure.groupPv;

        const checkLeft = tooltipConfig.find( item => item.slug === 'left'); 
        if(checkLeft) tooltipData.tableData.left = treeStructure.LegDetail?.totalLeftCount;

        const checkRight = tooltipConfig.find( item => item.slug === 'right'); 
        if(checkRight) tooltipData.tableData.right = treeStructure.LegDetail?.totalRightCount;

        const checkLeftCarry = tooltipConfig.find( item => item.slug === 'left-carry'); 
        if(checkLeftCarry) tooltipData.tableData.totalLeftCarry = treeStructure.LegDetail?.totalLeftCarry;

        const checkRightCarry = tooltipConfig.find( item => item.slug === 'right-carry'); 
        if(checkRightCarry) tooltipData.tableData.totalRightCarry = treeStructure.LegDetail?.totalRightCarry;

        const checkRank = tooltipConfig.find( item => item.slug === 'rank-status'); 
        if(checkRank && moduleStatus.rankStatus) {
            tooltipData.rankDetails = {
                name : treeStructure.Rank?.name,
                color: treeStructure.Rank?.color,
                image: treeStructure.Rank?.image
            }

        }
        return tooltipData;
    }

    async checkUser({ authUser, userId }) { 
        return await Treepath.findOne({ where: { descendant: userId, ancestor: authUser }});
    }

    appendNode({ moduleStatus, attributes, updatedArray, treeDepth, treeWidth, authUser, widthCeiling}) {
        if(moduleStatus.mlmPlan === 'Binary') {
            if(attributes.depth < (treeDepth -1) && updatedArray.length < 2){
                if(!updatedArray.length) {
                    updatedArray.push(
                        {
                            username: null,
                            attributes: {
                                parent : attributes.username,
                                userLevel : attributes.userLevel +1,
                                position : 'L',
                                legPosition : 1,
                            },
                            tooltipData : {},
                            isPlaceholder : true,
                            children : []
                        },
                        {
                            username: null,
                            attributes: {
                                parent : attributes.username,
                                userLevel : attributes.userLevel +1,
                                position : 'R',
                                legPosition : 2,
                            },
                            tooltipData : {},
                            isPlaceholder : true,
                            children : []
                        }
                    );
                } else {
                    updatedArray.push(
                        {
                            username: 'Add User',
                            attributes: {
                                parent : attributes.username,
                                parentId : attributes.id,
                                userLevel : attributes.userLevel +1,
                                position : (updatedArray[0].attributes.legPosition == 1) ? 'R' : 'L', // R : L
                                legPosition : (updatedArray[0].attributes.legPosition == 1) ? 2 : 1, // 2 : 1
                            },
                            tooltipData : {},
                            isPlaceholder : true,
                            children : []
                        }
                    );
                }
            }
        }
        else if(moduleStatus.mlmPlan === 'Unilevel') {
            if(attributes.firstLevelChildrenCount > treeWidth) {
                updatedArray.push(
                    {
                        username: 'More',
                        attributes: {
                            fatherId : attributes.id,
                            userLevel : attributes.userLevel,
                            position : updatedArray.length  +1,
                            legPosition : updatedArray.length  +1,
                            moreChildren : attributes.firstLevelChildrenCount - treeWidth
                        },
                        tooltipData : {},
                        isPlaceholder : true,
                        isMore : true,
                        children : []
                    }
                );
            }
            if(parseInt(authUser) === parseInt(attributes.id)) {
                updatedArray.push(
                    {
                        username: 'Add User',
                        attributes: {
                            parent : attributes.username,
                            parentId : attributes.id,
                            userLevel : attributes.userLevel,
                            position : attributes.firstLevelChildrenCount  +1,
                            legPosition : attributes.firstLevelChildrenCount  +1,
                        },
                        tooltipData : {},
                        isPlaceholder : true,
                        children : []
                    }
                );
            }
        } else if(moduleStatus.mlmPlan === 'Matrix') {
            if(widthCeiling > attributes.firstLevelChildrenCount) {
                updatedArray.push(
                    {
                        username: 'Add User',
                        attributes: {
                            parent : attributes.username,
                            parentId : attributes.id,
                            userLevel : attributes.userLevel,
                            position : updatedArray.length  +1,
                            legPosition : updatedArray.length  +1,
                        },
                        tooltipData : {},
                        isPlaceholder : true,
                        children : []
                    }
                );
            }
        }
        return updatedArray.sort( (first, second) => first.attributes.legPosition - second.attributes.legPosition);
    }

    nestedExtraNode({moduleStatus, item, nestedChildren, attributes, child, treeDepth, treeWidth, widthCeiling, type}) {
        if(type === 'genealogy') {
            if(moduleStatus.mlmPlan === 'Binary') {
                if (nestedChildren.length) {
                    if(item.depth < (treeDepth -1) && nestedChildren.length < 2) {
                        nestedChildren.push({
                            username: null,
                            attributes: {
                                parent : attributes.username,
                                parentId: attributes.id,
                                position : (nestedChildren[0].attributes.legPosition === 1) ? 'R' : 'L',
                                legPosition : nestedChildren[0].attributes.legPosition === 1 ? 2 : 1,
                            },
                            tooltipData : {},
                            isPlaceholder : true,
                            children : []
                        });
                    }
                    child.children = nestedChildren.sort( (first, second) => first.attributes.legPosition - second.attributes.legPosition);
                } else {
                    if (child.attributes.depth < treeDepth -1) {
                        child.children = [
                            {
                                username: null,
                                attributes: {
                                    parent : attributes.username,
                                    parentId: attributes.id,
                                    userLevel : attributes.userLevel +1,
                                    position : 'L',
                                    legPosition : 1,
                                },
                                tooltipData : {},
                                isPlaceholder : true,
                                children : []
                            },
                            {
                                username: null,
                                attributes: {
                                    parent : attributes.username,
                                    parentId: attributes.id,
                                    userLevel : attributes.userLevel +1,
                                    position : 'R',
                                    legPosition : 2,
                                },
                                tooltipData : {},
                                isPlaceholder : true,
                                children : []
                            }
                        ];
                    } else {
                        child.children = [];
                    }
                }
            } else if(moduleStatus.mlmPlan === 'Unilevel' && item.depth < (treeDepth -1)) {
                if(attributes.firstLevelChildrenCount > treeWidth) {
                    nestedChildren.push(
                        {
                            username: 'More',
                            attributes: {
                                fatherId : attributes.id,
                                userLevel : attributes.userLevel,
                                position : nestedChildren.length  +1,
                                legPosition : nestedChildren.length  +1,
                                moreChildren : attributes.firstLevelChildrenCount - treeWidth
                            },
                            tooltipData : {},
                            isPlaceholder : true,
                            isMore : true,
                            children : []
                        }
                    );
                }
                child.children = nestedChildren;
            } else if(moduleStatus.mlmPlan === 'Matrix') {
                if(item.depth < (treeDepth -1)){
                    if (item.firstLevelChildrenCount < widthCeiling) {
                        nestedChildren.push({
                            username: null,
                            attributes: {
                                parent : attributes.username,
                                parentId: attributes.id,
                                userLevel : attributes.userLevel +1,
                                position : attributes.childrenCount + 1,
                                legPosition : attributes.childrenCount +1,
                            },
                            tooltipData : {},
                            isPlaceholder : true,
                            children : []
                        });
                    } else {
                        child.children = nestedChildren;
                    }
                } else {
                    child.children = [];
                }
                child.children = nestedChildren.sort( (first, second) => first.attributes.legPosition - second.attributes.legPosition);
            }
        } else {
            if(item.depth < (treeDepth -1) && attributes.firstLevelChildrenCount > parseInt(treeWidth)) {
                nestedChildren.push(
                    {
                        username: 'More',
                        attributes: {
                            fatherId : attributes.id,
                            sponsorId : attributes.id,
                            userLevel : attributes.userLevel,
                            position : nestedChildren.length  + 1,
                            legPosition : nestedChildren.length  + 1,
                            moreChildren : attributes.firstLevelChildrenCount - nestedChildren.length
                        },
                        tooltipData : {},
                        isPlaceholder : true,
                        isMore : true,
                        children : []
                    }
                );
            }
            child.children = nestedChildren;
        }
        return child;
    }

    async getNextChildren({ userId, tree, prefix, startPosition, moreTree}) {
        const { treeWidth } = await getConfiguration();

        const children = await User.findAndCountAll({
            attributes: ["id", "username", "fatherId", "position", "legPosition"],
            where: {
                fatherId : userId, 
                legPosition: {
                    [Op.gte]: startPosition,
                    [Op.lt] : parseInt(startPosition) + treeWidth
                } 
            },
        });
        return children;
    }

    async formatTreeMore({ treeData, fatherId, type, tooltipConfig, authUser, childrenCount }) {
        let moreFirstLevel  = treeData.filter( (item) => parseInt(item.fatherId) === parseInt(fatherId));
        const moduleStatus  = await getModuleStatus({});
        const children      = [];
        const { treeDepth, treeWidth, treeIconBased, widthCeiling } = await getConfiguration();
        for( const treeStructure of moreFirstLevel) {
            const tooltipData   = this.getTooltipData({ tooltipConfig, treeStructure, moduleStatus });
            const { username, ...attributes } = treeStructure;
            if(moduleStatus.rankStatus) {
                delete attributes['rankName'];
                delete attributes['rankColor'];
                delete attributes['rankImage'];
            }
            const propertiesToKeep = ['username', 'id', 'userLevel', 'position', 'legPosition', 'treeIcon', 'leftChildrenCount', 'rightChildrenCount', 'depth', 'childrenCount', 'firstLevelChildrenCount', 'sponsorId', 'fatherId'];
            for (const prop in treeStructure) {
                if (!propertiesToKeep.includes(prop)) {
                    delete attributes[prop];
                }
            }
            let currentChild = {
                username, 
                attributes, 
                isPlaceholder: false, 
                tooltipData
            };
            currentChild.children = this.nstedChildren({data:treeData, parentId:treeStructure.id, moduleStatus, type, tooltipConfig, treeDepth, treeWidth, widthCeiling});
            if((parseInt(treeStructure.firstLevelChildrenCount) > parseInt(currentChild.children.length))) {
                currentChild.children.push( {
                    username: 'More',
                    attributes: {
                        fatherId : treeStructure.id,
                        userLevel : treeStructure.userLevel,
                        position : parseInt(currentChild.children.length)  +1,
                        legPosition : parseInt(currentChild.children.length)  +1,
                        moreChildren : parseInt(treeStructure.firstLevelChildrenCount) - treeWidth
                    },
                    tooltipData : {},
                    isPlaceholder : true,
                    isMore : true,
                    children : []
                });
            }
            children.push(currentChild)
        }
        const maxLegPosition = _.maxBy(children, entry => entry.attributes?.legPosition)?.attributes?.legPosition || 0;
        if(parseInt(childrenCount.count) > parseInt(maxLegPosition)){
            children.push({
                username: 'More',
                attributes: {
                    fatherId,
                    userLevel : childrenCount.userLevel,
                    position : parseInt(maxLegPosition) + 1,
                    legPosition : parseInt(maxLegPosition)  + 1,
                    moreChildren : parseInt(childrenCount.count) - parseInt(maxLegPosition)
                },
                tooltipData : {},
                isPlaceholder : true,
                isMore : true,
                children : []
            });
        }
        return await successMessage({ data: children });
    }

    async getFatherChildrenCount({userId, prefix}) {
        const children = await User.findAndCountAll({
            attributes: ["id", "username", "fatherId", "position", "legPosition"],
            where: {
                fatherId : userId, 
            },
        });
        return children;
    }

    async checkUserInSponsor({ authUser, userId }) { 
        return await SponsorTreepath.findOne({ where: { descendant: userId, ancestor: authUser }});
    }

    async appendMoreNode({ moduleStatus, attributes, updatedArray, treeDepth, treeWidth, authUser, widthCeiling }) {
        if(parseInt(attributes.firstLevelChildrenCount) > parseInt(treeWidth)) {
            updatedArray.push(
                {
                    username: 'More',
                    attributes: {
                        fatherId : attributes.id,
                        sponsorId : attributes.id,
                        userLevel : attributes.userLevel,
                        position : updatedArray.length  +1,
                        legPosition : updatedArray.length  +1,
                        moreChildren : attributes.firstLevelChildrenCount - parseInt(treeWidth)
                    },
                    tooltipData : {},
                    isPlaceholder : true,
                    isMore : true,
                    children : []
                }
            );
        }
        return updatedArray;
    }

    async checkUserSponsorTree({ authUser, userId }) { 
        return await SponsorTreepath.findOne({ where: { descendant: userId, ancestor: authUser }});
    }

    async getNextSponsorTreeChildren({ userId, tree, prefix, startPosition, moreTree}) {
        const { treeWidth } = await getConfiguration();

        const children = await User.findAndCountAll({
            attributes: ["id", "username", "sponsorId", "position", "sponsorIndex"],
            where: {
                sponsorId : userId, 
                sponsorIndex: {
                    [Op.gte]: startPosition,
                    [Op.lt] : parseInt(startPosition) + treeWidth
                } 
            },
            include: [
                {
                    model: SponsorTreepath,
                    as: "sponsorDescendant",
                    where: { depth: {
                        [Op.lte]: 1
                    }},
                }
            ]
        });
        return children;
    }

    async getSponsorChildrenCount({userId, prefix}) {
        const children = await User.findAndCountAll({
            attributes: ["id", "username", "sponsorId", "position", "sponsorIndex"],
            where: {
                sponsorId : userId, 
            },
        });
        return children;
    }

    async formatSponsorTreeMore({ treeData, sponsorId, type, tooltipConfig, authUser, childrenCount }) {
        let moreFirstLevel  = treeData.filter( (item) => parseInt(item.sponsorId) === parseInt(sponsorId));
        const moduleStatus  = await getModuleStatus({});
        const children      = [];
        const { treeDepth, treeWidth, treeIconBased, widthCeiling } = await getConfiguration();
        for( const treeStructure of moreFirstLevel) {
            const tooltipData   = this.getTooltipData({ tooltipConfig, treeStructure, moduleStatus });
            const { username, ...attributes } = treeStructure;
            if(moduleStatus.rankStatus) {
                delete attributes['rankName'];
                delete attributes['rankColor'];
                delete attributes['rankImage'];
            }
            const propertiesToKeep = ['username', 'id', 'userLevel', 'position', 'legPosition', 'treeIcon', 'leftChildrenCount', 'rightChildrenCount', 'depth', 'childrenCount', 'firstLevelChildrenCount', 'sponsorId', 'fatherId','sponsorIndex'];
            for (const prop in treeStructure) {
                if (!propertiesToKeep.includes(prop)) {
                    delete attributes[prop];
                }
            }
            let currentChild = {
                username, 
                attributes, 
                isPlaceholder: false, 
                tooltipData
            };
            currentChild.children = this.nstedChildren({data:treeData, parentId:treeStructure.id, moduleStatus, type, tooltipConfig, treeDepth, treeWidth, widthCeiling});
            if(parseInt(currentChild.attributes.firstLevelChildrenCount) > parseInt(treeWidth) && treeStructure.depth < (treeDepth -1)){
                const maxLegPosition = _.maxBy(currentChild.children, entry => entry.attributes?.sponsorIndex)?.attributes?.sponsorIndex || 0;

                currentChild.children.push({
                    username: 'More',
                    attributes: {
                        fatherId: currentChild.attributes.id, // in react fatherId is the parent node key
                        sponsorId: currentChild.attributes.id,
                        userLevel : childrenCount.userLevel,
                        position : parseInt(maxLegPosition) + 1,
                        legPosition : parseInt(maxLegPosition)  + 1,
                        moreChildren : parseInt(currentChild.attributes.firstLevelChildrenCount) - parseInt(maxLegPosition)

                    },
                    tooltipData : {},
                    isPlaceholder : true,
                    isMore : true,
                    children : []
                });
            }
            children.push(currentChild);
        }
        const maxLegPosition = _.maxBy(treeData, entry => entry.sponsorIndex).sponsorIndex || 0;
        if(parseInt(childrenCount.count) - parseInt(maxLegPosition) > 0 && childrenCount.count > parseInt(treeWidth) ) {
            children.push({
                username: 'More',
                attributes: {
                    fatherId: sponsorId, // in react fatherId is the parent node key
                    sponsorId: sponsorId,
                    userLevel : childrenCount.userLevel,
                    position : parseInt(maxLegPosition) + 1,
                    legPosition : parseInt(maxLegPosition)  + 1,
                    moreChildren : parseInt(childrenCount.count) - parseInt(maxLegPosition)

                },
                tooltipData : {},
                isPlaceholder : true,
                isMore : true,
                children : []
            });
        }
        return await successMessage({ data: children });
    }
}

export default new treeService();
