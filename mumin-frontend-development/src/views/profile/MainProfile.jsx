import React, { useState } from "react";
import UserProfileAvatar from "../../components/Profile/UserProfileAvatar";
import UserProfileExtra from "../../components/Profile/UserProfileExtra";
import ChangePasswordModal from "../../components/Common/modals/ChangePasswordModal";
import ChangeTransPassModal from "../../components/Common/modals/ChangeTransPassModal";
import UserProfileTabs from "../../components/Profile/UserProfileTabs";
import { ApiHook } from "../../hooks/apiHook";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import RankViewModal from "../../components/Common/modals/RankViewModal";

const ProfileLayout = () => {
  const { t } = useTranslation();
  const [showPasswordModal, setShowPasswordModal] = useState(false);
  const [showTransPasswordModal, setShowTransPasswordModal] = useState(false);
  const [showRankView, setShowRankView] = useState(false);

  const handleClosePasswordModal = () => {
    setShowPasswordModal(!showPasswordModal);
  };
  const handleCloseTransPasswordModal = () => {
    setShowTransPasswordModal(!showTransPasswordModal);
  };
  const handleCloseRankView = () => {
    setShowRankView(!showRankView);
  };
  const Profile = ApiHook.CallProfile();
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );

  return (
    <>
      <div className="page_head_top">{t("profileView")}</div>
      <div className="profileBgBox">
        <div className="row align-items-center">
          <UserProfileAvatar
            profile={Profile?.data?.profile}
            moduleStatus={moduleStatus}
            userKyc={Profile?.data?.profile?.kycStatus}
          />
          <UserProfileExtra
            profile={Profile?.data?.profile}
            handleClosePasswordModal={handleClosePasswordModal}
            handleCloseTransPasswordModal={handleCloseTransPasswordModal}
            handleCloseRankView={handleCloseRankView}
            moduleStatus={moduleStatus}
          />
        </div>
      </div>
      <UserProfileTabs profile={Profile?.data} />
      <ChangePasswordModal
        showModal={showPasswordModal}
        onHide={handleClosePasswordModal}
        passwordPolicy={Profile?.data?.passwordPolicy}
      />
      <ChangeTransPassModal
        showModal={showTransPasswordModal}
        onHide={handleCloseTransPasswordModal}
        passwordPolicy={Profile?.data?.passwordPolicy}
      />
      <RankViewModal
        show={showRankView}
        handleClose={handleCloseRankView}
        data={Profile?.data?.profile?.rankDetails?.rankData}
        currentRank={Profile?.data?.profile?.rankDetails?.currentRank?.id}
      />
    </>
  );
};

export default ProfileLayout;
