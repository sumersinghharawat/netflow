import React from "react";
import { useTranslation } from "react-i18next";

const CrmTiles = ({ text, count }) => {
  const { t } = useTranslation();

  return (
    <div className="col-xl-4 col-lg-4 col-md-4 col-sm-12 lead-ongoing">
      <div className="ewallet_top_qc_balance_box">
        <div className="row align-items-center">
          <div className="ewallet_top_qc_balance_box_cnt">
            <div className="ewallet_top_qc_balance_box_cnt_ico">
              <img src="/images/conversion.png" alt="" />
            </div>
            <div className="ewallet_top_qc_balance_box_cnt_cnt">
              <div className="ewallet_top_qc_balance_box_cnt_head">
                {t(text)}
              </div>
              <div className="box_amnt_dv">
                <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">
                  {count}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CrmTiles;
