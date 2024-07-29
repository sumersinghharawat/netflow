import React from "react";
import { useTranslation } from "react-i18next";
import { NavLink } from "react-router-dom";

const TicketHeader = () => {
  const { t } = useTranslation();

    return (
        <div className="tree_view_top_filter_bar mt-2 hide_show_mobile">
        <div className="row justify-content-between">
          <div className="col-md-8 hide_show_mobile">
            <NavLink to="/support-center" className="btn_ewallt_page">
              {t('my_ticket')}
            </NavLink>
            <NavLink to="/create-ticket" className="btn_ewallt_page">
              {t('create_ticket')}
            </NavLink>
            <NavLink to="/support-faqs" className="btn_ewallt_page">
              {t('faqs')}
            </NavLink>
          </div>
        </div>
      </div>
    )
}

export default TicketHeader