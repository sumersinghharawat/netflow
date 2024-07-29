import { Navigate } from 'react-router-dom';
import MainDashboard from '../views/dashboard/MainDashboard';
import AuthLayout from '../Layouts/AuthLayout';
import EwalletLayout from '../views/ewallet/MainEwallet';
import PayoutLayout from '../views/payout/MainPayout';
import RegisterLayout from '../views/register/MainRegister';
import ProfileLayout from '../views/profile/MainProfile';
import GenealogyTree from '../views/tree/GenealogyTree';
import TreeView from '../views/tree/TreeView';
import EpinLayout from '../views/epin/MainEpin';
import ShoppingLayout from '../views/shopping/ShoppingLayout';
import ProductDetailsLayout from '../views/shopping/ProductDetailsLayout';
import CheckoutLayout from '../views/shopping/CheckoutLayout';
import Faqs from '../components/Tools/Faqs';
import News from '../components/Tools/News';
import DownlineMembers from '../views/tree/DownlineMembers';
import SponserTree from '../views/tree/SponserView';
import ReferralMembers from '../views/tree/ReferralMembers';
import Leads from '../views/Crm/Leads';
import ComingSoon from '../components/Common/ComingSoon';
import BackToOffice from '../components/Auth/BackToOffice';
import RegisterComplete from '../views/register/RegisterComplete';
import KycDetails from '../components/Profile/KycDetails';
import ReplicaSite from '../views/Tools/ReplicaSite';
import LeadsForm from '../components/Crm/LeadsForm';
import DownloadMaterials from '../views/Tools/DownloadMaterials';
import ReplicaLayout from '../Layouts/ReplicaLayout';
import MainReplica from '../views/replica/MainReplica';
import ReplicaRegisterLayout from '../views/replica/MainReplicaRegister';
import { ForgotPasswordForm } from '../components/Auth/forgotPassword';
import Upgrade from '../views/upgrade/Upgrade';
import Renew from '../views/renew/Renew';
import MailBox from '../views/mailbox/MailBox';
import RepurchaseReport from '../views/shopping/RepurchaseReportLayout';
import PurchaseInvoice from '../components/shopping/PurchaseInvoice';
import MainSupport from '../views/support/MainSupport';
import CreateTicket from '../components/Support/CreateTicket';
import SupportFaqs from '../components/Support/SupportFaqs';
import TicketDetails from '../components/Support/TicketDetails';
import TicketTimeline from '../components/Support/TicketTimeline';
import CrmDashboard from '../views/Crm/CrmDashboard';
import ViewLead from '../views/Crm/ViewLead';
import AddLead from '../views/Crm/AddLead';
import CrmGraph from '../views/Crm/CrmGraph';
import CrmTimeline from '../components/Crm/CrmTimeline';
import LeadsDetails from '../components/Crm/LeadDetails';
import LeadsHistory from '../components/Crm/LeadsHistory';
import GenealogyTreeWebView from '../views/web/GenealogyTreeWebView';
import SponserTreeWebView from '../views/web/SponserTreeWebView';
import WebToReact from '../components/Auth/WebToReact';
import TreeViewWeb from '../views/web/TreeViewWeb';
import WebView from '../components/Auth/WebView';

const publicRoutes = [
  {
    path: '/',
    element: <Navigate to="/login" replace />
  },
  {
    path: '/login/:adminUsername?/:username?',
    element: <AuthLayout />
  },
  {
    path: '/oc-login/?:string?:db_prefix?',
    element: <BackToOffice />
  },
  {
    path: '/lcp/:username?/:hash?',
    element: <LeadsForm />
  },
  {
    path: '/replica/:username/:hashKey',
    element: <ReplicaLayout><MainReplica /></ReplicaLayout>
  },
  {
    path: '/replica-register',
    element: <ReplicaLayout><ReplicaRegisterLayout /></ReplicaLayout>
  },
  {
    path: '/forgot-password/:hashKey',
    element: <ForgotPasswordForm />
  }
];

const privateRoutes = [
  {
    path: '/dashboard',
    element: <MainDashboard />,
  },
  {
    path: '/networks',
    element: <Navigate to='/genealogy-tree' replace />
  },
  {
    path: '/e-wallet',
    element: <EwalletLayout />
  },
  {
    path: '/e-pin',
    element: <EpinLayout />
  },
  {
    path: '/payout',
    element: <PayoutLayout />
  },
  {
    path: '/genealogy-tree',
    element: <GenealogyTree />
  },
  {
    path: '/sponsor-tree',
    element: <SponserTree />
  },
  {
    path: '/tree-view',
    element: <TreeView />
  },
  {
    path: '/downline-members',
    element: <DownlineMembers />
  },
  {
    path: '/referral-members',
    element: <ReferralMembers />
  },
  {
    path: '/register/:parent?/:position?',
    element: <RegisterLayout />
  },
  {
    path: '/profile',
    element: <ProfileLayout />
  },
  {
    path: '/shopping',
    element: <ShoppingLayout />
  },
  {
    path: '/product-details/:id',
    element: <ProductDetailsLayout />
  },
  {
    path: '/checkout',
    element: <CheckoutLayout />
  },
  {
    path: '/faqs',
    element: <Faqs />
  },
  {
    path: '/news/:newsId?',
    element: <News />
  },
  {
    path: '/leads',
    element: <Leads />
  },
  {
    path: '/settings',
    element: <ComingSoon />
  },
  {
    path: '/downloads',
    element: <ComingSoon />
  },
  {
    path: '/mailbox',
    element: <MailBox />
  },
  {
    path: '/support-center',
    element: <MainSupport />
  },
  {
    path: '/download-materials',
    element: <DownloadMaterials />
  },
  {
    path: '/replica-site',
    element: <ReplicaSite />
  },
  {
    path: '/promotion-tools',
    element: <ComingSoon />
  },
  {
    path: '/registration-complete/:username?',
    element: <RegisterComplete />
  },
  {
    path: '/kyc-details',
    element: <KycDetails />
  },
  {
    path: '/upgrade',
    element: <Upgrade />
  },
  {
    path: '/renew',
    element: <Renew />
  },
  {
    path: '/repurchase-report',
    element: <RepurchaseReport />
  },
  {
    path: '/repurchase-invoice/:id',
    element: <PurchaseInvoice />
  },
  {
    path: '/create-ticket',
    element: <CreateTicket />
  },
  {
    path: '/support-faqs',
    element: <SupportFaqs />
  },
  {
    path: '/ticket-details/:trackId?',
    element: <TicketDetails />
  },
  {
    path: '/ticket-timeline/:trackId?',
    element: <TicketTimeline />
  },
  // {
  //   path: '/crm-dashboard',
  //   element: <CrmDashboard />
  // },
  {
    path: '/view-lead',
    element: <ViewLead />
  },
  {
    path: '/add-lead',
    element: <AddLead />
  },
  {
    path: '/crm-graph',
    element: <CrmGraph />
  }, 
  {
    path: '/crm-timeline/:id?',
    element: <LeadsDetails />
  },
  {
    path: '/crm-lead-history/:id?',
    element: <LeadsHistory />
  }

]

const webRoutes = [
  {
    path:'/genealogy-tree-web/',
    element: <GenealogyTreeWebView/>
  },
  {
    path: '/sponsor-tree-web/',
    element: <SponserTreeWebView />
  },
  {
    path: '/tree-view-web',
    element: <TreeViewWeb/>
  },
  {
    path:'/web-login/?:string?:db_prefix:type?',
    element: <WebView/>
  },
]

export { privateRoutes, publicRoutes, webRoutes }
