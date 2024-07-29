import React, { useRef } from "react";
import { useEffect, useState } from "react";
import { useTranslation } from "react-i18next";
import { useSelector } from "react-redux";
import { useParams } from "react-router";
import { formatDate } from "../../utils/formateDate";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { ApiHook } from "../../hooks/apiHook";
import { useReactToPrint } from "react-to-print";

const RegisterComplete = () => {
  const { t } = useTranslation();
  const tableRef = useRef();
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const userSelectedCurrency = useSelector(
    (state) => state?.user?.selectedCurrency
  );
  const selectedLanguage = useSelector(
    (state) => state?.user?.selectedLanguage
  );
  const [successMessageShow, setSuccessMessageShow] = useState(true);
  const params = useParams()
  const username = params?.username
  const preview = ApiHook.CallLetterPreview(username);
  const user = preview?.data?.userData;
  const companyData = preview?.data?.companyData;
  const packageData = preview?.data?.productData;
  const welcomeContent = preview?.data?.welcomeLetter;
  const selectedLanguageId = selectedLanguage?.id;
  const selectedWelcomeContent = welcomeContent?.find(item => item.id === selectedLanguageId);
  const welcomeContentHtml = selectedWelcomeContent?.content || "";
  const date = preview?.data?.date;
  const closeAlert = () => {
    setSuccessMessageShow(false);
  };

  // Use useEffect to automatically call closeAlert after 3 seconds
  useEffect(() => {
    const timer = setTimeout(() => {
      closeAlert();
    }, 3000);

    // Clear the timer when the component unmounts or when successMessageShow changes to false
    return () => {
      clearTimeout(timer);
    };
  }, [successMessageShow]);

  const handlePrint = useReactToPrint({
    content: () => tableRef.current,
  });

  return (
    <>
      <div className="page_head_top">{t("registrationComplete")}</div>
      {successMessageShow && (
        <div
          className="alertNotification"
          style={{
            width: "100%",
            border: "1px solid #A377FF",
            background: "#D4CBFF",
            display: "inline-block",
            color: "#8349ff",
            padding: "8px 15px 8px 15px",
            borderRadius: "20px",
            display: "flex",
            alignItems: "center",
            justifyContent: "space-between",
            flexWrap: "wrap",
          }}
        >
          <p style={{ margin: "0" }}>{t("regCompleted")}</p>
          <div className="closeBtnIcon" onClick={closeAlert}>
            <i className="fa-solid fa-xmark"></i>
          </div>
        </div>
      )}
      <div className="ewallet_table_section" ref={tableRef}>
        <div className="ewallet_table_section_cnt">
          <div
            className="printBtnsec"
            style={{ textAlign: "end", marginBottom: "5px" }}
          >
            <button
              type="button"
              className="btn print-button"
              style={{ backgroundColor: "#954cea", color: "white" }}
              onClick={handlePrint}
            >
              {t("print")}
            </button>
          </div>
          <div className="table-responsive min-hieght-table">
            <div
              className="cmpnyAddressname"
              style={{ textAlign: "end", padding: "10px" }}
            >
              <p style={{ marginBottom: "5px" }}>{companyData?.name}</p>
              <p style={{ marginBottom: "0" }}>{companyData?.address}</p>
            </div>
            <table className="striped">
              <tbody>
                <tr>
                  <td>{t("username")}</td>
                  <td>{user?.username}</td>
                </tr>
                <tr>
                  <td>{t("fullName")}</td>
                  <td>{user?.fullName}</td>
                </tr>
                <tr>
                  <td>{t("sponsor")}</td>
                  <td>{user?.sponsorName}</td>
                </tr>
                <tr>
                  <td>{t("regFee")}</td>
                  <td>
                    {userSelectedCurrency?.symbolLeft} {CurrencyConverter(preview?.data?.regFee,conversionFactor)}
                  </td>
                </tr>
                {packageData && (
                  <>
                    <tr>
                      <td>{t("package")}</td>
                      <td>{packageData?.name}</td>
                    </tr>
                    <tr>
                      <td>{t("packageAmount")}</td>
                      <td>
                        {userSelectedCurrency?.symbolLeft} {CurrencyConverter(packageData?.price,conversionFactor)}
                      </td>
                    </tr>
                  </>
                )}
                <tr>
                  <td>{t("totalAmount")}</td>
                  <td>
                    {userSelectedCurrency?.symbolLeft} {CurrencyConverter(preview?.data?.totalAmount,conversionFactor)}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <div className="container-fluid">
            <div
              className="regardssec"
              style={{
                display: "inline-block",
                width: "100%",
                marginTop: "10px",
              }}
            >
              <div
                className="regardBg"
                style={{
                  backgroundColor: "#e9f4f9",
                  borderRadius: "5px",
                  padding: "15px",
                  width: "100%",
                  height: "auto",
                }}
              >
                <p style={{ marginBottom: "5px" }}>
                <div dangerouslySetInnerHTML={{ __html: welcomeContentHtml }} />
                </p>
                <p>{t("wishingRegards")}</p>
                <p>{t("admin")}</p>
                <p>{companyData?.name}</p>
                <p style={{ marginBottom: "0" }}>{t("date")}</p>
                <p>{formatDate(date)}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default RegisterComplete;
