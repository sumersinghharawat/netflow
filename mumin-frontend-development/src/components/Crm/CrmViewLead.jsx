import React, { useState } from "react";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
import DatePickerComponent from "../Common/DatePickerComponent";
import Select from "react-select";
import dayjs from "dayjs";
import CrmTable from "./CrmTable";
import LeadEditModal from "../Common/modals/LeadEditModal";
import AddLeadModal from "../Common/modals/AddLeadModal";
import NextFollowUp from "../Common/modals/NextFollowUp";

const CrmViewLead = () => {
  const { t } = useTranslation();
  const initialFormData = {
    searchTag: "",
    fromDate: "",
    toDate: "",
    nextFromDate: "",
    nextToDate: "",
    level_of_interest: "",
    country: "",
    statusFromDate: "",
    statusToDate: "",
    leadStatus: "",
  };
  const [selectedDate, setSelectedDate] = useState(dayjs());
  const [isCalenderOpen, setIsCalenderOpen] = useState({
    fromDate: false,
    toDate: false,
    nextFromDate: false,
    nextToDate: false,
    statusFromDate: false,
    statusToDate: false,
  });
  const [formData, setFormData] = useState(initialFormData);
  const [apiCheck, setApiCheck] = useState(false);
  const [currentPage, setCurrentPage] = useState({ recent: 1 });
  const [itemsPerPage, setItemsPerPage] = useState({ recent: 10 });
  const [leadData, setLeadData] = useState("");
  const [leadId, setLeadId] = useState("");
  const [showLeadEditModal, setShowLeadEditModal] = useState(false);
  const [showAddLeadModal, setShowAddLeadModal] = useState(false);
  const [showNextFollowUp, setShowNextFollowUp] = useState(false);
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
    followupDate: "",
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
  const interestOptions = [
    {
      label: t("not_interested"),
      value: 0,
    },
    {
      label: t("interested"),
      value: 1,
    },
    {
      label: t("very_interested"),
      value: 2,
    },
  ];

  const statusOptions = [
    {
      label: t("ongoing"),
      value: 1,
    },
    {
      label: t("accepted"),
      value: 2,
    },
    {
      label: t("rejected"),
      value: 0,
    },
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
    t("view_details")
  ];

  //---------------------------------------- API ------------------------------------
  const leads = ApiHook.CallViewLeads(
    formData,
    apiCheck,
    setApiCheck,
    currentPage.recent,
    itemsPerPage.recent
  );

  const handleInputChange = (e) => {
    const { value } = e.target;
    setFormData({
      ...formData,
      searchTag: value,
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setApiCheck(!apiCheck);
    setSelectedDate("");
  };
  
  const handleEditClick = (idToFind) => {
    setLeadId(idToFind);
    const item = leads.data?.data.find((item) => item.id === idToFind);
    setEditFormData({
      id: item?.id,
      firstName: item.firstName,
      lastName: item.lastName,
      emailId: item.emailId,
      skypeId: item.skypeId,
      mobileNo: item?.mobileNo,
      countryId: item.countryId,
      description: item.description,
      interestStatus: item.interestStatus,
      followupDate: item.followupDate,
      leadStatus: item.leadStatus,
    });
  };

  return (
    <div className="row crm-view-lead">
      <div className="col-xl-12">
        <div className="frm-content">
          <div className="crm_panel__h6izZ">
            <div className="p-3">
              <legend>
                <span>{t("find_lead")}</span>
              </legend>
              <div>
                <form onSubmit={handleSubmit}>
                  <div className="row">
                    <div className="col-xl-4">
                      <div className="form-group">
                        <label htmlFor="searchTag" className="form-label">
                          {t("search_tag")}
                        </label>
                        <input
                          id="searchTag"
                          name={"Search Tag"}
                          placeholder={
                            "Search by first name, last name, skype ID, email ID, mobile no."
                          }
                          type="text"
                          className="form-control"
                          defaultValue={formData.searchTag}
                          onChange={(e) => handleInputChange(e)}
                        />
                      </div>
                      <div className="form-group">
                        <label className="form-label">
                          {t("lead_added_from_date")}
                        </label>
                        <DatePickerComponent
                          className={"date-picker"}
                          date={selectedDate}
                          handleDateChange={(newDate) =>
                            setFormData({
                              ...formData,
                              fromDate: newDate.format("YYYY-MM-DD"),
                            })
                          }
                          isCalenderOpen={isCalenderOpen.fromDate}
                          openCalender={() =>
                            setIsCalenderOpen({
                              ...isCalenderOpen,
                              fromDate: true,
                            })
                          }
                          closeCalender={() =>
                            setIsCalenderOpen({
                              ...isCalenderOpen,
                              fromDate: false,
                            })
                          }
                        />
                      </div>
                      <div className="form-group">
                        <label className="form-label">
                          {t("lead_added_to_date")}
                        </label>
                        <DatePickerComponent
                          className={"date-picker"}
                          date={selectedDate}
                          handleDateChange={(newDate) =>
                            setFormData({
                              ...formData,
                              toDate: newDate.format("YYYY-MM-DD"),
                            })
                          }
                          isCalenderOpen={isCalenderOpen.toDate}
                          openCalender={() =>
                            setIsCalenderOpen({
                              ...isCalenderOpen,
                              toDate: true,
                            })
                          }
                          closeCalender={() =>
                            setIsCalenderOpen({
                              ...isCalenderOpen,
                              toDate: false,
                            })
                          }
                        />
                      </div>
                      <div className="form-group">
                        <label className="form-label">
                          {t("next_follow-up_from_date")}
                        </label>
                        <DatePickerComponent
                          className={"date-picker"}
                          date={selectedDate}
                          handleDateChange={(newDate) =>
                            setFormData({
                              ...formData,
                              nextFromDate: newDate.format("YYYY-MM-DD"),
                            })
                          }
                          isCalenderOpen={isCalenderOpen.nextFromDate}
                          openCalender={() =>
                            setIsCalenderOpen({
                              ...isCalenderOpen,
                              nextFromDate: true,
                            })
                          }
                          closeCalender={() =>
                            setIsCalenderOpen({
                              ...isCalenderOpen,
                              nextFromDate: false,
                            })
                          }
                        />
                      </div>
                    </div>
                    <div className="col-xl-4">
                      <div className="form-group">
                        <label className="form-label">
                          {t("next_follow-up_to_date")}
                        </label>
                        <DatePickerComponent
                          className={"date-picker"}
                          date={selectedDate}
                          handleDateChange={(newDate) =>
                            setFormData({
                              ...formData,
                              nextToDate: newDate.format("YYYY-MM-DD"),
                            })
                          }
                          isCalenderOpen={isCalenderOpen.nextToDate}
                          openCalender={() =>
                            setIsCalenderOpen({
                              ...isCalenderOpen,
                              nextToDate: true,
                            })
                          }
                          closeCalender={() =>
                            setIsCalenderOpen({
                              ...isCalenderOpen,
                              nextToDate: false,
                            })
                          }
                        />
                      </div>
                      <div className="form-group">
                        <label htmlFor="interest_status" className="form-label">
                          {t("level_of_interest")}
                        </label>
                        <Select
                          id={"interest_status"}
                          name={"interest_status"}
                          className={`dropdown-common`}
                          options={interestOptions}
                          onChange={(data) => {
                            setFormData({
                              ...formData,
                              level_of_interest: data.value,
                            });
                          }}
                          defaultValue={formData.level_of_interest}
                          isSearchable={false}
                        />
                      </div>
                      <div className="form-group">
                        <label htmlFor="country" className="form-label">
                          {"Country"}
                        </label>
                        <Select
                          id={"country"}
                          name={"country"}
                          className={`dropdown-common`}
                          options={leads.data?.countries}
                          onChange={(data) => {
                            setFormData({
                              ...formData,
                              country: data.value,
                            });
                          }}
                          defaultValue={formData.country}
                          isSearchable
                        />
                      </div>
                    </div>
                    <div className="col-xl-4">
                      <div className="form-group">
                        <label htmlFor="leadStatus" className="form-label">
                          {t("lead_status")}
                        </label>
                        <Select
                          id={"leadStatus"}
                          name={"leadStatus"}
                          className={`dropdown-common`}
                          options={statusOptions}
                          onChange={(data) => {
                            setFormData({
                              ...formData,
                              leadStatus: data.value,
                            });
                          }}
                          defaultValue={formData.leadStatus}
                          isSearchable={false}
                        />
                      </div>
                      <div className="form-group">
                        <label className="form-label">
                          {t("lead_status_change_from_date")}
                        </label>
                        <div>
                          <div>
                            <DatePickerComponent
                              className={"date-picker"}
                              date={selectedDate}
                              handleDateChange={(newDate) =>
                                setFormData({
                                  ...formData,
                                  statusFromDate: newDate.format("YYYY-MM-DD"),
                                })
                              }
                              isCalenderOpen={isCalenderOpen.statusFromDate}
                              openCalender={() =>
                                setIsCalenderOpen({
                                  ...isCalenderOpen,
                                  statusFromDate: true,
                                })
                              }
                              closeCalender={() =>
                                setIsCalenderOpen({
                                  ...isCalenderOpen,
                                  statusFromDate: false,
                                })
                              }
                            />
                          </div>
                        </div>
                      </div>
                      <div className="form-group">
                        <label className="form-label">
                          {t("lead_status_change_to_date")}
                        </label>
                        <div>
                          <div>
                            <DatePickerComponent
                              className={"date-picker"}
                              date={selectedDate}
                              handleDateChange={(newDate) =>
                                setFormData({
                                  ...formData,
                                  statusToDate: newDate.format("YYYY-MM-DD"),
                                })
                              }
                              isCalenderOpen={isCalenderOpen.statusToDate}
                              openCalender={() =>
                                setIsCalenderOpen({
                                  ...isCalenderOpen,
                                  statusToDate: true,
                                })
                              }
                              closeCalender={() =>
                                setIsCalenderOpen({
                                  ...isCalenderOpen,
                                  statusToDate: false,
                                })
                              }
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <button type="submit" className="btn btn-primary">
                    {t("submit")}
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div className="col-xl-12">
        <div className="table-sec">
          <div className="crm_panel__h6izZ">
            <div className="crm_panel_body__31wq1 p-0">
              <div className="crm_table_responsive__cut_1 crm_panel__h6izZ">
                <CrmTable
                  title={"Recent Leads"}
                  data={leads.data?.data}
                  headers={leadsHeaders}
                  setShowLeadEditModal={setShowLeadEditModal}
                  setShowAddLeadModal={setShowAddLeadModal}
                  setShowNextFollowUp={setShowNextFollowUp}
                  startPage={1}
                  currentPage={leads?.data?.currentPage}
                  totalPages={leads?.data?.totalPages}
                  setCurrentPage={setCurrentPage}
                  itemsPerPage={itemsPerPage}
                  setItemsPerPage={setItemsPerPage}
                  type={"recent"}
                  handleEditClick={handleEditClick}
                  errorMessage={errorMessage}
                  setErrorMessage={setErrorMessage}
                  setApiCheck={setApiCheck}
                  check={true}
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
                  countries={leads.data?.countries}
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
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CrmViewLead;
