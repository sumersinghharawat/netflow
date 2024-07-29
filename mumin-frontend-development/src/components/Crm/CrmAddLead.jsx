import React, { useState } from "react";
import Select from "react-select";
import { useTranslation } from "react-i18next";
import DatePickerComponent from "../Common/DatePickerComponent";
import dayjs from "dayjs";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";
import { PhoneInput } from "react-international-phone";

const AddLeadForm = () => {
  const { t } = useTranslation();
  const [selectedDate, setSelectedDate] = useState(dayjs());
  const initialFormData = {
    firstName: "",
    lastName: "",
    emailId: "",
    skypeId: "",
    mobileNo: "",
    countryId: "",
    interestStatus: "",
    followupDate: "",
    leadStatus: "",
    description: "",
  };
  const [formData, setFormData] = useState(initialFormData);
  const [isCalenderOpen, setIsCalenderOpen] = useState({
    nextDate: false,
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

  //------------------------------------------- API --------------------------------------------------

  const addLeadMutation = ApiHook.CallAddCrmLead();
  const countries = ApiHook.CallGetCountries();

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value,
    });
  };

  const handlePhoneNumber = (phone) => {
    setFormData({ ...formData, mobileNo: phone });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setFormData({
      firstName: formData.firstName,
      lastName: formData.lastName,
      emailId: formData.emailId,
      skypeId: formData.skypeId,
      mobileNo: formData.mobileNo,
      countryId: formData.countryId,
      interestStatus: formData.interestStatus,
      followupDate: formData.followupDate,
      leadStatus: formData.leadStatus,
      description: formData.description,
    });
    addLeadMutation.mutate(formData, {
      onSuccess: (res) => {
        if (res.status) {
          toast.success(res.data);
          setFormData(initialFormData);
          setSelectedDate("");
        } else {
          toast.error(res?.message);
        }
      },
    });
  };

  return (
    <>
      <div className="page_head_top">{t("add_lead")}</div>
      <div className="frm-content">
        <div className="p-3 container-fluid">
          <div className="crm_panel__h6izZ">
            <div className="crm_panel_body__31wq1">
              <form className="" onSubmit={handleSubmit}>
                <div className="row">
                  <div className="col-xl-4">
                    <div className="form-group">
                      <label className="form-label">{t("firstName")}</label>
                      <input
                        name="firstName"
                        type="text"
                        className="form-control"
                        defaultValue={formData.firstName}
                        onChange={handleChange}
                      />
                    </div>
                    <div className="form-group">
                      <label className="form-label">{t("lastName")}</label>
                      <input
                        name="lastName"
                        type="text"
                        className="form-control"
                        defaultValue={formData.lastName}
                        onChange={handleChange}
                      />
                    </div>
                    <div className="form-group">
                      <label className="form-label">{t("email")}</label>
                      <input
                        name="emailId"
                        type="text"
                        className="form-control"
                        defaultValue={formData.emailId}
                        onChange={handleChange}
                      />
                    </div>
                  </div>
                  <div className="col-xl-4">
                    <div className="form-group">
                      <label className="form-label">{t("skype")}</label>
                      <input
                        name="skypeId"
                        type="text"
                        className="form-control"
                        defaultValue={formData.skypeId}
                        onChange={handleChange}
                      />
                    </div>
                    <div className="form-group">
                      <label className="form-label">{t("mobileNo")}</label>
                      <PhoneInput
                        defaultCountry="us"
                        value={formData.mobileNo}
                        onChange={handlePhoneNumber}
                      />
                    </div>
                    <div className="form-group">
                      <label htmlFor="countryId" className="form-label">
                        {"Country"}
                      </label>
                      <Select
                        id={"countryId"}
                        name={"countryId"}
                        className={`dropdown-common`}
                        options={countries?.data}
                        onChange={(data) => {
                          setFormData({
                            ...formData,
                            countryId: data.value,
                          });
                        }}
                        defaultValue={formData.countryId}
                        isSearchable
                      />
                    </div>
                  </div>
                  <div className="col-xl-4">
                    <div className="form-group">
                      <label className="form-label">
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
                            interestStatus: data.value,
                          });
                        }}
                        defaultValue={formData.interestStatus}
                        isSearchable={false}
                      />
                    </div>
                    <div className="form-group">
                      <label className="form-label">
                        {t("next_followup_date")}
                      </label>
                      <DatePickerComponent
                        className={"date-picker"}
                        date={selectedDate}
                        handleDateChange={(newDate) =>
                          setFormData({
                            ...formData,
                            followupDate: newDate.format("YYYY-MM-DD"),
                          })
                        }
                        isCalenderOpen={isCalenderOpen.nextDate}
                        openCalender={() =>
                          setIsCalenderOpen({
                            ...isCalenderOpen,
                            nextDate: true,
                          })
                        }
                        closeCalender={() =>
                          setIsCalenderOpen({
                            ...isCalenderOpen,
                            nextDate: false,
                          })
                        }
                        past={true}
                      />
                    </div>
                    <div className="form-group">
                      <label className="form-label">{t("lead_status")}</label>
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
                  </div>
                  <div className="col-xl-12">
                    <div className="form-group">
                      <label className="form-label">{t("description")}</label>
                      <textarea
                        name="description"
                        type="text"
                        className="form-control"
                        value={formData.description}
                        onChange={handleChange}
                      />
                    </div>
                  </div>
                </div>
                <button
                  type="submit"
                  className="Common_customBtn__2_PSp Common_primary__2pdY1 undefined btn btn-primary"
                >
                  {t("add_lead")}
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default AddLeadForm;
