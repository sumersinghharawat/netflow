import { consoleLog, errorMessage } from "../../helper/index.js";
import { getModuleStatus, getCompensation, usernameToid } from "../../utils/index.js";
import treeService from "../../services/treeService.js";

export const getGenealogy = async (req, res, next) => {
    try {
        let data          = { userId: req.query.userId ? req.query.userId : req.auth.user.id, tree: "genealogy", prefix: req.prefix};
        let isSubTree     = req.query.userId ? (parseInt(req.query.userId) === parseInt(req.auth.user.id)) ? false : true : false;
        const serachUser  = req.query.username ? req.query.username : false;
        if(serachUser) {
            const searchUserId = await usernameToid(serachUser);
            if(!searchUserId || !await treeService.checkUser({authUser: req.auth.user.id, userId: searchUserId.id})) {
                const response = await errorMessage({ code: 1085, statusCode: 422});
                return res.status(response.code).json(response.data);
            }
            data.userId = searchUserId.id;
            isSubTree = true;
        }

        if(req.query.userId && !await treeService.checkUser({authUser: req.auth.user.id, userId: req.query.userId})){
            const response = await errorMessage({ code: 1089, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const [tooltipConfig, treeData ] = await Promise.all([
            treeService.getTooltipConfig({ prefix : req.prefix+"_"}),
            treeService.getGenealogy(data)
        ]);

        if(!treeData.length){
            const response = await errorMessage({ code: 1043, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const response  = await treeService.formatTree({ treeData, parentId: data.userId, type: "genealogy", tooltipConfig, authUser:req.auth.user.id, subTree: isSubTree });
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);
    }
};

export const getSponsorTree = async (req, res, next) => {
    let data          = { userId: req.query.userId ? req.query.userId : req.auth.user.id, tree: "sponsorTree", prefix: req.prefix};
    const moduleStatus  = await getModuleStatus({attributes:["mlmPlan"]});
    if(moduleStatus.mlmPlan === "Unilevel") {
        const response = await errorMessage({ code: 1091, statusCode: 422});
            return res.status(response.code).json(response.data);
    }
    const serachUser    = req.query.username ? req.query.username : false;
    if(serachUser) {
        const searchUserId = await usernameToid(serachUser);
        if(!searchUserId || !await treeService.checkUserInSponsor({authUser: req.auth.user.id, userId: searchUserId.id})) {
            const response = await errorMessage({ code: 1085, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        data.userId = searchUserId.id;
    }
    if(req.query.userId && !await treeService.checkUserInSponsor({authUser: req.auth.user.id, userId: req.query.userId})){
        const response = await errorMessage({ code: 1089, statusCode: 422});
        return res.status(response.code).json(response.data);
    }
    const [tooltipConfig, treeData] = await Promise.all([
        treeService.getTooltipConfig({ prefix : req.prefix+"_"}),
        treeService.getGenealogy(data)
    ]);
    const response  = await treeService.formatTree({treeData, parentId: data.userId, type: "sponsorTree", tooltipConfig, authUser:req.auth.user.id});
    return res.status(response.code).json(response.data);
};

export const getTreeView = async (req, res, next) => {
    try {
        const moduleStatus              = await getModuleStatus({attributes:["mlmPlan","rankStatus"]});
        const prefix                    = req.prefix+"_";
        const userId                    = (req.query.userId && req.query.userId != '') 
                                            ? req.query.userId
                                            : req.auth.user.id;
        const authUser                  = req.auth.user.id;
        if(!await treeService.checkUser({ authUser, userId })){
            const response = await errorMessage({ code: 1085, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const tooltipConfig             = await treeService.getTooltipConfig({ prefix});
        const response                  = await treeService.getTreeView({ userId, prefix, moduleStatus, tooltipConfig, authUser:req.auth.user.id});    
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);
    }
};

export const getDownlineHeader = async (req, res, next) => {
    const prefix    = req.prefix+"_";
    const userId    = req.auth.user.id;
    const response  = await treeService.getDownlineHeader({userId, prefix});
    return res.status(response.code).json(response.data);
};

export const getDownlines = async (req, res, next) => {
    let { level = "all" } = req.query;
    const depth = level === "all" ? null : level === "" ? null : parseInt(level);
    const page 		= parseInt(req.query.page) || 1;
    const pageSize 	= parseInt(req.query.perPage) || 10;
    const offset 	= (page - 1) * pageSize;
    const prefix    = req.prefix+"_";
    const userId    = req.auth.user.id;
    const response  = await treeService.getDownlines({ depth, offset, userId, prefix, pageSize});
    return res.status(response.code).json(response.data);
};

export const getReferralHeader = async (req, res, next) => {
    const prefix    = req.prefix+"_";
    const userId    = req.auth.user.id;
    const response  = await treeService.getreferralHeader({userId, prefix});
    return res.status(response.code).json(response.data);
};

export const getReferrals = async (req, res, next) => {
    let { level = "all" } = req.query;
    const depth = level === "all" ? null : level === "" ? null : parseInt(level);
    const page 		= parseInt(req.query.page) || 1;
    const pageSize 	= parseInt(req.query.perPage) || 10;
    const offset 	= (page - 1) * pageSize;
    const prefix    = req.prefix+"_";
    const userId    = req.auth.user.id;
    const response  = await treeService.getReferrals({ depth, offset, userId, prefix, pageSize});
    return res.status(response.code).json(response.data);
};

export const getUnilevelMore = async (req, res, next) => {
    try {
        const queryLen      = Object.keys(req.query).length === 2;          
        if(!queryLen) {
            const response = await errorMessage({ code: 1089, statusCode: 422});
            return res.status(response.code).json(response.data);
        }

        if(req.query.userId && !await treeService.checkUser({authUser: req.auth.user.id, userId: req.query.fatherId})){
            const response = await errorMessage({ code: 1089, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const data          = { 
            userId: req.query.fatherId, 
            tree: "genealogy", 
            prefix: req.prefix,
            startPosition: req.query.position,
            moreTree : true,
        };
        const [ tooltipConfig, nextFirstLevelChildren, fatherChildrenCount ] = await Promise.all([
            treeService.getTooltipConfig({ prefix : req.prefix+"_"}),
            treeService.getNextChildren(data),
            treeService.getFatherChildrenCount(data),
        ]);

        if(!nextFirstLevelChildren.count) {
            const response = await errorMessage({ code: 1095, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const children = [];

        for(const nextChild of nextFirstLevelChildren.rows) {
            let childData  = { 
                userId: nextChild.id, 
                tree: "genealogy", 
                prefix: req.prefix,
                startPosition: req.query.position,
                moreTree : true,
            };
            children.push(... await treeService.getGenealogy(childData));   
        }

        if(!children.length){
            const response = await errorMessage({ code: 1095, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const response  = await treeService.formatTreeMore({ treeData: children, fatherId:parseInt(data.userId), type: "genealogy", tooltipConfig, authUser:req.auth.user.id, childrenCount: fatherChildrenCount });
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);
    }
};

export const getSponsorMore = async (req, res, next) => {
    try {
        const queryLen      = Object.keys(req.query).length === 2;          
        if(!queryLen) {
            const response = await errorMessage({ code: 1043, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        if(!req.query.position){
            const response = await errorMessage({ code: 1126, statusCode: 422});
            return res.status(response.code).json(response.data);
        }

        if(!await treeService.checkUserSponsorTree({authUser: req.auth.user.id, userId: req.query.sponsorId})){
            const response = await errorMessage({ code: 1089, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const data          = { 
            userId: req.query.sponsorId, 
            tree: "sponsorTree", 
            prefix: req.prefix,
            startPosition: req.query.position,
            moreTree : true,
        };
        const [ tooltipConfig, nextFirstLevelChildren, sponsorChildrenCount ] = await Promise.all([
            treeService.getTooltipConfig({ prefix : req.prefix+"_"}),
            treeService.getNextSponsorTreeChildren(data),
            treeService.getSponsorChildrenCount(data),
        ]);
        if(!nextFirstLevelChildren.count) {
            const response = await errorMessage({ code: 1095, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const children = [];

        for(const nextChild of nextFirstLevelChildren.rows) {
            let childData  = { 
                userId: nextChild.id, 
                tree: "sponsorTree", 
                prefix: req.prefix,
                startPosition: req.query.position,
                moreTree : true,
            };
            children.push(... await treeService.getGenealogy(childData));   
        }

        if(!children.length){
            const response = await errorMessage({ code: 1095, statusCode: 422});
            return res.status(response.code).json(response.data);
        }
        const response  = await treeService.formatSponsorTreeMore({ treeData: children, sponsorId:parseInt(data.userId), type: "sponsorTree", tooltipConfig, authUser:req.auth.user.id, childrenCount: sponsorChildrenCount });
        return res.status(response.code).json(response.data);
    } catch (error) {
        next(error);
    }
};