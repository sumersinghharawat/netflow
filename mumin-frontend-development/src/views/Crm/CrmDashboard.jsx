import React, { useState } from "react";
import CrmLinks from "../../components/Crm/CrmLinks";
import CrmTilesSegment from "../../components/Crm/CrmTilesSegment";
import CrmTable from "../../components/Crm/CrmTable";
import LeadEditModal from "../../components/Common/modals/LeadEditModal";
import AddLeadModal from "../../components/Common/modals/AddLeadModal";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
import NextFollowUp from "../../components/Common/modals/NextFollowUp";
import { crmFormateDate } from "../../utils/formateDate";

const CrmDashboard = () => {
  const { t } = useTranslation();
  const [showLeadEditModal, setShowLeadEditModal] = useState(false);
  const [showAddLeadModal, setShowAddLeadModal] = useState(false);
  const [showNextFollowUp, setShowNextFollowUp] = useState(false);
  const [leadId, setLeadId] = useState("");
  const [leadData, setLeadData] = useState("");
  const [currentPage, setCurrentPage] = useState({
    today: 1,
    missed: 1,
    recent: 1,
  });
  const [itemsPerPage, setItemsPerPage] = useState({
    today: 10,
    missed: 10,
    recent: 10,
  });
  const [editFormData, setEditFormData] = useState({
    id: "",
    firstName: "",
    lastName: "",
    emailId: "",
    skypeId: "",
    mobileNo: "",
    countryId: "",
    description: "",
    interestStatus: "",
    followupDate: null,
    leadStatus: "",
  });
  const [errorMessage, setErrorMessage] = useState({
    firstName: null,
    lastName: null,
    emailId: null,
    skypeId: null,
    mobileNo: null,
    countryId: null,
    description: null,
    interestStatus: null,
    followupDate: null,
    leadStatus: null,
  });

  const followupHeaders = [
    "#",
    t("firstName"),
    t("lastName"),
    t("skype_id"),
    t("edit_lead"),
    t("add_follow-up"),
    t("view_details"),
  ];
  const leadsHeaders = [
    "#",
    t("firstName"),
    t("lastName"),
    t("skype_id"),
    t("lead_completeness"),
    t("date_added"),
    t("edit_lead"),
    t("add_follow-up"),
    t("next_followup_date"),
    t("view_details"),
  ];

  //---------------------------------------------- API -----------------------------------------------

  const todayFollowup = ApiHook.CallFollowupToday(
    currentPage.today,
    itemsPerPage.today
  );
  const recentLeads = ApiHook.CallRecentLeads(
    currentPage.recent,
    itemsPerPage.recent
  );
  const missedFollowup = ApiHook.CallMissedFollowup(
    currentPage.missed,
    itemsPerPage.missed
  );
  const tiles = ApiHook.CallCrmTiles();

  const handleEditClick = (idToFind) => {
    setLeadId(idToFind);
    const item = recentLeads.data?.data.find((item) => item.id === idToFind);

    setEditFormData((prev) => ({
      ...prev,
      id: item?.id,
      firstName: item.firstName,
      lastName: item.lastName,
      emailId: item.emailId,
      skypeId: item.skypeId,
      mobileNo: item.mobileNo,
      countryId: item.countryId,
      description: item.description,
      interestStatus: item.interestStatus,
      leadStatus: item.leadStatus,
    }));
  };
  
  return (
    <>
      <div className="page_head_top">
        {"CRM"}
        <div className="right_btn_mob_toggle">
          <i className="fa fa-bars"></i>
        </div>
      </div>
      <CrmLinks />
      <CrmTilesSegment tiles={tiles} />
      <CrmTable
        title={"Your Follow ups for Today"}
        data={todayFollowup.data?.data}
        headers={followupHeaders}
        setShowLeadEditModal={setShowLeadEditModal}
        setShowAddLeadModal={setShowAddLeadModal}
        startPage={1}
        currentPage={todayFollowup?.data?.currentPage}
        totalPages={todayFollowup?.data?.totalPages}
        setCurrentPage={setCurrentPage}
        itemsPerPage={itemsPerPage}
        setItemsPerPage={setItemsPerPage}
        type={"today"}
        handleEditClick={handleEditClick}
      />
      <CrmTable
        title={"Your Missed Follow up"}
        data={missedFollowup.data?.data}
        headers={followupHeaders}
        setShowLeadEditModal={setShowLeadEditModal}
        setShowAddLeadModal={setShowAddLeadModal}
        startPage={1}
        currentPage={missedFollowup?.data?.currentPage}
        totalPages={missedFollowup?.data?.totalPages}
        setCurrentPage={setCurrentPage}
        itemsPerPage={itemsPerPage}
        setItemsPerPage={setItemsPerPage}
        type={"missed"}
        handleEditClick={handleEditClick}
      />
      <CrmTable
        title={"Recent Leads"}
        data={recentLeads.data?.data}
        headers={leadsHeaders}
        setShowLeadEditModal={setShowLeadEditModal}
        setShowAddLeadModal={setShowAddLeadModal}
        setShowNextFollowUp={setShowNextFollowUp}
        setLeadId={setLeadId}
        setLeadData={setLeadData}
        startPage={1}
        currentPage={recentLeads?.data?.currentPage}
        totalPages={recentLeads?.data?.totalPages}
        setCurrentPage={setCurrentPage}
        itemsPerPage={itemsPerPage}
        setItemsPerPage={setItemsPerPage}
        type={"recent"}
        handleEditClick={handleEditClick}
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
      />
      <LeadEditModal
        leadId={leadId}
        leadData={leadData}
        setLeadData={setLeadData}
        showLeadEditModal={showLeadEditModal}
        setShowLeadEditModal={setShowLeadEditModal}
        editFormData={editFormData}
        setEditFormData={setEditFormData}
        setErrorMessage={setErrorMessage}
        errorMessage={errorMessage}
        countries={tiles?.data?.countries}
      />
      <AddLeadModal
        leadId={leadId}
        showAddLeadModal={showAddLeadModal}
        setShowAddLeadModal={setShowAddLeadModal}
        editFormData={editFormData}
        setEditFormData={setEditFormData}
      />
      <NextFollowUp
        leadId={leadId}
        showNextFollowUp={showNextFollowUp}
        setShowNextFollowUp={setShowNextFollowUp}
        editFormData={editFormData}
        setEditFormData={setEditFormData}
      />
    </>
  );
};

export default CrmDashboard;
