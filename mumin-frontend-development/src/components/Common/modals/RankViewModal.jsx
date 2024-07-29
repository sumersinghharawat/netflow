import React from "react";
import { Modal } from "react-bootstrap";
import { useTranslation } from "react-i18next";

const RankViewModal = ({ show, handleClose, data, currentRank }) => {
  const { t } = useTranslation();
  return (
    <Modal show={show} onHide={handleClose}>
      <Modal.Header closeButton>
        <Modal.Title as={"h5"}>{t("rank")}</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <div className="rank-timeline">
          <div className="history-tl-container">
            <ul className="tl">
              {data?.map((item, index) => (
                <li
                  className={`tl-item ${
                    item.id === currentRank ? "active" : ""
                  }`}
                  key={index}
                >
                  <div className="item-title">{item.name}</div>
                  <div className="timestamp">
                    {item.criteria.map((criterion, i) => (
                      <p className="active" key={i}>
                        {`${t(criterion.name)}: ${
                          Array.isArray(criterion.value)
                            ? criterion.value.length !== 0
                              ? criterion.value
                                  .map((item) => `${item.label}: ${item.value}`)
                                  .join(", ")
                              : 0
                            : criterion.value
                        }`}
                        <span>{`${t('achieved')} : ${
                          Array.isArray(criterion.value)
                            ? criterion.value.length !== 0
                              ? criterion.value
                                  .map((item) => `${item.label}: ${item.value}`)
                                  .join(", ")
                              : 0
                            : criterion.value
                        }`}</span>
                      </p>
                    ))}
                  </div>
                </li>
              ))}
            </ul>
          </div>
        </div>
      </Modal.Body>
    </Modal>
  );
};

export default RankViewModal;
