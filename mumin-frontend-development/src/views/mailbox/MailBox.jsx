import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import MailboxComponent from "../../components/mailbox/MailboxComponent";
import { Link } from "react-router-dom";

const MailBox = () => {
  const { t } = useTranslation();
  const [showMobileNav, setShowMobileNav] = useState(false);
  return (
    <>
      <div
        className={`page_head_top mail_box_top_hx ${
          showMobileNav ? "show_mailbox_sidebar_ico" : ""
        } `}
        style={{ cursor: "pointer" }}
        onClick={() => setShowMobileNav(!showMobileNav)}
      >
        {t("mailBox")}
        <Link>
          <i className="fa-solid fa-bars"></i>
        </Link>
      </div>
      <MailboxComponent showMobileNav={showMobileNav} />
    </>
  );
};

export default MailBox;
