import React, { useState } from "react";
import { Popover, OverlayTrigger } from "react-bootstrap";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";
import { Link } from "react-router-dom";

const ReplicaAndLeadLink = ({
  payoutTab,
  currency,
  replicaLink,
  leadCaptureLink,
  conversionFactor,
}) => {
  const { t } = useTranslation();
  const [activeTab, setActiveTab] = useState("replica");
  const [copied, setCopied] = useState(false);
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );

  const handleTabClick = (tab) => {
    setActiveTab(tab);
  };

  const copyToClipboard = async (text) => {
    if (navigator && navigator.clipboard) {
      try {
        await navigator.clipboard.writeText(text);
        setCopied(true);
        // Reset copied status after a delay (e.g., 2 seconds)
        setTimeout(() => {
          setCopied(false);
        }, 2000);
      } catch (error) {
        console.error("Clipboard copy failed:", error);
      }
    } else {
      // Clipboard API is not supported, provide a fallback method
      try {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("copy");
        document.body.removeChild(textArea);
        setCopied(true);
        // Reset copied status after a delay (e.g., 2 seconds)
        setTimeout(() => {
          setCopied(false);
        }, 2000);
      } catch (error) {
        console.error("Fallback clipboard copy failed:", error);
      }
    }
  };

  const renderPopover = (content) => (
    <Popover>
      <Popover.Body>{`${content}`}</Popover.Body>
    </Popover>
  );

  const renderSocialIcons = (links) => {
    return links?.map((icon, index) => (
      <React.Fragment key={index}>
        <OverlayTrigger
          trigger={["hover", "focus"]}
          placement="top"
          overlay={renderPopover(copied ? "Copied" : icon.name)}
        >
          {(icon?.name === "Replica Link" || icon?.name === "Lead Capture Link") ? (
            <div
              className="replica_link_sec_ico"
              onClick={() => copyToClipboard(icon.link)}
            >
              <img src={`images/${icon.icon}`} alt={icon.name} />
            </div>
          ) : (
            <a
              className="replica_link_sec_ico"
              target="_blank"
              href={icon.link}
            >
              <img src={`images/${icon.icon}`} alt={icon.name} />
            </a>
          )}
        </OverlayTrigger>
      </React.Fragment>
    ));
  };

  const renderPayoutRows = () => {
    return (
      <>
        <div className="dashboard_payout_right_2_cnt_row" key="1">
          <span>{t("requested")}</span>
          <strong>
            <span style={{ backgroundColor: "#3498db" }}>
              {currency?.symbolLeft}{" "}
              {CurrencyConverter(payoutTab?.payoutRequested, conversionFactor)}
            </span>
          </strong>
        </div>
        <div className="dashboard_payout_right_2_cnt_row" key="2">
          <span>{t("approved")}</span>
          <strong>
            <span style={{ backgroundColor: "#3e03ad" }}>
              {currency?.symbolLeft}{" "}
              {CurrencyConverter(payoutTab?.payoutApproved, conversionFactor)}
            </span>
          </strong>
        </div>
        <div className="dashboard_payout_right_2_cnt_row" key="3">
          <span>{t("paid")}</span>
          <strong>
            <span style={{ backgroundColor: "#03AD47" }}>
              {currency?.symbolLeft}{" "}
              {CurrencyConverter(payoutTab?.payoutPaid, conversionFactor)}
            </span>
          </strong>
        </div>
        <div className="dashboard_payout_right_2_cnt_row" key="4">
          <span>{t("rejected")}</span>
          <strong>
            <span style={{ backgroundColor: "#f00" }}>
              {currency?.symbolLeft}{" "}
              {CurrencyConverter(payoutTab?.payoutRejected, conversionFactor)}
            </span>
          </strong>
        </div>
      </>
    );
  };

  return (
    <div>
      <div className="replica_lead_btn_top">
        {!!moduleStatus?.replicated_site_status && (
          <Link
            className={`replica_lead_tab_btn ${
              activeTab === "replica" ? "active" : ""
            }`}
            onClick={() => handleTabClick("replica")}
          >
            {t("replica")}
          </Link>
        )}
        {!!moduleStatus?.lead_capture_status && (
          <Link
            className={`replica_lead_tab_btn ${
              activeTab === "leadcapture" ? "active" : ""
            }`}
            onClick={() => handleTabClick("leadcapture")}
          >
            {t("leadCapture")}
          </Link>
        )}
      </div>
      {!!moduleStatus?.replicated_site_status && activeTab === "replica" && (
        <div className="replica_link_sec">
          <div className="replica_link_sec_row">
            {renderSocialIcons(replicaLink)}
          </div>
        </div>
      )}
      {!!moduleStatus?.lead_capture_status && activeTab === "leadcapture" && (
        <div className="replica_link_sec">
          <div className="replica_link_sec_row">
            {renderSocialIcons(leadCaptureLink)}
          </div>
        </div>
      )}

      <div className="dashboard_payout_right_section_2">
        <div className="dashboard_payout_right_section_2_head">
          {t("payout")}
        </div>
        <div className="dashboard_payout_right_2_cnt">{renderPayoutRows()}</div>
      </div>
    </div>
  );
};

export default ReplicaAndLeadLink;
