import React, { useEffect, useRef } from "react";
import { useTranslation } from "react-i18next";
import { useSelector } from "react-redux";
import { NavLink } from "react-router-dom";

function UserDropdown({ props }) {
  const { t } = useTranslation();
  const progressBarRef = useRef(null);
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );

  useEffect(() => {
    const strokeDashOffsetValue =
      100 - (props?.productValidity?.packageValidityPercentage ?? 100);
    progressBarRef.current.style.strokeDashoffset = strokeDashOffsetValue;
  }, [props?.productValidity?.packageValidityPercentage]);

  return (
    <>
      <aside className="left_sidebar"></aside>
      <div className="rightSide_top_user_dropdown">
        <div className="rightSide_top_user_dropdown_avatar_sec">
          <div className="rightSide_top_user_dropdown_avatar">
            <img
              src={props?.image ? props?.image : "/images/user-profile.png"}
              alt=""
            />
            {moduleStatus?.kyc_status === 1 ? (
              <div className="kyc_vrfd">
                {props?.kycStatus ? (
                  <img src="/images/kyc_vrfd.svg" alt="" />
                ) : (
                  <img src="/images/kyc_not_vrfd.png" alt="" />
                )}
              </div>
            ) : (
              <div className="kyc_vrfd">
                <img src="/images/kyc_vrfd.svg" alt="" />
              </div>
            )}
            <svg
              className="profile_progress"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="-1 -1 34 34"
            >
              <circle
                cx="16"
                cy="16"
                r="15.9155"
                className="progress-bar__background"
              />
              <circle
                cx="16"
                cy="16"
                r="15.9155"
                className="progress-bar__progress js-progress-bar"
                ref={progressBarRef}
              />
            </svg>
          </div>
        </div>
        <div className="rightSide_top_user_dropdown_nameBOx">
          <div className="rightSide_top_user_dropdown_name">
            {props?.fullname}
          </div>
          <div className="rightSide_top_user_dropdown_id">
            {props?.username}
          </div>
          {moduleStatus?.product_status === 1 && (
            <div className="rightSide_top_user_dropdown_package">
              {props?.packageName}
            </div>
          )}
        </div>
      </div>
      <div className="top_right_personal_dtl_box_sec">
        <div className="top_right_personal_dtl_box border-sprt">
          <span>{t("personalPV")}</span>
          <strong>{props?.personalPv ?? 0}</strong>
        </div>
        <div className="top_right_personal_dtl_box">
          <span>{t("groupPV")}</span>
          <strong>{props?.groupPv ?? 0}</strong>
        </div>
      </div>
      <div className="top_right_personal_dtl_box_sec">
        <div className="top_right_personal_dtl_box text-center">
          <span>{t("sponsor")}</span>
          <strong>{props?.sponsorName}</strong>
        </div>
      </div>
      <div className="top_profile_upgrade_renew_btn_sec">
        {(moduleStatus?.product_status || moduleStatus?.ecom_status) && (
          <>
            {moduleStatus?.package_upgrade === 1 && (
              <NavLink
                to="/upgrade"
                className="top_profile_upgrade_renew_btn_1"
              >
                {t("upgrade")}
              </NavLink>
            )}
            {moduleStatus?.subscription_status === 1 && (
              <NavLink to="/renew" className="top_profile_upgrade_renew_btn_1">
                {t("renew")}
              </NavLink>
            )}
          </>
        )}
      </div>
    </>
  );
}

export default UserDropdown;
