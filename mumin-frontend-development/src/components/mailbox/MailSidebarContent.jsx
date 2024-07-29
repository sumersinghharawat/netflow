import React from "react";
import { useTranslation } from "react-i18next";
import { Link } from "react-router-dom";

const MailSidebarContent = (props) => {
  const { t } = useTranslation();
  return (
    <>
      <div className="menu-bar">
        <ul className="menu-items">
          <li className="compose mb-3">
            <button
              data-bs-toggle="offcanvas"
              data-bs-target="#composemail"
              aria-controls="composemail"
              className="btn btn-primary btn-block"
              onClick={() => props.setShowCompose(true)}
            >
              <svg
                fill="currentColor"
                viewBox="0 0 16 16"
                height="1em"
                width="1em"
                style={{ marginRight: "8px" }}
              >
                <path d="M12.146.146a.5.5 0 01.708 0l3 3a.5.5 0 010 .708l-10 10a.5.5 0 01-.168.11l-5 2a.5.5 0 01-.65-.65l2-5a.5.5 0 01.11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 01.5.5v.5h.5a.5.5 0 01.5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 015 12.5V12h-.5a.5.5 0 01-.5-.5V11h-.5a.5.5 0 01-.468-.325z" />
              </svg>
              {t("compose")}
            </button>
          </li>
          <li
            className={props?.activeTab === "inbox" ? "active" : ""}
            onClick={() => props?.handleTabChange("inbox")}
          >
            <Link>
              <i className="fa-regular fa-envelope-open"></i> {t("inbox")}
            </Link>
            {props.mailData?.inboxUnreadCount !== 0 && (
              <span className="badge badge-pill bg-success">
                {props.mailData?.inboxUnreadCount}
              </span>
            )}
          </li>
          <li
            className={props?.activeTab === "sent" ? "active" : ""}
            onClick={() => props?.handleTabChange("sent")}
          >
            <Link>
              <i className="fa-regular fa-paper-plane"></i> {t("sent")}
            </Link>
          </li>
          <li
            className={props?.activeTab === "adminInbox" ? "active" : ""}
            onClick={() => props?.handleTabChange("adminInbox")}
          >
            <Link>
              <i className="fa-regular fa-envelope-open"></i> {t("admin_inbox")}
            </Link>
          </li>
          <li
            className={props?.activeTab === "replicaInbox" ? "active" : ""}
            onClick={() => props?.handleTabChange("replicaInbox")}
          >
            <Link>
              <i className="fa-regular fa-envelope-open"></i>{" "}
              {t("replica_inbox")}
            </Link>
          </li>
        </ul>
      </div>
    </>
  );
};

export default MailSidebarContent;
