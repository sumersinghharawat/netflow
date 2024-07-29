import { Op } from "sequelize";
import { consoleLog, successMessage } from "../helper/index.js";
import { getModuleStatus } from "../utils/index.js";
import Language from "../models/language.js";
import { CompanyProfile, CurrencyDetail, Menu, MenuPermission, User, UserDetail } from "../models/association.js";

class appService {
    async getEnabledMenu() {
        return await Menu.findAll( {
            where : { 
                react: 1 , adminOnly :0, isChild: 0,
            },
            attributes: [ "id", "title", "slug", "userIcon", "is_child"],
            include: [
                {
                    model: MenuPermission,
                    attributes: ["userPermission"],
                    required: true,
                    where: { userPermission: 1 }
                },
                {
                    model: Menu,
                    as: "subMenu",
                    attributes: [ "id", "title", "slug", "parentId", "userIcon"],
                    include: {
                        model: MenuPermission,
                        attributes: ["userPermission"],
                        required: true,
                        where: { userPermission: 1 }
                    },
                }
            ],
            order: ["order"],
        });
    }

    async getCompanyAndLang () {
        let currencies, languages   = [];
        const moduleStatus      = await getModuleStatus({attributes:["multiCurrencyStatus","multilangStatus"]});
        const currencyStatus    = moduleStatus.multiCurrencyStatus;
        const languageStatus    = moduleStatus.multilangStatus;
        if(currencyStatus) {
            currencies = await CurrencyDetail.findAll(
                { 
                    attributes: ['id', 'title', 'code', 'value', 'symbolLeft', 'symbolRight', 'default'],
                    where: { status: 1}
                }
            );
        }
        if(languageStatus) {
            languages = await Language.findAll(
                { 
                    attributes: ['id', 'nameInEnglish', 'name', 'code','status', 'default', 'flagImage'],
                    where: { status: 1}
                }
            );
        }

        const companyProfile   = await CompanyProfile.findOne({ attributes: ['name', 'logo', 'address', 'favicon']});

        return { companyProfile, languages, currencies }
    }

    async getUserData(userId) {
        const user = await User.findByPk(userId,{
            attributes: ["id", "username", 'userType', 'email', 'emailVerified', 'defaultCurrency', 'defaultLang'],
            include: [
                { model: UserDetail, attributes: [ "image" ] },
                { model: CurrencyDetail, attributes: ['id', 'code', 'symbolLeft', 'value']},
                { model: Language, attributes: ['id', 'code', 'name', 'nameInEnglish', 'flagImage']},
            ],
        });
        return {
            id: user.id,
            username: user.username,
            userType: user.userType,
            email: user.email,
            emailVerified: user.emailVerified,
            defaultCurrency: user.CurrencyDetail,
            defaultLang: user.Language,
            image: user.UserDetail.image,       
        }
    }
}

export default new appService;