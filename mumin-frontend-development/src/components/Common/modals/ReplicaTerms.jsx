import React from "react";
import { Modal, Button } from "react-bootstrap";

function TermsAndConditionReplicaModal({ show, setShow, data }) {
  return (
    <Modal
      show={show}
      onHide={() => setShow(false)}
      size="lg"
      dialogClassName="custom-modal"
    >
      <Modal.Header closeButton className="border-0">
        <Modal.Title className="text-center">Terms And Condition</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <div className="popup_cnt_sec">
          {data && <div dangerouslySetInnerHTML={{ __html: data }}></div>}
        </div>
      </Modal.Body>
      <Modal.Footer className="border-0">
        <Button
          className="btn-primary"
          variant="secondary"
          onClick={() => setShow(false)}
        >
          Understood
        </Button>
      </Modal.Footer>
    </Modal>
  );
}

export default TermsAndConditionReplicaModal;
