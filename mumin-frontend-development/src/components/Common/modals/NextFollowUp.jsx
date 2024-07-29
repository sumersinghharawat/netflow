import React, { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { Modal, Button, Form } from "react-bootstrap";
import DatePickerComponent from "../DatePickerComponent";
import dayjs from "dayjs";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../../hooks/apiHook";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";

const NextFollowUp = ({
  leadId,
  showNextFollowUp,
  setShowNextFollowUp,
  editFormData,
  setEditFormData,
}) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const [currentFollowDate, setCurrentFollowDate] = useState(dayjs("01-09-2025"));
  const [nextFollowDate, setNextFollowDate] = useState(dayjs());
  const [files, setFiles] = useState([]);
  const [formData, setFormData] = useState({
    currentFollowDate: "",
    nextFollowDate: "",
  });
  const [errorMessage, setErrorMessage] = useState({
    currentFollowDate:"",
    nextFollowDate: "",
  });
  const [isCurrentCalenderOpen, setIsCurrentCalenderOpen] = useState(false);
  const [isNextCalenderOpen, setIsNextCalenderOpen] = useState(false);
  const handleClose = () => {
    setShowNextFollowUp(false);
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
      followupDate: "",
      leadStatus: "",
    });
  };
  const addNextFollowUpMutation = ApiHook.CallAddNextFollowUp();
  const openCurrentCalender = () => {
    setIsCurrentCalenderOpen(true);
  };
  const closeCurrentCalender = () => {
    setIsCurrentCalenderOpen(false);
  };
  const openNextCalender = () => {
    setIsNextCalenderOpen(true);
  };
  const closeNextCalender = () => {
    setIsNextCalenderOpen(false);
  };
  const handleFileUpload = (e) => {
    const file = e.target.files;
    setFiles([...file]);
  };
  const handleSubmit = (event) => {
    event.preventDefault();

    // Add the 'Id' property to the formData
    const data = {
      id: leadId,
      nextFollowupDate:formData.nextFollowDate
    };
    addNextFollowUpMutation.mutateAsync(data, {
      onSuccess: (res) => {
        if (res?.status) {
          toast.success(t(res?.data));
          queryClient.invalidateQueries({ queryKey: ["crm-tiles"] });
          queryClient.invalidateQueries({ queryKey: ["followup-today"] });
          queryClient.invalidateQueries({ queryKey: ["recent-leads"] });
          queryClient.invalidateQueries({ queryKey: ["missed-followup"] });
        }
      },
    });

    handleClose();
  };


  const handleCurrentFollowUp = (newDate) => {
    if (newDate) {
      const formattedDate = newDate.format("YYYY-MM-DD");
      setCurrentFollowDate(newDate);
      setFormData((prev) => ({
        ...prev,
        currentFollowDate: formattedDate,
      }));
      setErrorMessage((prev) => ({
        ...prev,
        currentFollowDate: null,
      }));
    }
  };
  const handleNextFollowUp = (newDate) => {
    if (newDate) {
      const formattedDate = newDate.format("YYYY-MM-DD");
      setCurrentFollowDate(newDate);
      setFormData((prev) => ({
        ...prev,
        nextFollowDate: formattedDate,
      }));
      setErrorMessage((prev) => ({
        ...prev,
        nextFollowDate: null,
      }));
    }
  };
  useEffect(() => {
    setCurrentFollowDate(dayjs(editFormData?.followupDate));
  }, [editFormData]);
  
  return (
    <Modal
      id="nextFollowUp"
      show={showNextFollowUp}
      onHide={handleClose}
      size="lg"
      centered
    >
      <Modal.Header closeButton>
        <Modal.Title>{t('next_follow_up_date')}</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <Form onSubmit={handleSubmit}>
        <Form.Group className="form-group">
            <Form.Label>{t('Current_Follow_Date')}</Form.Label>
            <DatePickerComponent
              className={"date-picker"}
              date={currentFollowDate}
              handleDateChange={handleCurrentFollowUp}
              isCalenderOpen={isCurrentCalenderOpen}
              openCalender={openCurrentCalender}
              closeCalender={closeCurrentCalender}
              disabled={true}
            />
            {errorMessage.currentFollowDate && (
              <span className="validation-error-message">
                {errorMessage.currentFollowDate}
              </span>
            )}
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>{t('next_follow_up_date')}</Form.Label>
            <DatePickerComponent
              className={"date-picker"}
              date={nextFollowDate}
              handleDateChange={handleNextFollowUp}
              isCalenderOpen={isNextCalenderOpen}
              openCalender={openNextCalender}
              closeCalender={closeNextCalender}
            />
            {errorMessage.nextFollowDate && (
              <span className="validation-error-message">
                {errorMessage.nextFollowDate}
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

export default NextFollowUp;
