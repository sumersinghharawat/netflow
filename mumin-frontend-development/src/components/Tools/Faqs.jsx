import React, { useState } from "react";
import anime from "animejs/lib/anime.es.js";
import { ApiHook } from "../../hooks/apiHook";

const Faqs = () => {
  const [activeAccordion, setActiveAccordion] = useState(null);
  const faqs = ApiHook.CallGetFaqs();
  const handleAccordionClick = (index) => {
    animateStepTransition(index);
    setActiveAccordion(index === activeAccordion ? null : index);
  };
  const animateStepTransition = (index) => {
    const containerElement = document.querySelector(`#animation${index}`);
    anime.set(containerElement, { opacity: 0, translateY: "10%" });
    anime({
      targets: containerElement,
      opacity: [0, 1],
      translateY: "-10%",
      duration: 500,
      easing: "easeInOutQuad",
    });
  };

  return (
    <>
      <div className="page_head_top">Faqs</div>

      <div className="faqAccordion">
        {faqs?.data?.length > 0 ? (
          <div className="accordion accordion-flush" id="accordionFlushExample">
            {faqs?.data?.map((item, index) => (
              <div className="accordion-item" key={index}>
                <h2 className="accordion-header" id={`flush-heading${index}`}>
                  <button
                    className={`accordion-button${
                      activeAccordion === index ? "" : " collapsed"
                    }`}
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target={`#flush-collapse${index}`}
                    aria-expanded={activeAccordion === index ? "true" : "false"}
                    aria-controls={`flush-collapse${index}`}
                    onClick={() => handleAccordionClick(index)}
                  >
                    {item?.question}
                  </button>
                </h2>
                <div
                  id={`flush-collapse${index}`}
                  className={`accordion-collapse collapse${
                    activeAccordion === index ? " show" : ""
                  }`}
                  aria-labelledby={`flush-heading${index}`}
                >
                  <div id={`animation${index}`}>
                    <div className="accordion-body">{item?.answer}</div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <>
          <img src="images/faq-no-data.png" alt="faq" />
          <div className="nodata-table-view-box-txt">Sorry no data found</div>
          </>
        )}
      </div>
    </>
  );
};

export default Faqs;
