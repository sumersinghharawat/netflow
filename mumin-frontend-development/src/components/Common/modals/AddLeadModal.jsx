import React, { useState } from "react";
import { useForm } from "react-hook-form";
import { Modal, Button, Form } from "react-bootstrap";
import DatePickerComponent from "../DatePickerComponent";
import dayjs from "dayjs";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../../hooks/apiHook";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";

const AddLeadModal = ({
  leadId,
  showAddLeadModal,
  setShowAddLeadModal,
  editFormData,
  setEditFormData,
}) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const [followDate, setFollowDate] = useState(dayjs());
  const [files, setFiles] = useState([]);
  const [formData, setFormData] = useState({
    description: "",
    followupDate: dayjs().format('YYYY-MM-DD'),
  });
  const [errorMessage, setErrorMessage] = useState({
    description: "",
    followupDate: "",
    selectedFile: "",
  });
  const [isCalenderOpen, setIsCalenderOpen] = useState(false);
  const handleClose = () => {
    setShowAddLeadModal(false);
    // Clear form fields when modal is closed
    setEditFormData({
      id: "",
      firstName: "",
      lastName: "",
      emailId: "",
      skypeId: "",
      mobileNo: "",
      countryId: "",
      description: "",
      interestStatus: "",
      followupDate: null,
      leadStatus: "",
    });
  };
  const addFollowUpMutation = ApiHook.CallAddFollowUp();
  const openCalender = () => {
    setIsCalenderOpen(true);
  };
  const closeCalender = () => {
    setIsCalenderOpen(false);
  };
  const handleFileUpload = (e) => {
    const file = e.target.files;
    setFiles([...file]);
  };
  const handleSubmit = (event) => {
    event.preventDefault();

    // Add the 'Id' property to the formData
    const data = {
      ...formData,
      Id: leadId,
      files: files,
    };
    addFollowUpMutation.mutateAsync(data, {
      onSuccess: (res) => {
        if (res?.status) {
          toast.success(res?.data)
          queryClient.invalidateQueries({queryKey:['crm-tiles']});
          queryClient.invalidateQueries({queryKey:['followup-today']});
          queryClient.invalidateQueries({queryKey:['recent-leads']});
          queryClient.invalidateQueries({queryKey:['missed-followup']});
          setFiles([]);
          setFormData({
            description: "",
            followupDate: dayjs().format('YYYY-MM-DD'),
          })
        }
      },
    });

    handleClose();
  };

  const handleChange = (event) => {
    const { id, value, files } = event.target;
    setFormData({
      ...formData,
      [id]: id === "selectedFile" ? files : value,
    });
    setErrorMessage((prev) => ({
      ...prev,
      [id]: null,
    }));
    const requiredIds = ["lead", "description", "nextFollowDate"];
  };
  const handleDateChange = (newDate) => {
    if (newDate) {
      const formattedDate = newDate.format("YYYY-MM-DD");
      setFollowDate(newDate);
      setFormData((prev) => ({
        ...prev,
        followupDate: formattedDate,
      }));
      setErrorMessage((prev) => ({
        ...prev,
        nextFollowDate: null,
      }));
    }
  };
  return (
    <Modal
      id="addfollowup"
      show={showAddLeadModal}
      onHide={handleClose}
      size="lg"
      centered
    >
      <Modal.Header closeButton>
        <Modal.Title>{t('add_follow-up')}</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <Form onSubmit={handleSubmit}>
          <Form.Group className="form-group">
            <Form.Label>{t("lead")}</Form.Label>
            <Form.Control
              id="lead"
              type="text"
              placeholder=""
              value={editFormData?.firstName + " " + editFormData?.lastName}
              disabled
            />
            {errorMessage.lead && (
              <span className="validation-error-message">
                {errorMessage.lead}
              </span>
            )}
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>{t("description")}</Form.Label>
            <Form.Control
              id="description"
              as="textarea"
              type="text"
              placeholder=""
              value={formData.description}
              onChange={handleChange}
            />
            {errorMessage.description && (
              <span className="validation-error-message">
                {errorMessage.description}
              </span>
            )}
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>Next Follow Date</Form.Label>
            <DatePickerComponent
              className={"date-picker"}
              date={followDate}
              handleDateChange={handleDateChange}
              isCalenderOpen={isCalenderOpen}
              openCalender={openCalender}
              closeCalender={closeCalender}
            />
            {errorMessage.nextFollowDate && (
              <span className="validation-error-message">
                {errorMessage.nextFollowDate}
              </span>
            )}
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>Select File</Form.Label>
            <Form.Control
              multiple
              id="selectedFile"
              type="file"
              placeholder=""
              onChange={handleFileUpload}
            />
            {errorMessage.selectedFile && (
              <span className="validation-error-message">
                {errorMessage.selectedFile}
              </span>
            )}
          </Form.Group>
          <Modal.Footer>
            <Button variant="secondary" onClick={handleClose}>
              Close
            </Button>
            <Button variant="primary" type="submit">
              Add Follow UP
            </Button>
          </Modal.Footer>
        </Form>
      </Modal.Body>
    </Modal>
  );
};

export default AddLeadModal;
