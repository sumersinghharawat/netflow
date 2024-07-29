import { consoleLog, successMessage } from "../../helper/index.js";
import { sideMenu, quickMenu, topMenu } from "../../utils/menus.js";
import { getConfiguration, getModuleStatus } from "../../utils/index.js";
import appService from "../../services/appService.js";
import Notification from "../../models/notification.js";
import dotenv from "dotenv";
import _ from "lodash";
import MailBox from "../../models/mailBox.js";

dotenv.config();

export const appLayout = async (req, res, next) => {
	const authUser = req.auth.user;
    try {
		const [
			enabledMenu, 
			companyProfileAndLang, 
			configData, 
			moduleStatus,
			userData,
			notificationCount,
			mailCount
		] = await Promise.all([
			appService.getEnabledMenu(),
			appService.getCompanyAndLang(),
			getConfiguration(),
			getModuleStatus({}),
			appService.getUserData(authUser.id),
			Notification.count({ where:{ notifiableId: authUser.id, readAt: null }}),
			MailBox.count({where:{ toUserId: authUser.id, inboxDeleteStatus: 0, readStatus: 0, toAll: 0}})
		]);
		let sideMenus     = enabledMenu.map( (menu) => {
			let itemObject = menu.toJSON();
			if(itemObject.slug === 'mail-box') {
				itemObject.slug = 'mailbox';
			}
			if (moduleStatus.ecomStatus) {
				itemObject = (itemObject.slug === "register" || itemObject.slug === "store" || itemObject.slug === "shopping")
								? { ...itemObject, ecomLink: true}
								: { ...itemObject, ecomLink: false};
			} else {
				itemObject = { ...itemObject, ecomLink: false};
			}
			if(sideMenu.includes(itemObject.slug)){
				return { ...itemObject, isMain: true};
			}
			return itemObject;
		});
		if(userData.defaultLang) {
			const flag = userData.defaultLang.flagImage.split(".")[0]+".svg";
			userData.defaultLang.flagImage = "images/"+flag;
		}
        const topMenus      = enabledMenu.filter( (menu) => topMenu.includes(menu.slug));
        let quickMenus    	= enabledMenu.filter( (menu) => quickMenu.includes(menu.slug));
		const toolsMenu 	= quickMenus.find( item => item.slug === "tools")?.subMenu;
		if (quickMenus.length === 4) {
			quickMenus = quickMenus.filter( item => item.slug !== "tools");
			quickMenus.push(...toolsMenu.filter( item => ["news", "faqs", "download-materials", "promotion-tools"].includes(item.slug)));
		} else if( quickMenus.length === 3) {
			quickMenus = quickMenus.filter( item => item.slug !== "tools");
			quickMenus.push(...toolsMenu.filter( item => ["news", "faqs", "download-materials", "promotion-tools","leads"].includes(item.slug)));
		} else if( quickMenus.length === 2) {
			quickMenus = quickMenus.filter( item => item.slug !== "tools");
			quickMenus.push(...toolsMenu.filter( item => ["news", "faqs", "download-materials", "promotion-tools","leads"].includes(item.slug)));
		} else if(quickMenus.length === 1) {
			quickMenus = [];
			quickMenus.push(...toolsMenu);
		}

		let spclMenu;
		spclMenu = enabledMenu.find( (menu) => menu.slug == 'crm');
        spclMenu = enabledMenu.find( (menu) => menu.slug == 'support-center');
		// if(!moduleStatus.ecomStatus) {
			spclMenu = enabledMenu.find( (menu) => menu.slug == 'shopping');
		// } else {
			quickMenus = quickMenus.filter( (menu) => menu.slug !== 'shopping');
			// sideMenus  = sideMenus.filter( (menu) => menu.slug !== 'shopping');
		// }

		if(!spclMenu){
			spclMenu = null;
		}
        quickMenus = (spclMenu) ? quickMenus.filter( (menu) => menu.slug !== spclMenu.slug) : quickMenus;
		spclMenu = spclMenu ? spclMenu.toJSON() : null;
		if(spclMenu) {
			spclMenu.ecomLink = (moduleStatus.ecomStatus && spclMenu.slug === 'shopping') 
							? true
							: false;
		}

		quickMenus = quickMenus.map(menu => {
			const newQuick = { ...menu.toJSON() };
			if(menu.slug === "crm") newQuick.quickIcon = "fa-solid fa-users";
			if(menu.slug === "support-center") newQuick.quickIcon = "fa-solid fa-headset";
			if(menu.slug === "download-materials") newQuick.quickIcon = "fa fa-download";
			if(menu.slug === "news") newQuick.quickIcon = "fa-regular fa-newspaper";
			if(menu.slug === "faqs") newQuick.quickIcon = "fa-solid fa-question";
			if(menu.slug === "promotion-tools") newQuick.quickIcon = "fa-solid fa-bullhorn";
			if(menu.slug === "shopping") newQuick.quickIcon = "fa-solid fa-cart-shopping";
			if(menu.slug === "leads") newQuick.quickIcon = "fa fa-download";
			return newQuick;
        });
	
		const configFields = [
			"tds", "serviceCharge", "pairValue", "regAmount", "maxPinCount", "transFee", "roiPeriod", "roiDaysSkip",
			"apiKey", "treeIconBased", "treeDepth", "treeWidth"
		];
		const { ...filteredConfigData } = configData;

		for (const field in filteredConfigData) {
			if (!configFields.includes(field)) {
				delete filteredConfigData[field];
			}
		}
		companyProfileAndLang.languages.forEach( lang => {
			lang.flagImage = "images/"+lang.flagImage.split(".")[0]+".svg";
		});
		
        let responseData  = {
			companyProfile : companyProfileAndLang.companyProfile,
			languages : companyProfileAndLang.languages,
			currencies : companyProfileAndLang.currencies,
			menu: { sideMenus, topMenus, spclMenu, quickMenus}, 
			mailCount : mailCount,
			notificationCount : notificationCount,
			configuration : filteredConfigData,
			moduleStatus : {
				mlm_plan : moduleStatus.mlmPlan,
				pin_status : moduleStatus.pinStatus,
				product_status : moduleStatus.productStatus,
				mailbox_status : moduleStatus.mailboxStatus,
				payout_release_status : moduleStatus.payoutReleaseStatus,
				rank_status : moduleStatus.rankStatus,
				captcha_status : moduleStatus.captchaStatus,
				multi_currency_status : moduleStatus.multiCurrencyStatus,
				lead_capture_status : moduleStatus.leadCaptureStatus,
				ticket_system_status : moduleStatus.ticketSystemStatus,
				ecom_status : moduleStatus.ecomStatus,
				autoresponder_status : moduleStatus.autoresponderStatus,
				lcp_type : moduleStatus.lcpType,
				payment_gateway_status : moduleStatus.paymentGatewayStatus,
				repurchase_status : moduleStatus.repurchaseStatus,
				google_auth_status : moduleStatus.googleAuthStatus,
				package_upgrade : moduleStatus.packageUpgrade,
				roi_status : moduleStatus.roiStatus,
				xup_status : moduleStatus.xupStatus,
				hyip_status : moduleStatus.hyipStatus,
				kyc_status : moduleStatus.kycStatus,
				subscription_status : moduleStatus.subscriptionStatus,
				promotion_status : moduleStatus.promotionStatus,
				multilang_status : moduleStatus.multilangStatus,
				replicated_site_status : moduleStatus.replicatedSiteStatus,
				purchase_wallet : moduleStatus.purchaseWallet
			},
			user: userData
		};
		const response =  await successMessage({ data : responseData });
		return res.status(response.code).json(response.data);
	} catch (error) {
		return next(error);
	}
};