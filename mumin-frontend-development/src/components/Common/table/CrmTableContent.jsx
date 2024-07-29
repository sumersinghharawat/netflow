import React from "react";
import { formatDate } from "../../../utils/formateDate";
import { Link } from "react-router-dom";
import { useTranslation } from "react-i18next";

const CrmTableContent = ({
  data,
  headers,
  setShowLeadEditModal,
  setShowAddLeadModal,
  setShowNextFollowUp,
  type,
  handleEditClick,
}) => {
  const { t } = useTranslation();
  const ProgressBar = ({ value, color }) => {
    return (
      <div className="progress">
        <div
          className="progress-bar progress-bar-striped"
          role="progressbar"
          style={{ width: `${value}%`, backgroundColor: color }}
          aria-valuenow={value}
          aria-valuemin="0"
          aria-valuemax="100"
        >
          <b>{`${value}%`}</b>
        </div>
      </div>
    );
  };
  const handleAddLead = (id) => {
    setShowAddLeadModal(true);
    handleEditClick(id);
  };
  const handleEditLead = (data) => {
    setShowLeadEditModal(true);
    handleEditClick(data);
  };
  const handleNextFollowUp = (data) => {
    setShowNextFollowUp(true);
    handleEditClick(data);
  };
  return (
    <table className="table follow-up-table">
      <thead>
        <tr className="th" align="center">
          {headers?.map((header, index) => (
            <th key={index}>{header}</th>
          ))}
        </tr>
      </thead>
      <tbody>
        {data?.length > 0 ? (
          data?.map((data, index) => (
            <tr key={index}>
              <td>{index + 1}</td>
              {data.firstName !== null ? (
                <td>{data.firstName !== null ? data.firstName : ""}</td>
              ) : (
                <td></td>
              )}
              {data.fullName && (
                <td>{data.fullName !== null ? data.fullName : ""}</td>
              )}
              {data.lastName !== null ? (
                <td>{data.lastName !== null ? data.lastName : ""}</td>
              ) : (
                <td></td>
              )}
              {data.skypeId !== null ? (
                <td>{data.skypeId !== null ? data.skypeId : ""}</td>
              ) : (
                <td></td>
              )}
              {data.leadCompletion && (
                <td>
                  <ProgressBar
                    value={data.leadCompletion.leadCompletion}
                    color={data.leadCompletion.colour}
                  />
                </td>
              )}
              {data.dateAdded && (
                <td>
                  {formatDate(data.dateAdded !== null ? data.dateAdded : "")}
                </td>
              )}
              {data.email && <td>{data.email !== null ? data.email : ""}</td>}
              <td>
                <Link
                  onClick={() => handleEditLead(data?.id)}
                  data-bs-toggle="modal"
                  data-bs-target="#followup"
                >
                  <i className="fa-regular fa-pen-to-square"></i>
                </Link>
              </td>

              <td>
                <Link
                  onClick={() => handleAddLead(data.id)}
                  data-bs-toggle="modal"
                  data-bs-target="#addfollowup"
                >
                  <i className="fa-solid fa-square-plus"></i>
                </Link>
              </td>

              {type === "recent" && (
                <td>
                  <Link
                    onClick={() => handleNextFollowUp(data.id)}
                    data-bs-toggle="modal"
                    data-bs-target="#nextFollowUp"
                  >
                    <i className="fa-solid fa-circle-up"></i>
                  </Link>
                </td>
              )}
              <td>
                <Link to={`/crm-timeline/${data.id}`}>
                  <i className="fa-solid fa-book"></i>
                </Link>
              </td>
            </tr>
          ))
        ) : (
          <tr>
            <td colSpan="11" align="center">
              {t("noDataFound")}
            </td>
          </tr>
        )}
      </tbody>
    </table>
  );
};

export default CrmTableContent;
