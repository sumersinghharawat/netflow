import React, { useState } from "react";
import CrmTimeline from "./CrmTimeline";
import { useParams } from "react-router";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
import LeadEditModal from "../Common/modals/LeadEditModal";
import { crmFormateDate } from "../../utils/formateDate";

const LeadsDetails = () => {
  const params = useParams();

  //----------------------------------------- API ------------------------------------------
  const { t } = useTranslation();
  // state
  const [showLeadEditModal, setShowLeadEditModal] = useState(false);
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
  // api
  const details = ApiHook.CallLeadDetails(params.id);
  // functions
  const handleEditLead = (item) => {
    setShowLeadEditModal(!showLeadEditModal);
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
      leadStatus: item.leadStatus
    }))
  };
  const getPercentage = (keyValue) => {
    return details.data?.leadCompletionValues[keyValue];
  };

  return (
    <>
      <CrmTimeline />
      <div className="crm-time-line-sec">
        <div className="time-line-form joinings_viewBox">
          <div className="progress">
            <div
              className="progress-bar progress-bar-striped"
              role="progressbar"
              style={{
                width: `${details.data?.leadCompletion?.leadCompletion}%`,
                backgroundColor: `${details.data?.leadCompletion?.colour}`,
              }}
              aria-valuenow={details.data?.leadCompletion?.leadCompletion}
              aria-valuemin="0"
              aria-valuemax="100"
            >
              <b>{`${details.data?.leadCompletion?.leadCompletion}%`}</b>
            </div>
          </div>
          <table className="time-line-table">
            <tbody>
              {details &&
                details.data &&
                details.data.details &&
                Object.entries(details.data.details).map(
                  ([key, value]) =>
                    // Conditionally render based on the key
                    key !== "countryId" &&
                    key !== "id" && (
                      <tr key={key}>
                        <td>{t(key)}</td>
                        {key === "interestStatus" ? (
                          <>
                            {value === 0 ? (
                              <td>{t("very_interested")}</td>
                            ) : null}
                            {value === 1 ? <td>{t("interested")}</td> : null}
                            {value === 2 ? (
                              <td>{t("not_that_interested")}</td>
                            ) : null}
                          </>
                        ) : (
                          <>
                            {key === "leadStatus" ? (
                              <>
                                {value === 0 ? <td>{t("rejected")}</td> : null}
                                {value === 1 ? <td>{t("ongoing")}</td> : null}
                                {value === 2 ? <td>{t("accepted")}</td> : null}
                              </>
                            ) : (
                              <td>
                                {value !== null ||
                                key === "followupDate" ||
                                key === "nextFollowupDate" ? (
                                  value
                                ) : (
                                  <>
                                    {t("NA")}
                                    <a
                                      className="btn btn-warning btn-sm"
                                      data-bs-toggle="modal"
                                      data-bs-target="#followup"
                                      onClick={() =>
                                        handleEditLead(details.data?.details)
                                      }
                                    >
                                      {`Add Lead's ${t(
                                        key
                                      )} to get ${getPercentage(key)}%`}
                                    </a>
                                  </>
                                )}
                              </td>
                            )}
                          </>
                        )}
                      </tr>
                    )
                )}
            </tbody>
          </table>
        </div>
      </div>
      <LeadEditModal
        showLeadEditModal={showLeadEditModal}
        setShowLeadEditModal={setShowLeadEditModal}
        editFormData={editFormData}
        setEditFormData={setEditFormData}
        errorMessage={errorMessage}
        setErrorMessage={setErrorMessage}
        countries={details.data?.countries}
      />
    </>
  );
};

export default LeadsDetails;
