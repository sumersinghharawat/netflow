import React, { useRef, useState } from "react";
import ProfileDetailsTab from "./ProfileDetailsTab";
import ContactDetailsTab from "./ContactDetailsTab";
import BankDetailsTab from "./BankDetailsTab";
import PaymentDetailsTab from "./PaymentDetailsTab";
// import SettingsTab from "./SettingsTab";
import { useTranslation } from "react-i18next";
import AdditionalDetails from "./AdditionalDetailsTab";

const ProfileLayout = ({ profile }) => {
  const { t } = useTranslation();
  const [activeTab, setActiveTab] = useState("firstTab");
  const containerRef = useRef(null);
  const handleTabClick = (tabId) => {
    setActiveTab(tabId);
  };

  return (
    <div className="profileTabSec">
      <div className="profileTabBg">
        <div className="tab">
          <button
            className={`tablinks ${activeTab === "firstTab" ? "active" : ""}`}
            onClick={() => handleTabClick("firstTab")}
          >
            {t("personalDetails")}
          </button>
          <button
            className={`tablinks ${activeTab === "secondTab" ? "active" : ""}`}
            onClick={() => handleTabClick("secondTab")}
          >
            {t("contactDetails")}
          </button>
          <button
            className={`tablinks ${activeTab === "thirdTab" ? "active" : ""}`}
            onClick={() => handleTabClick("thirdTab")}
          >
            {t("bankDetails")}
          </button>
          <button
            className={`tablinks ${activeTab === "fourthTab" ? "active" : ""}`}
            onClick={() => handleTabClick("fourthTab")}
          >
            {t("paymentDetails")}
          </button>
          {profile?.additionalDetails?.length > 0 && (
            <button
              className={`tablinks ${activeTab === "fifthTab" ? "active" : ""}`}
              onClick={() => handleTabClick("fifthTab")}
            >
              {t("additionalDetails")}
            </button>
          )}
          {/* <button
            className={`tablinks ${activeTab === "fifthTab" ? "active" : ""}`}
            onClick={() => handleTabClick("fifthTab")}
          >
            {t("settings")}
          </button> */}
        </div>
        <div ref={containerRef}></div>
        {activeTab === "firstTab" && <ProfileDetailsTab />}
        {activeTab === "secondTab" && (
          <ContactDetailsTab
            contact={profile?.contactDetails}
            countries={profile?.countries}
          />
        )}
        {activeTab === "thirdTab" && (
          <BankDetailsTab bank={profile?.bankDetails} />
        )}
        {activeTab === "fourthTab" && (
          <PaymentDetailsTab payment={profile?.payoutDetails} />
        )}
        {activeTab === "fifthTab" && (
          <AdditionalDetails additional={profile?.additionalDetails} />
        )}
        {/* {activeTab === 'fifthTab' && (
                    <SettingsTab
                    settings={profile?.payoutDetails} 
                    isEditModeEnabled={isEditModeEnabled}  
                    toggleEditMode={toggleEditMode} />
                )} */}
      </div>
    </div>
  );
};

export default ProfileLayout;
