import React, { useState } from "react";
import MailViewContent from "./MailViewContent";
import MailSidebarContent from "./MailSidebarContent";
import MailListContainerContent from "./MailListContainerContent";
import MailCompose from "./MailCompose";
import { ApiHook } from "../../hooks/apiHook";
import { useSelector } from "react-redux";

const MailboxComponent = ({ showMobileNav }) => {
  const [showCompose, setShowCompose] = useState(false);
  const [singleMailId, setSingleMailId] = useState("");
  const [mailCheck, setMailCheck] = useState(false);
  const [activeTab, setActiveTab] = useState("inbox");
  const [selectedContacts, setSelectedContacts] = useState();
  const [showMailPlaceholder, setShowMailPlaceholder] = useState(true);
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(10);
  const [selectedCheckboxes, setSelectedCheckboxes] = useState({});
  const [selectAllChecked, setSelectAllChecked] = useState(false);
  const [replyBtn, setReplyBtn] = useState(false);
  const [selectedTab, setSelectedTab] = useState({
    inbox: true,
    sent: false,
    adminInbox: false,
    replicaInbox: false,
  });
  const inboxes = ApiHook.CallInboxes(page, perPage, selectedTab);
  const sendMail = ApiHook.CallSentMail(page, perPage, selectedTab);
  const adminInbox = ApiHook.CallAdminInbox(page, perPage, selectedTab);
  const replicaInbox = ApiHook.CallReplicaInbox(page, perPage, selectedTab);
  const mailList = useSelector((state) => state.mail?.mailList);
  const handleTabChange = (tab) => {
    setSelectedCheckboxes({});
    setSelectAllChecked(false);
    setShowMailPlaceholder(true);
    setActiveTab(tab);
    setApiTab(tab);
    setPage(1);
    setPerPage(10);
  };

  const setApiTab = (tab) => {
    setSelectedTab((prev) => ({
      inbox: tab === "inbox",
      sent: tab === "sent",
      adminInbox: tab === "adminInbox",
      replicaInbox: tab === "replicaInbox",
    }));
  };

  const getMailContent = () => {
    let mailContent;
    let mailData;
    switch (activeTab) {
      case "inbox":
        mailContent = mailList;
        mailData = inboxes?.data?.data;
        break;
      case "sent":
        mailContent = mailList;
        mailData = sendMail?.data?.data;
        break;
      case "adminInbox":
        mailContent = mailList;
        mailData = adminInbox?.data?.data;
        break;
      case "replicaInbox":
        mailContent = mailList;
        mailData = replicaInbox?.data?.data;
        break;
      default:
        throw new Error("Invalid mailbox type");
    }

    return { mailContent: mailContent || [], mailData };
  };
  return (
    <>
      <div className="content-wrapper">
        <div className="email-wrapper wrapper">
          <div className="row align-items-stretch">
            <div
              className={`mail-sidebar d-none d-lg-block col-md-2 pt-3 bg-white ${
                showMobileNav ? "show_mailbox_sidebar" : ""
              } `}
            >
              {/* Sidebar content */}
              <MailSidebarContent
                setShowCompose={setShowCompose}
                handleTabChange={handleTabChange}
                activeTab={activeTab}
                count={inboxes?.data?.data?.inboxUnreadCount}
                adminInboxCount={inboxes?.data?.data?.adminInboxUnreadCount}
                {...getMailContent()}
              />
            </div>
            <div
              id="mail-list-container"
              className="mail-list-container col-md-3 pt-4 border-right bg-white"
            >
              {/* Mail list container content */}
              <MailListContainerContent
                // inboxes={getMailContent()}
                {...getMailContent()}
                setSingleMailId={setSingleMailId}
                setMailCheck={setMailCheck}
                activeTab={activeTab}
                setSelectedContacts={setSelectedContacts}
                setShowMailPlaceholder={setShowMailPlaceholder}
                page={page}
                setPage={setPage}
                setPerPage={setPerPage}
                selectedContacts={selectedContacts}
                setApiTab={setApiTab}
                selectedCheckboxes={selectedCheckboxes}
                setSelectedCheckboxes={setSelectedCheckboxes}
                selectAllChecked={selectAllChecked}
                setSelectAllChecked={setSelectAllChecked}
                setReplyBtn={setReplyBtn}
              />
            </div>
            <div className="mail-view col-md-9 col-lg-7 bg-white">
              {/* Mail view content */}
              <MailViewContent
                // inboxes={getMailContent()}
                {...getMailContent()}
                setSingleMailId={setSingleMailId}
                mailId={singleMailId}
                mailCheck={mailCheck}
                setMailCheck={setMailCheck}
                activeTab={activeTab}
                selectedContacts={selectedContacts}
                showMailPlaceholder={showMailPlaceholder}
                setShowMailPlaceholder={setShowMailPlaceholder}
                setApiTab={setApiTab}
                setPage={setPage}
                replyBtn={replyBtn}
                setReplyBtn={setReplyBtn}
              />
            </div>
          </div>
        </div>
      </div>
      {/* Compose Mail */}
      <MailCompose
        showCompose={showCompose}
        setShowCompose={setShowCompose}
        setPage={setPage}
        setApiTab={setApiTab}
        activeTab={activeTab}
     u/>
    </>
  );
};

export default MailboxComponent;
