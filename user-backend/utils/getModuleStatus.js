import ModuleStatus from '../models/moduleStatus.js'

export default async ({attributes1}) => {
    return await ModuleStatus.findOne({ 
        attributes:attributes1 || ["mlmPlan","pinStatus","productStatus","mailboxStatus","employeeStatus","rankStatus","langStatus","captchaStatus","multiCurrencyStatus","leadCaptureStatus","ticketSystemStatus","ecomStatus","autoresponderStatus","lcpType","paymentGatewayStatus","repurchaseStatus","googleAuthStatus","packageUpgrade","roiStatus","xupStatus","hyipStatus","kycStatus","signupConfig","purchaseWallet","subscriptionStatus","promotionStatus","multilangStatus","defaultLangCode","multiCurrencyStatus","defaultCurrencyCode","replicatedSiteStatus"],
        raw: true});
}