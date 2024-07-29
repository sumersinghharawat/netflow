import Activity from "./activities.js";
import Address from "./address.js";
import AggregateUserCommissionAndIncome from "./aggregateUserCommissionAndIncome.js";
import AmountPaid from "./amountPaid.js";
import Cart from "./cart.js";
import CartPaymentReceipt from "./cartPaymentReceipt.js";
import CompanyProfile from "./companyProfile.js";
import Compensation from "./compensation.js";
import Configuration from "./configuration.js";
import Contact from "./contact.js";
import Country from "./countries.js";
import CrmFollowup from "./crmFollowup.js";
import CrmLead from "./crmLead.js";
import CurrencyDetail from "./currency.js";
import CustomfieldLang from "./customfieldLang.js";
import CustomfieldValue from "./customfieldValues.js";
import DemoUser from "./demoUser.js";
import Document from "./document.js";
import EpinTransferHistory from "./epinTransferHistory.js";
import EwalletCommissionHistory from "./ewalletCommissionHistory.js";
import EwalletPaymentDetail from "./ewalletPaymentDetail.js";
import EwalletPurchaseHistory from "./ewalletPurchaseHistory.js";
import EwalletTransferHistory from "./ewalletTransferHistory.js";
import FAQ from "./faqs.js";
import FundTransferDetail from "./fundTransferDetail.js";
import KycCategory from "./kycCategory.js";
import KycDoc from "./kycDocs.js";
import Language from "./language.js";
import LegAmount from "./legAmount.js";
import LegDetail from "./legDetail.js";
import LevelCommissionRegisterPacks from "./levelCommissionRegisterPack.js";
import MailBox from "./mailBox.js";
import Menu from "./menu.js";
import MenuPermission from "./menuPermission.js";
import News from "./news.js";
import Notification from "./notification.js";
import OcCustomer from "./ocCustomer.js";
import OcProduct from "./ocProduct.js";
import Order from "./order.js";
import OrderDetail from "./orderDetail.js";
import Package from "./package.js";
import PackageUpgradeHistory from "./packageUpgradeHistory.js";
import PackageValidityExtendHistory from "./packageValidityExtendHistory.js";
import PaymentGatewayConfig from "./paymentGatewayConfig.js";
import PaymentGatewayDetail from "./paymentGatewayDetail.js";
import PaymentReceipt from "./paymentReceipt.js";
import PaypalHistory from "./paypalHistories.js";
import PayoutConfiguration from "./payoutConfiguration.js";
import PayoutReleaseRequest from "./payoutReleaseRequest.js";
import PaypalProduct from "./paypalProduct.js";
import PaypalSubscription from "./paypalSubscription.js";
import PendingRegistration from "./pendingRegistration.js";
import PinAmountDetail from "./pinAmountDetail.js";
import PinConfig from "./pinConfig.js";
import PinNumber from "./pinNumber.js";
import PinRequest from "./pinRequest.js";
import PinUsed from "./pinUsed.js";
import PurchaseRank from "./purchaseRank.js";
import PurchaseWalletHistory from "./purchaseWalletHistory.js";
import Rank from "./rank.js";
import RankDetail from "./rankDetails.js";
import RankDownlineRank from "./rankDownlineRank.js";
import RankUser from "./rankUser.js";
import ReplicaBanner from "./replicaBanner.js";
import ReplicaContent from "./replicaContent.js";
import RepurchaseCategory from "./repurchaseCategory.js";
import RoiOrder from "./roiOrder.js";
import SalesOrder from "./salesOrder.js";
import SignupField from "./signupFields.js";
import SignupSettings from "./signupSettings.js";
import SponsorTreepath from "./sponsorTreepath.js";
import State from "./states.js";
import SubscriptionConfig from "./subscriptionConfig.js";
import Transaction from "./transaction.js";
import TransactionPassword from "./transactionPassword.js";
import Treepath from "./treepath.js";
import UpgradeSalesOrder from "./upgradeSalesOrder.js";
import UploadCategory from "./uploadCategory.js";
import User from "./user.js";
import UserBalanceAmount from "./userBalanceAmount.js";
import UserDetail from "./userDetail.js";
import UserPlacement from "./userPlacement.js";
import UserpvDetail from "./userpvDetail.js";
import UsersRegistration from "./usersRegistration.js";
import Ticket from "./ticket.js";
import TicketCategory from "./ticketCategory.js";
import TicketPriority from "./ticketPriority.js";
import TicketStatus from "./ticketStatus.js";
import Tag from "./tag.js";
import TicketTag from "./ticketTag.js"
import TicketComment from "./ticketComment.js";
import TicketFaq from "./ticketFaq.js";
import TicketActivity from "./ticketActivity.js";
import TicketReply from "./ticketReply.js";

