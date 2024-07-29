import React from "react";
import { formatDateWithoutTime } from "../../utils/formateDate";
import { useTranslation } from "react-i18next";
import Skeleton from "react-loading-skeleton";
import "react-loading-skeleton/dist/skeleton.css";

const TeamMembers = ({ members }) => {
  const { t } = useTranslation();
  return (
    <div className="col-md-5">
      <div className="joinings_viewBox">
        <div className="joinings_viewBox_head">
          <h5>{t("teamMembers")}</h5>
        </div>
        <div className="teammbrs_cnt">
  {!members ? (
    <div className="teammbrs_cnt_row">
      <div className="teammbrs_cnt_img">
        <Skeleton
          width="45px"
          height="45px"
          circle
          containerClassName="avatar-skeleton"
          count={3}
        />
      </div>
      <div className="teammbrs_cnt_name_dtl">
        <div className="teammbrs_cnt_name">
          <Skeleton count={6} />
        </div>
      </div>
    </div>
  ) : members?.length === 0 ? (
    <div className="no-data-div">
      <div className="no-data-div-image">
        <img src="/images/nodata-image.png" alt="" />
      </div>
      <p>{t('noDataFound')}</p>
    </div>
  ) : (
    members.map((member, index) => (
      <div className="teammbrs_cnt_row" key={index}>
        <div className="teammbrs_cnt_img">
          <img
            src={
              member?.image
                ? member?.image
                : member?.gender === "F"
                ? "/images/team1.png"
                : member?.gender === "M"
                ? "/images/user-profile.png"
                : "/images/team3.png"
            }
            alt={member?.name}
          />
        </div>
        <div className="teammbrs_cnt_name_dtl">
          <div className="teammbrs_cnt_name">
            {member?.name} {member?.secondName}
          </div>
          <div className="teammbrs_cnt_date_id">
            <span>{member?.username}</span>
            <span>{formatDateWithoutTime(member?.dateOfJoining)}</span>
          </div>
        </div>
      </div>
    ))
  )}
</div>

      </div>
    </div>
  );
};

export default TeamMembers;
