import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";

const FaqsList = () => {
  const { t } = useTranslation();
  const [expanded, setExpanded] = useState(null);

  //----------------------------------------------- API ---------------------------------------------

  const faqs = ApiHook.CallTicketFaqs();

  const toggleAccordion = (index) => {
    if (expanded === index) {
      setExpanded(null);
    } else {
      setExpanded(index);
    }
  };

  return (
    <>
      <div className="page_head_top">{t("faqs")}</div>
      {faqs.data?.length > 0 ? (
        <div className="support_cnt_box">
          <div className="row">
            <div className="accordion">
              {faqs.data?.map((item, index) => (
                <div className="accordion-item" key={index}>
                  <button
                    id={`accordion-button-${index}`}
                    aria-expanded={expanded === index}
                    onClick={() => toggleAccordion(index)}
                  >
                    <span className="accordion-title">{item.question}</span>
                    <span className="icon" aria-hidden="true"></span>
                  </button>
                  <div
                    className={`accordion-content ${
                      expanded === index ? "expanded" : ""
                    }`}
                  >
                    <p>{item.answer}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      ) : (
        <div className="noSupportFaq">
          <img src="images/faq-no-data.png" />
          <div className="nodata-table-view-box-txt">Sorry no data found</div>
        </div>
      )}
    </>
  );
};

export default FaqsList;
