import React from "react";
import CrmTimeline from "./CrmTimeline";
import { useParams } from "react-router";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";

const LeadsHistory = () => {
  const params = useParams();
  const { t } = useTranslation();
  //------------------------------------------------ API -------------------------------------------------
  const timeLines = ApiHook.CallCrmTimeLine(params?.id);

  return (
    <>
      <CrmTimeline />
      <div className="crm-time-line-sec">
        <div className="time-line-form joinings_viewBox">
          <div className="progress">
            <div
              className="progress-bar progress-bar-striped bg-success"
              role="progressbar"
              style={{
                width: `${timeLines.data?.leadCompletion?.leadCompletion}%`,
                backgroundColor: `${timeLines.data?.leadCompletion?.color}`,
              }}
              aria-valuenow={timeLines.data?.leadCompletion?.leadCompletion}
              aria-valuemin="0"
              aria-valuemax="100"
            >
              <b>{`${timeLines.data?.leadCompletion?.leadCompletion}%`}</b>
            </div>
          </div>
          <ul className="timeline">
            {timeLines?.data?.firstEntry && (
              <li>
                <div className="direction-r">
                  <div className="flag-wrapper">
                    <span className="flag">
                      <div className="number">
                        <p>{1}</p>
                      </div>
                      <div className="head">{`Introduced to ${timeLines.data?.companyName} by ${timeLines.data?.addedBy}`}</div>
                    </span>
                  </div>
                  <div class="desc">{timeLines.data?.description}</div>
                </div>
              </li>
            )}
            {timeLines?.data?.followups?.length !== 0 &&
              timeLines?.data?.followups?.map((item, index) => (
                <li key={index}>
                  <div className={item.direction}>
                    <div className="flag-wrapper">
                      <span className="flag">
                        <div className="number">
                          <p>{index + 2}</p>
                        </div>
                        <div className="head">{`Followup added on ${item?.createdAt}`}</div>
                      </span>
                    </div>
                    <div className="head">{item.description}</div>
                    <div class="desc">{`${t(
                      "followup_date"
                    )} : ${item.followupDate}`}</div>
                  </div>
                </li>
              ))}
          </ul>
        </div>
      </div>
    </>
  );
};

export default LeadsHistory;
