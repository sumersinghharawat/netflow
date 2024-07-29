import React from "react";
import { Modal } from "react-bootstrap";
import { useTranslation } from "react-i18next";

const RegisterModal = ({ show, handleClose, data }) => {
  const { t } = useTranslation();
  return (
    <Modal show={show} onHide={handleClose} centered>
      <Modal.Header closeButton>
        <Modal.Title>{t('termsAndCondition')}</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        {data}
      </Modal.Body>
    </Modal>
  );
};

export default RegisterModal;