// foreignKey - for belongsTo, it is the name of the FK column in the source model which refers to the target model. for hasOne or hasMany, foreignKey refers to the foreign key column in the target model.
// targetKey - for belongsTo, it is the name of the PK column in the target model that the FK in the source model references. used to specify a custom column other than the PK of the target model. for hasOne or hasMany, targetKey refers to the primary key column in the source model.
// sourceKey -  name of the column in the source model. used to specify a custom column other than the PK of the source model

AmountPaid.belongsTo(User);
AmountPaid.belongsTo(PaymentGatewayConfig, { foreignKey: "paymentMethod" });

Cart.belongsTo(Package,{foreignKey:"packageId"});

CrmLead.hasMany(CrmFollowup, {foreignKey:"leadId"});
CrmLead.belongsTo(Country, { foreignKey: "countryId" });
CrmLead.belongsTo(User, { foreignKey: "addedBy" });

CrmFollowup.belongsTo(User, {foreignKey: "followupEnteredBy"});

Document.belongsTo(UploadCategory, {foreignKey: "catId"});

EpinTransferHistory.belongsTo(User, {foreignKey:"to_user", as:"toUserId"});
EpinTransferHistory.belongsTo(User, {foreignKey:"from_user", as:"fromUserId"});
EpinTransferHistory.belongsTo(PinNumber , {foreignKey:"epinId"});

EwalletPurchaseHistory.belongsTo(PayoutReleaseRequest, {foreignKey:"referenceId"});

EwalletTransferHistory.belongsTo(FundTransferDetail, { foreignKey: "fund_transfer_id", as: "TransferDetails"});
EwalletTransferHistory.belongsTo(User);
EwalletTransferHistory.belongsTo(Transaction);

FundTransferDetail.belongsTo(User, { foreignKey: "from_id", as: "FromUser" });
FundTransferDetail.belongsTo(User, { foreignKey: "to_id", as: "ToUser" });

Language.hasMany(User, { foreignKey: "defaultLang" });
LegAmount.belongsTo(User,{foreignKey:"fromId"});

MailBox.belongsTo(User,{foreignKey:"fromUserId", as: "fromUser"});
MailBox.belongsTo(User,{foreignKey:"toUserId", as: "toUser"});
MailBox.hasMany(MailBox,{foreignKey:"thread", as: "Threads"});


// Menu- menupermission association
Menu.hasOne(MenuPermission);
Menu.hasMany(Menu, { foreignKey: "parent_id", as: "subMenu"});
MenuPermission.belongsTo(Menu);

OcProduct.hasMany(User, {foreignKey:"ocProductId"});

Order.belongsTo(PaymentGatewayConfig, { foreignKey: "paymentMethod" });
Order.belongsTo(Address, { foreignKey: "orderAddressId" });
Order.hasMany(OrderDetail);
OrderDetail.belongsTo(Package);
OrderDetail.belongsTo(Order);

Package.belongsTo(RepurchaseCategory, { foreignKey: "categoryId" });
Package.hasMany(LevelCommissionRegisterPacks, { foreignKey: "packageId" });
Package.hasMany(User, { foreignKey: "productId" });
Package.hasOne(Rank, { foreignKey: "packageId" });
Package.hasOne(PaypalProduct, { foreignKey: "productId" });

PaymentGatewayConfig.hasOne(PaymentGatewayDetail, { foreignKey: "paymentGatewayId" });
PaymentGatewayDetail.belongsTo(PaymentGatewayConfig, { foreignKey: "paymentGatewayId" });

PayoutReleaseRequest.belongsTo(PaymentGatewayConfig, { foreignKey: "paymentMethod" });

PaypalProduct.belongsTo(Package, { foreignKey: "productId" });

