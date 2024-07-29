import React, { useEffect, useRef } from "react";
import { useTranslation } from "react-i18next";
import { NavLink } from "react-router-dom";
import { ApiHook } from "../../hooks/apiHook";
import Skeleton from "react-loading-skeleton";

const UserProfileAvatar = ({ profile, moduleStatus, userKyc }) => {
  const { t } = useTranslation();
  const progressBarRef = useRef(null);

  //------------------------------------ API ---------------------------------
  const updateAvatarMutation = ApiHook.CallUpdateProfilePicture();
  const deleteProfileMutation = ApiHook.CallDeleteProfileAvatar();

  useEffect(() => {
    const strokeDashOffsetValue =
      100 - (profile?.productValidity?.packageValidityPercentage ?? 100);
    progressBarRef.current.style.strokeDashoffset = strokeDashOffsetValue;
  }, [profile?.productValidity?.packageValidityPercentage]);

  const handleImageChange = (event) => {
    const selectedFile = event.target.files[0];
    if (selectedFile) {
      event.preventDefault();
      updateAvatarMutation.mutate(selectedFile);
    }
  };

  const deleteProfilePicture = () => {
    deleteProfileMutation.mutate();
  };

  return (
    <div className="col-lg-3 col-md-12 borderPofileStyle">
      <div className="rightSide_top_user_dropdown">
        <div className="rightSide_top_user_dropdown_avatar_sec">
          <div className="profileEditBar">
            <button
              style={{ background: "none", border: "none", cursor: "pointer" }}
              onClick={() => document.getElementById("fileInput").click()}
            >
              <i className="fa-solid fa-pen-to-square"></i>
              <input
                type="file"
                id="fileInput"
                style={{ display: "none" }}
                onChange={handleImageChange}
              />
            </button>
          </div>
          <div className="deletIcon" style={{}} onClick={deleteProfilePicture}>
            <a style={{ textDecoration: "none" }}>
              <i className="fa-solid fa-trash"></i>
              <input type="file" id="fileInput" style={{ display: "none" }} />
            </a>
          </div>
          {moduleStatus?.kyc_status === 0 && (
            <div
              className="kyc_vrfd profileKycVerified"
              style={{ width: "25px" }}
            >
              <img src="/images/kyc_vrfd.svg" alt="" />
            </div>
          )}
          {moduleStatus?.kyc_status === 1 && (
            <div className="kyc_vrfd profileKycVerified">
              {userKyc ? (
                <img src="/images/kyc_vrfd.svg" alt="" />
              ) : (
                <img src="/images/kyc_not_vrfd.png" alt="" />
              )}
            </div>
          )}
          <div className="rightSide_top_user_dropdown_avatar_extra_padding avatarProfileStyle">
            <img
              src={
                profile?.avatar ? profile?.avatar : "/images/user-profile.png"
              }
              alt=""
            />
            <svg
              className="profile_progress avatarProfileProgress"
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
        <div className="profileAvatarnameSec">
          {profile ? (
            <>
              <h4>{profile?.fullName}</h4>
              <p>{profile?.username}</p>
            </>
          ) : (
            <>
              <Skeleton width="70%" />
              <Skeleton count={0.5} />
            </>
          )}
          {moduleStatus?.kyc_status === 1 && (
            <div className="kycDetailProfile">
              <div className="kycDetailProfile_cnt">
                <h6>{t("kyc")}</h6>
                <h6
                  style={{
                    color: profile?.kycStatus === 1 ? "#008000" : "#FF0000",
                  }}
                >
                  {userKyc === 1 ? t("verified") : t("notVerified")}
                </h6>
              </div>
              <NavLink to={"/kyc-details"} className="kyc_more_info_btn">
                {t("moreInfo")}
              </NavLink>
            </div>
          )}
        </div>
      </div>
    </div>
  );
};

export default UserProfileAvatar;
