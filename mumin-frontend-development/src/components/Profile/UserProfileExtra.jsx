import React from "react";
import { useSelector } from "react-redux";
import { formatDate } from "../../utils/formateDate";
import { useTranslation } from "react-i18next";
import Skeleton from "react-loading-skeleton";
import { NavLink } from "react-router-dom";

const UserProfileExtra = ({
  profile,
  handleClosePasswordModal,
  handleCloseTransPasswordModal,
  handleCloseRankView,
  moduleStatus,
}) => {
  const { t } = useTranslation();
  const plan = useSelector(
    (state) => state?.dashboard?.appLayout?.moduleStatus?.mlm_plan
  );

  return (
    <div className="col-lg-9 col-md-12 border-prf-left">
      <div className="profDetailuserDtl">
        <div>
          <h5>{t("email")}</h5>
          {profile?.email ? <p>{profile?.email}</p> : <Skeleton width={200} />}
          {/* email verification need check with BE */}
          {moduleStatus?.kyc_status === 1 &&
            (moduleStatus?.kyc_status === 1 ? (
              <p className="text-success">{t("verified")} </p>
            ) : (
              <p className="text-danger">{t("notVerified")} </p>
            ))}
        </div>
        <div>
          <h5>{t("resetPassword")}</h5>
          <p>*************</p>
          <div className="chngPassBtn">
            <button
              type="button"
              className="btn btn-change"
              data-bs-toggle="modal"
              onClick={handleClosePasswordModal}
            >
              {t("resetPassword")}
            </button>
          </div>
        </div>
        <div>
          <h5>{t("resetTransactionPassword")}</h5>
          <p>*************</p>
          <div className="chngPassBtn">
            <button
              type="button"
              className="btn btn-change"
              data-bs-toggle="modal"
              onClick={handleCloseTransPasswordModal}
            >
              {t("resetTransactionPassword")}
            </button>
          </div>
        </div>
        {!!moduleStatus?.rank_status && (
          <div>
            <h5>{t("current_ranking")}</h5>
            {profile?.rankDetails?.currentRank !== null ? (
              <p>
                {profile?.rankDetails?.currentRank?.name ?? (
                  <Skeleton width={200} />
                )}
                <a
                  href="#"
                  data-bs-toggle="modal"
                  type="button"
                  onClick={handleCloseRankView}
                >
                  <i className="fa-solid fa-eye view_rank_dtl"></i>
                </a>
              </p>
            ) : (
              <p>{t("no_rank_achieved")}</p>
            )}
          </div>
        )}
      </div>
      <div className="packageTypesNames">
        <div className="row">
          <div className="col-md-6">
            <div className="packageNames">
              <div className="sponserType">
                <h5>{t("sponsor")}</h5>
                {profile ? <p>{profile?.sponsor}</p> : <Skeleton width={175} />}
              </div>
              <div className="placementType">
                <h5>{t("placement")}</h5>
                {profile?.father ? <p>{profile?.father}</p> : <Skeleton />}
              </div>
              {plan === "Binary" && (
                <div className="positionType">
                  <h5>{t("position")}</h5>
                  {profile?.position ? (
                    <p>{profile?.position}</p>
                  ) : (
                    <Skeleton />
                  )}
                </div>
              )}
            </div>
          </div>
          <div className="col-md-6">
            <div className="row">
              {!!moduleStatus?.product_status && (
                <div className="col-md-6">
                  <div className="PackageDetailProfile">
                    <h5>{t("package")}</h5>
                    {profile?.package?.name ? (
                      <h6>{profile?.package?.name}</h6>
                    ) : (
                      <Skeleton />
                    )}
                    {!!moduleStatus?.package_upgrade && (
                      <NavLink type="button" className="btn" to="/upgrade">
                        {t("upgrade")}
                      </NavLink>
                    )}
                  </div>
                </div>
              )}
              {!!moduleStatus?.subscription_status && (
                <div className="col-md-6">
                  <div className="expiryDetailProfile">
                    <h5>{t("expiry")}</h5>
                    {profile?.productValidity?.productValidityDate ? (
                      <h6>
                        {formatDate(
                          profile?.productValidity?.productValidityDate
                        )}
                      </h6>
                    ) : (
                      <Skeleton />
                    )}
                    <NavLink
                      type="button"
                      className="btn"
                      to="/renew"
                    >
                      {t("renew")}
                    </NavLink>
                  </div>
                </div>
              )}
            </div>
          </div>
        </div>
      </div>
      <div className="profileStatusSec">
        <div className="profileStatusBg">
          <div className="profileStatusContDetailSec">
            <div className="profileStatusIconBg">
              <i className="fa-solid fa-user" style={{ color: "#5e28fb" }}></i>
            </div>
            <div className="statusnameCount">
              <h6>{t("personalPV")}</h6>
              {profile?.pv !== null && profile?.pv !== undefined ? (
                <p>{profile?.pv}</p>
              ) : (
                <Skeleton />
              )}
            </div>
          </div>
          <div className="profileStatusContDetailSec">
            <div className="profileStatusIconBgtwo">
              <i
                className="fa-solid fa-user-group"
                style={{ color: "#7e6711" }}
              ></i>
            </div>
            <div className="statusnameCount">
              <h6>{t("groupPV")}</h6>
              {profile?.gpv !== null && profile?.gpv !== undefined ? (
                <p>{profile?.gpv}</p>
              ) : (
                <Skeleton />
              )}
            </div>
          </div>
          {plan === "Binary" && (
            <>
              <div className="profileStatusContDetailSec">
                <div className="profileStatusIconBgthree">
                  <i
                    className="fa-solid fa-arrow-left"
                    style={{ color: "#2c628a" }}
                  ></i>
                </div>
                <div className="statusnameCount">
                  <h6>{t("leftCarry")}</h6>
                  {profile?.leftCarry !== null &&
                  profile?.leftCarry !== undefined ? (
                    <p>{profile?.leftCarry}</p>
                  ) : (
                    <Skeleton />
                  )}
                </div>
              </div>
              <div className="profileStatusContDetailSec">
                <div className="profileStatusIconBgfour">
                  <i
                    className="fa-solid fa-arrow-right"
                    style={{ color: "#207b70" }}
                  ></i>
                </div>
                <div className="statusnameCount">
                  <h6>{t("rightCarry")}</h6>
                  {profile?.rightCarry !== null &&
                  profile?.rightCarry !== undefined ? (
                    <p>{profile?.rightCarry}</p>
                  ) : (
                    <Skeleton />
                  )}
                </div>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
};

export default UserProfileExtra;