PinNumber.belongsTo(PinAmountDetail, { foreignKey: "amount", targetKey: "amount" });

PurchaseWalletHistory.belongsTo(User, {foreignKey:"from_user_id",as:"purchaseWalletFromUser"});

Rank.hasMany(User,{foreignKey:"userRankId"});

RankDetail.belongsTo(Rank, {foreignKey:"rank_id",as:"rank"});

Rank.hasOne(RankDetail, { foreignKey: "rank_id", as: "details" });
Rank.belongsTo(Package, { foreignKey: "package_id" });
Rank.belongsTo(OcProduct, { foreignKey: "oc_product_id" });
Rank.belongsToMany(Package, { through: PurchaseRank, as: "PackageCount", foreignKey: "rank_id", otherKey:"package_id", targetKey:"id" });
Rank.belongsToMany(OcProduct, { through: PurchaseRank, as: "OcProductCount", foreignKey: "rank_id", otherKey:"oc_product_product_id", targetKey:"productId" });
Rank.belongsToMany(Rank, { through: RankDownlineRank, as: "RankCount", foreignKey: "rank_id", otherKey: "downline_rank_id" });


SignupField.hasMany(CustomfieldLang, { foreignKey: "customfieldId" });
SignupField.hasOne(CustomfieldValue, { foreignKey: "customfieldId"});

SponsorTreepath.belongsTo(User, { foreignKey: "descendant", as: "sponsorDescendantUser", });
SponsorTreepath.belongsTo(User, { foreignKey: "ancestor", as: "sponsorAncestorUser" });
SponsorTreepath.belongsTo(User, { foreignKey: "descendant", targetKey: "sponsorId", as: "descendantChildren" }); //

Treepath.belongsTo(User, { foreignKey: "descendant", targetKey: "id", as: "downlines", });


User.hasOne(UserDetail);
User.hasOne(UserBalanceAmount);
User.hasMany(LegAmount);
User.hasMany(Treepath, { foreignKey: "descendant", as: "descendant", });
User.hasMany(Treepath, { foreignKey: "ancestor", as: "ancestor" });
User.hasMany(SponsorTreepath, { foreignKey: "descendant", as: "sponsorDescendant", });
User.hasMany(PayoutReleaseRequest);
// User.hasMany(PinNumber, {foreignKey:"generated_user"});
User.hasMany(PinNumber, {foreignKey:"allocated_user", as: "allocatedUser"});

User.hasMany(PinRequest);
User.hasMany(AmountPaid);
User.hasMany(User, { foreignKey: "sponsor_id", targetKey: "id", as: "downline" });
User.hasMany(AggregateUserCommissionAndIncome);
User.hasMany(EwalletCommissionHistory);
User.hasMany(EwalletPurchaseHistory);
User.hasMany(EwalletTransferHistory);
User.hasMany(PaypalSubscription);
User.belongsTo(CurrencyDetail, { foreignKey: "default_currency" });
User.belongsTo(Rank, { foreignKey: "user_rank_id", targetKey: "id" });
// User.belongsTo(RankDetail, { foreignKey: "user_rank_id", targetKey: "rank_id", as: "rankDetail", });
User.belongsTo(User, { foreignKey: "sponsor_id", as: "sponsor" });
User.belongsTo(User, { foreignKey: "father_id", as: "father" });
User.belongsTo(Package, { foreignKey:"product_id"});
User.belongsTo(OcProduct,{foreignKey:"ocProductId"});
User.hasOne(TransactionPassword);
User.hasOne(LegDetail);
User.hasOne(UserpvDetail);
User.hasMany(User, { foreignKey: "father_id", as: "children"});
User.belongsToMany(User, { through: Treepath, foreignKey: "descendant", as: "userAncestorData"});
User.belongsToMany(User, { through: Treepath, foreignKey: "ancestor", as: "userDescendantData"});
User.belongsTo(Language, {foreignKey: "default_lang"});

User.belongsToMany(User, { through: SponsorTreepath, foreignKey: "descendant", as: "userUnilevelAncestorData"});
User.belongsToMany(User, { through: SponsorTreepath, foreignKey: "ancestor", as: "userUbilevelDescendantData"});

User.hasOne(UserPlacement, {as: 'UserPlacement'});

