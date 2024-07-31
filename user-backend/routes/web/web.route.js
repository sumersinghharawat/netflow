import { Router } from "express";
import { validateRequest } from "../../middleware/validateRequest.js";
import auth from "../../middleware/web/auth.middleware.js";

import { currencyUpdateSchema, langUpdateScema } from "../../validators/curencyLangUpdateValidator.js";
import { fundTransferSchema } from "../../validators/fundTransferValidator.js";
import { epinPurchaseSchema, epinRequestSchema, epinTransferSchema } from "../../validators/epinValidator.js";
import { personalDataSchema, contactDataSchema, bankDataSchema } from "../../validators/profileValidator.js";
import { payoutCancelSchema, payoutRequestSchema } from "../../validators/payoutValidator.js";

import { getDashboardUserProfile, getDashboardTiles, getTiles, getGraph, getNotifications, getDashboardDetails, getTopRecruiters, getPackageOverview, getDashboardExpenses, getRankOverview, readNotifications } from "../../controllers/web/homeController.js";

import { appLayout } from "../../controllers/web/appController.js";
import { getCountries, updateCurrency, updateLanguage } from "../../controllers/web/utilityController.js";
import { epinPurchase, epinRequest, epinTransfer, getEpinList, epinRefund, 
    getEpinTransferHistory, getPendingEpinRequest, getEpinTiles, getPurchasedEpinList, getEpinPartials } from "../../controllers/web/epinController.js";
import { checkTransactionPassword, getRegister, registerFieldVerification, registerUser, letterPreview } from "../../controllers/web/registerController.js";
import { getEwalletTiles,ewalletStatement, getPurchaseWallet, getMyEarnings, 
    ewalletTransferHistory, fundTransfer, getEwalletBalance } from "../../controllers/web/ewalletController.js";
import { 
    profileView, updatePersonalData, updateContactDetails, updateBankDetails, 
    updatePaymentDetails, updateSettings, removeAvatar, uploadAvatar, 
    changeUserPassword, changeTransactionPassword, getKYCDetails, kycUpload, updateCustomFieldValue, kycDelete 
} from "../../controllers/web/profileController.js";
import { cancelPayoutRequest, getPayoutDetails, getPayoutRequestDetails, payoutRequest, getPayoutTiles } from "../../controllers/web/payoutController.js";
import { getGenealogy, getSponsorTree, getTreeView, getDownlines, getDownlineHeader, 
    getReferralHeader, getReferrals, getUnilevelMore, getSponsorMore } from "../../controllers/web/treeController.js";
import { checkEpinValidity, getPaymentGatewayKey, getPaymentMethods, removeBankReceipt, uploadBankReceipt } from "../../controllers/web/paymentController.js";
import { getAllNews, getDownloads, getFAQs, getNewsArticle, getLeads, updateLead, searchLead } from "../../controllers/web/toolsController.js";
import transactionPasswordSchema from "../../validators/transactionPasswordValidator.js";
import { deleteMail, getAdminMail, getComposeMailData, getMailInbox, getSentMail, sendInternalMail, replyToMail, viewSingleMailThread, getReplicaContacts, updateAllMail } from "../../controllers/web/mailController.js";
import { composeMailSchema} from "../../validators/composeMailValidator.js";

const router = Router();

// Dashboard
router.get("/dashboard-user-profile", auth, getDashboardUserProfile);
router.get("/dashboard-tiles", auth, getDashboardTiles);
router.get("/dashboard-details", auth, getDashboardDetails);
router.get("/top-recruiters", auth, getTopRecruiters);
router.get("/package-overview", auth, getPackageOverview);
router.get("/rank-overview", auth, getRankOverview);
router.get("/dashboard-expenses", auth, getDashboardExpenses);

router.get("/get-tiles", auth, getTiles);
router.get("/get-graph", auth, getGraph);
router.get("/app-layout", auth, appLayout);
router.patch("/change-currency", auth, validateRequest(currencyUpdateSchema), updateCurrency);
router.patch("/change-language", auth, validateRequest(langUpdateScema), updateLanguage);
router.get("/notifications", auth, getNotifications);
router.post("/notifications-read-all", auth, readNotifications);

// E-wallet
router.get("/ewallet-tiles", auth, getEwalletTiles);
router.get("/ewallet-statement", auth, ewalletStatement);
router.get("/ewallet-transfer-history", auth, ewalletTransferHistory);
router.get("/purchase-wallet", auth, getPurchaseWallet);
router.get("/my-earnings", auth, getMyEarnings);
router.post("/fund-transfer", auth, validateRequest(fundTransferSchema), fundTransfer);
router.get("/get-ewallet-balance", auth, getEwalletBalance);

