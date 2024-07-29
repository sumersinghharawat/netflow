import React, { useEffect, useState } from "react";
import { Modal, Button, Form } from "react-bootstrap";
import { ApiHook } from "../../../hooks/apiHook";
import Select from "react-select";
import { useTranslation } from "react-i18next";
import { PhoneInput } from "react-international-phone";
import { useForm } from "react-hook-form";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";

const LeadEditModal = ({
  showLeadEditModal,
  setShowLeadEditModal,
  editFormData,
  setEditFormData,
  setErrorMessage,
  errorMessage,
  countries,
}) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();

  const countryList = (data) => {
    return countries?.map((item) => ({
      label: item?.name,
      value: item?.id,
    }));
  };
  const interestLevelOptions = [
    { value: 0, label: `${t("very_interested")}` },
    { value: 1, label: `${t("interested")}` },
    { value: 2, label: `${t("not_that_interested")}` },
  ];
  const leadStatusOptions = [
    { value: 0, label: `${t("rejected")}` },
    { value: 1, label: `${t("ongoing")}` },
    { value: 2, label: `${t("accepted")}` },
  ];

  // api
  const editCrmLeadMutation = ApiHook.CallEditCrmLead();
  // validation
  const isPhoneValid = (phone) => {
    return phone.length >= 7 && !isNaN(phone);
  };
  const isPhoneNumberValid = isPhoneValid(editFormData?.mobileNo);

  const handlePhoneNumber = (phone) => {
    setEditFormData((prev) => ({
      ...prev,
      mobileNo: phone,
    }));
  };

  const changeInterestLevel = (data) => {
    setEditFormData((prev) => ({
      ...prev,
      interestStatus: data?.value,
    }));
  };
  const changeLeadStatus = (data) => {
    setEditFormData((prev) => ({
      ...prev,
      leadStatus: data?.value,
    }));
  };
  const changeCountry = (country) => {
    setEditFormData((prev) => ({
      ...prev,
      countryId: country,
    }));
  };

  const handleClose = () => {
    setShowLeadEditModal(false);
    // Clear form fields when modal is closed
    setEditFormData({
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
    setErrorMessage({
      firstName:null,
      lastName:null,
      emailId: null,
      skypeId: null,
      mobileNo: null,
      countryId: null,
      description: null,
      interestStatus: null,
      followupDate: null,
      leadStatus: null
    })
  };
  console.log(errorMessage);
  const handleChange = (e) => {
    const { id, value } = e.target;
    setEditFormData((prevData) => ({
      ...prevData,
      [id]: value,
    }));
    setErrorMessage((prevData) => ({
      ...prevData,
      [id]: null,
    }));
    const requiredIds = ["firstName", "emailId", "description"];

    if (requiredIds.includes(id) && (value === null || value === "")) {
      setErrorMessage((prev) => ({
        ...prev,
        [id]: "*Required",
      }));
    }

    if (id === "emailId") {
      const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
      if (!emailRegex.test(value)) {
        setErrorMessage((prevData) => ({
          ...prevData,
          emailId: "Invalid email format",
        }));
      }
    }
  };
  const handleSubmit = () => {
    const hasErrors =
      errorMessage.firstName ||
      errorMessage.emailId ||
      errorMessage.description;
    console.log(hasErrors);
    // Handle form submission here
    if (!hasErrors && isPhoneNumberValid) {
      editCrmLeadMutation.mutateAsync(editFormData, {
        onSuccess: (res) => {
          if (res?.data?.status) {
            toast.success(res?.data?.data);
            handleClose(); // Close the modal after submission
            queryClient.invalidateQueries({ queryKey: ["crm-tiles"] });
            queryClient.invalidateQueries({ queryKey: ["followup-today"] });
            queryClient.invalidateQueries({ queryKey: ["recent-leads"] });
            queryClient.invalidateQueries({ queryKey: ["missed-followup"] });
            queryClient.invalidateQueries({ queryKey: ["missed-followup"] });
          }
        },
      });
    }
  };

  return (
    <Modal
      id="followup"
      show={showLeadEditModal}
      onHide={handleClose}
      size="lg"
      centered
    >
      <Modal.Header closeButton>
        <Modal.Title>{t("editLead")}</Modal.Title>
      </Modal.Header>
      <Modal.Body>
        <Form>
          <Form.Group className="form-group">
            <Form.Label>
              {t("firstName")}
              <span className="text-danger">*</span>
            </Form.Label>
            <Form.Control
              value={editFormData?.firstName}
              id="firstName"
              type="text"
              placeholder={t("firstName")}
              onChange={(e) => handleChange(e)}
              isInvalid={errorMessage?.firstName !== null}
            />
            {errorMessage.firstName && (
              <span className="validation-error-message">
                {errorMessage.firstName}
              </span>
            )}
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>{t("lastName")}</Form.Label>
            <Form.Control
              value={editFormData?.lastName}
              id="lastName"
              type="text"
              placeholder={t("lastName")}
              onChange={(e) => handleChange(e)}
            />
            {errorMessage.lastName && (
              <span className="validation-error-message">
                {errorMessage.lastName}
              </span>
            )}
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>
              {t("emailAddress")}
              <span className="text-danger">*</span>
            </Form.Label>
            <Form.Control
              id="emailId"
              value={editFormData?.emailId}
              type="text"
              placeholder={t("email")}
              onChange={(e) => handleChange(e)}
              isInvalid={errorMessage?.emailId !== null}
            />
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>Skype ID</Form.Label>
            <Form.Control
              id="skypeId"
              value={editFormData?.skypeId}
              type="text"
              placeholder="Skype ID"
              onChange={(e) => handleChange(e)}
            />
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>
              {t("mobile")}
              <span className="text-danger">*</span>
            </Form.Label>{" "}
            {errorMessage.mobileNo && (
              <span className="validation-error-message">
                {errorMessage.mobileNo}
              </span>
            )}
            <PhoneInput
              defaultCountry="us"
              id="mobileNo"
              value={editFormData?.mobileNo}
              onChange={handlePhoneNumber}
            />
            {errorMessage.mobileNo && (
              <span className="validation-error-message">
                {errorMessage.mobileNo}
              </span>
            )}
          </Form.Group>
          <Form.Group>
            <Form.Label>
              {t("country")}
              <span className="text-danger">*</span>
            </Form.Label>
            <Select
              id="country"
              value={countryList(countries)?.find(
                (item) => item.value === editFormData.countryId
              )}
              options={countryList(countries)}
              onChange={changeCountry}
              isSearchable={false}
            />
            {errorMessage.countryId && (
              <span className="validation-error-message">
                {errorMessage.countryId}
              </span>
            )}
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>{t("level_of_interest")}</Form.Label>
            <Select
              name="interestStatus"
              id="interestStatus"
              isSearchable={false}
              onChange={changeInterestLevel}
              value={interestLevelOptions.find(
                (item) => item.value === editFormData?.interestStatus
              )}
              options={interestLevelOptions}
            />
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>{t("description")}</Form.Label>
            <Form.Control
              as="textarea"
              id="description"
              type="text"
              value={editFormData?.description}
              placeholder={t("description")}
              onChange={(e) => handleChange(e)}
            />
          </Form.Group>
          <Form.Group className="form-group">
            <Form.Label>{t("lead_status")}</Form.Label>
            <Select
              name="leadStatus"
              id="leadStatus"
              isSearchable={false}
              onChange={changeLeadStatus}
              value={leadStatusOptions.find(
                (item) => item.value === editFormData?.leadStatus
              )}
              options={leadStatusOptions}
            />
          </Form.Group>
        </Form>
      </Modal.Body>
      <Modal.Footer>
        <Button variant="secondary" onClick={handleClose}>
          {t("close")}
        </Button>
        <Button variant="primary" onClick={handleSubmit}>
          {t("saveChanges")}
        </Button>
      </Modal.Footer>
    </Modal>
  );
};

export default LeadEditModal;