User.belongsToMany(Rank, {through: RankUser, foreignKey: "user_id", as: "userRanks"});

UserDetail.belongsTo(User);
UserDetail.belongsTo(Country);
UserDetail.belongsTo(State);
UserDetail.belongsTo(PaymentGatewayConfig, { foreignKey: "payoutType" });

Country.hasMany(State, {foreignKey: 'countryId'});
State.belongsTo(Country);

PinNumber.hasOne(EpinTransferHistory, {foreignKey:"epin_id"});

PendingRegistration.belongsTo(Package, {foreignKey: 'package_id'});
KycDoc.belongsTo(KycCategory, {foreignKey: 'type'});
KycCategory.hasMany(KycDoc, {foreignKey:'type'});

UserPlacement.belongsTo(User, {as: 'parent', foreignKey: 'branch_parent'});
UserPlacement.belongsTo(User, {as: 'user', foreignKey: 'user_id'});
UserPlacement.belongsTo(User, {as: 'left', foreignKey: 'left_most'});
UserPlacement.belongsTo(User, {as: 'right', foreignKey: 'right_most'});

// Tickets
Ticket.belongsTo(User, {foreignKey:"user_id"});
Ticket.belongsTo(TicketStatus,{ foreignKey:"status_id"});
Ticket.belongsTo(TicketCategory,{ foreignKey:"category_id"});
Ticket.belongsTo(TicketPriority,{ foreignKey:"priority_id"});
TicketTag.belongsTo(Tag,{ foreignKey:"tag_id"});
Ticket.belongsToMany(Tag,{ through:TicketTag, as:"TicketTags"});
Ticket.belongsTo(User, {as: "Assignee", foreignKey: "assignee_id"});
Ticket.hasMany(TicketComment, {foreignKey: "ticket_id"});
// Ticket.belongsToMany(User, { through: TicketComment, as: "CommentedBy", foreignKey: "commented_by"});
TicketFaq.belongsTo(TicketCategory, {foreignKey:"category_id"});
Ticket.hasMany(TicketActivity, {foreignKey: "ticket_id"});
TicketReply.belongsTo(User);

export { 
    Activity,
    Address,
    AggregateUserCommissionAndIncome,
    AmountPaid,
    Cart,
    CartPaymentReceipt,
    CompanyProfile,
    Compensation,
    Configuration,
    Contact,
    Country,
    CrmFollowup,
    CrmLead,
    CurrencyDetail, 
    CustomfieldLang,
    CustomfieldValue,
    DemoUser,
    Document,
    EpinTransferHistory,
    EwalletCommissionHistory,
    EwalletPaymentDetail,
    EwalletPurchaseHistory,
    EwalletTransferHistory,
    FAQ,
    FundTransferDetail,
    KycCategory,
    KycDoc,
    LegAmount,
    LegDetail,
    MailBox,
    Menu,
    MenuPermission,
    News,
    Notification,
    OcCustomer,
    OcProduct,
    Order,
    OrderDetail,
    Package,
    PackageUpgradeHistory,
    PackageValidityExtendHistory,
    LevelCommissionRegisterPacks,
    PaymentGatewayConfig,
    PaymentGatewayDetail,
    PaymentReceipt,
    PaypalHistory,
    PayoutConfiguration,
    PayoutReleaseRequest,
    PaypalProduct,
    PaypalSubscription,
    PendingRegistration,
    PinAmountDetail,
    PinConfig,
    PinNumber,
    PinRequest,
    PinUsed,
    PurchaseWalletHistory,
    Rank,
    RankDetail,
    ReplicaBanner,
    ReplicaContent,
    RepurchaseCategory,
    RoiOrder,
    SalesOrder,
    SignupField,
    SignupSettings, 
    SponsorTreepath,
    State,
    SubscriptionConfig,
    Transaction,
    TransactionPassword,
    Treepath,
    UpgradeSalesOrder,
    UploadCategory,
    User, 
    UserBalanceAmount,
    UserDetail, 
    UserpvDetail,
    UsersRegistration,
    UserPlacement,
    PurchaseRank,
    RankDownlineRank,
    Tag,
    TicketStatus,
    TicketCategory,
    TicketPriority,
    TicketTag,
    Ticket,
    TicketComment,
    TicketActivity,
};