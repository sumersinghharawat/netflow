import React, { useEffect, useRef } from "react";
import { useTranslation } from "react-i18next";
import { Link, useParams } from "react-router-dom";
import { ApiHook } from "../../hooks/apiHook";
import { formatDateWithoutTime } from "../../utils/formateDate";

const TicketTimeline = () => {
  const { t } = useTranslation();
  const param = useParams();
  const timeline = ApiHook.CallTicketTimeline(param.trackId);
  const itemsRef = useRef([]);

  useEffect(() => {
    const items = itemsRef.current;

    function isElementInViewport(el) {
      const rect = el.getBoundingClientRect();
      return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <=
          (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <=
          (window.innerWidth || document.documentElement.clientWidth)
      );
    }

    function handleScroll() {
      items.forEach((item) => {
        if (isElementInViewport(item)) {
          item.classList.add("in-view");
        } else {
          item.classList.remove("in-view");
        }
      });
    }

    handleScroll(); // Check initially when the component mounts

    // Event listener for scroll
    window.addEventListener("scroll", handleScroll);

    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, [timeline]);

  return (
    <>
      <div className="page_head_top">{t("timeline")}</div>
      <div className="support_cnt_box">
        <section className="timeline">
          <h4>
            <Link
              to={"/support-center"}
              className="back_btn"
              style={{ marginRight: "25px" }}
            >
              <i className="fa-solid fa-arrow-left"></i>
            </Link>
            {`${t("ticket")} : ${param.trackId}`}
          </h4>
          <ul>
            {timeline.data?.TicketActivity?.map((item, index) => (
              <li key={index} ref={(el) => (itemsRef.current[index] = el)}>
                <div>
                  <time>{formatDateWithoutTime(item.date)}</time>
                  <h5>{item.activity}</h5>
                  <p>{item.comment}</p>
                </div>
              </li>
            ))}
          </ul>
        </section>
      </div>
    </>
  );
};

export default TicketTimeline;