// E-pin
router.get("/epin-tiles", auth, getEpinTiles);
router.get("/epin-list", auth, getEpinList);
router.get("/epin-partials", auth, getEpinPartials);
router.get("/pending-epin-request", auth, getPendingEpinRequest);
router.get("/epin-transfer-history", auth, getEpinTransferHistory);
router.post("/epin-purchase", auth, validateRequest(epinPurchaseSchema),epinPurchase);
router.post("/epin-request", auth, validateRequest(epinRequestSchema),epinRequest);
router.post("/epin-transfer", auth, validateRequest(epinTransferSchema),epinTransfer);
router.post("/epin-refund", auth, epinRefund);
router.get("/purchased-epin-list", auth, getPurchasedEpinList);

// Payout
router.get("/payout-details", auth, getPayoutDetails);
router.get("/payout-request-details", auth, getPayoutRequestDetails);
router.post("/payout-request", auth, validateRequest(payoutRequestSchema),payoutRequest);
router.post("/payout-request-cancel", auth, validateRequest(payoutCancelSchema),cancelPayoutRequest);
router.get('/payout-tiles', auth, getPayoutTiles);

// Register
router.get("/register", auth, getRegister);
router.get("/register-field-verification", auth, registerFieldVerification);
router.post("/register", auth, registerUser);
router.post("/check-transaction-password",auth, validateRequest(transactionPasswordSchema),checkTransactionPassword);
router.post("/upload-bank-receipt", auth, uploadBankReceipt);
router.post("/remove-bank-receipt", auth, removeBankReceipt);
router.get("/letter-preview", auth, letterPreview);

// Profile
router.get("/profile-view", auth, profileView);
router.patch("/update-personal-details", auth, validateRequest(personalDataSchema), updatePersonalData);
router.patch("/update-contact-details", auth, validateRequest(contactDataSchema), updateContactDetails);
router.patch("/update-bank-details", auth, validateRequest(bankDataSchema), updateBankDetails);
router.patch("/update-payment-details", auth, updatePaymentDetails);
router.patch("/update-settings", auth, updateSettings);
router.patch("/remove-avatar", auth, removeAvatar);
router.post("/update-avatar", auth, uploadAvatar);
router.get("/kyc-details", auth, getKYCDetails);
router.post("/kyc-upload", auth, kycUpload);
router.post("/kyc-delete", auth, kycDelete);
router.patch("/change-user-password", auth, changeUserPassword);
router.patch("/change-transaction-password", auth, changeTransactionPassword);
router.patch("/update-additionalData", auth, updateCustomFieldValue);

router.get("/payment-methods", auth, getPaymentMethods);
router.post("/check-epin-validity", auth, checkEpinValidity);

// Tree
router.get("/get-genealogy-tree", auth, getGenealogy);
router.get("/get-sponsor-tree", auth, getSponsorTree);
router.get("/get-tree-view", auth, getTreeView);
router.get("/get-downlines", auth, getDownlines);
router.get("/get-downline-header", auth, getDownlineHeader);
router.get("/get-referral-header", auth, getReferralHeader);
router.get("/get-referrals", auth, getReferrals);
router.get("/get-unilevel-more", auth, getUnilevelMore);
router.get("/get-sponsor-tree-more", auth, getSponsorMore);


// Tools
router.get("/all-news", auth, getAllNews);
router.get("/get-news-article", auth, getNewsArticle);
router.get("/get-faqs", auth, getFAQs);
router.get("/downloadable-material", auth, getDownloads);
router.get("/get-countries", auth, getCountries);


// Mail
router.get("/inbox", auth, getMailInbox);
router.get("/inbox-from-admin", auth, getAdminMail);
router.get("/sent-mail", auth, getSentMail);
router.get("/view-single-mail", auth, viewSingleMailThread);
router.get("/compose-mail-data", auth, getComposeMailData);
router.post("/send-internal-mail", auth,validateRequest(composeMailSchema), sendInternalMail);
router.post("/delete-mail", auth, deleteMail);
router.post("/update-all-mail", auth, updateAllMail);
router.post("/reply-to-mail", auth,validateRequest(composeMailSchema), replyToMail);
router.get("/contacts", auth, getReplicaContacts);

export default router;