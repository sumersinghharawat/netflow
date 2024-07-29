import React, { useEffect, useState } from "react";
import LeadsTableFilter from "../Common/table/LeadsTableFilter";
import TableMain from "../Common/table/TableMain";
import { Col, Form, Modal, Row } from "react-bootstrap";
import SubmitButton from "../Common/buttons/SubmitButton";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";
import { useTranslation } from "react-i18next";
import Select from "react-select";
import { PhoneInput } from "react-international-phone";
import { PhoneNumberUtil } from "google-libphonenumber";
import DatePickerComponent from "../Common/DatePickerComponent";
import { useForm } from "react-hook-form";
import dayjs from "dayjs";

const LeadsTable = (props) => {
  const { t } = useTranslation();
  const {
    setError,
    setValue,
    trigger,
    formState: { errors },
  } = useForm();
  const phoneUtil = PhoneNumberUtil.getInstance();
  const queryClient = useQueryClient();
  const [tableData, setTableData] = useState(props?.tableData);
  const [formVisible, setFormVisible] = useState(false);
  const [isEditModeEnabled, setIsEditModeEnabled] = useState(false);
  const [selectedDate, setSelectedDate] = useState(dayjs());
  const [isCalenderOpen, setIsCalenderOpen] = useState(false);

  const [editFormData, setEditFormData] = useState({
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
  const [errorMessage, setErrorMessage] = useState({
    firstName: null,
    lastName: null,
    emailId: null,
    skypeId: null,
    mobileNo: null,
    countryId: null,
    description: null,
    interestStatus: null,
    followupDate: null,
    leadStatus: null,
  });
  const [countries, setCountries] = useState([]);
  const [leadId, setLeadId] = useState();
  const leadUpdateMutation = ApiHook.CallUpdateLead();
  const headers = [
    `${t("slno")}`,
    `${t("first_name")}`,
    `${t("last_name")}`,
    `${t("lead_status")}`,
    `${t("email")}`,
    `${t("mobile")}`,
    `${t("skype")}`,
    `${t("edit_lead")}`,
  ];
  const countryList = (data) => {
    return data?.map((item) => ({
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
  const changeCountry = (country) => {
    setEditFormData((prevData) => ({
      ...prevData,
      countryId: country?.value,
    }));
  };
  const changeInterestLevel = (value) => {
    setEditFormData((prevData) => ({
      ...prevData,
      interestStatus: value?.value,
    }));
  };
  const changeLeadStatus = (value) => {
    setEditFormData((prevData) => ({
      ...prevData,
      leadStatus: value?.value,
    }));
  };

  const isPhoneValid = (phone) => {
    return phone.length >= 7 && !isNaN(phone);
  };
  const isPhoneNumberValid = isPhoneValid(editFormData.mobileNo);
  const handlePhoneNumber = (phone) => {
    setEditFormData((prevData) => ({
      ...prevData,
      mobileNo: phone,
    }));
    setValue("mobileNo", phone);
    setError("mobileNo", { message: "" });
  };

  const toggleEditMode = () => {
    setIsEditModeEnabled(!isEditModeEnabled);
  };
  const openCalender = () => {
    setIsCalenderOpen(true);
  };
  const closeCalender = () => {
    setIsCalenderOpen(false);
  };

  useEffect(() => {
    setTableData(props.tableData);
    setCountries(props.tableData?.countries);
  }, [props.tableData]);

  const handleEditClick = (idToFind) => {
    setLeadId(idToFind);
    setFormVisible(true);
    const item = tableData?.leads?.rows.find((item) => item.id === idToFind);
    setEditFormData({
      firstName: item.firstName,
      lastName: item.lastName,
      emailId: item.emailId,
      skypeId: item.skypeId,
      mobileNo: item.mobileNo,
      countryId: item.countryId,
      description: item.description,
      interestStatus: item.interestStatus,
      followupDate: item.followupDate,
      leadStatus: item.leadStatus,
    });
  };

  // const isFormValid = () => {
  //   const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

  //   return (
  //     isPhoneNumberValid &&
  //     editFormData?.firstName.trim() !== "" &&
  //     emailRegex.test(editFormData?.emailId.trim())
  //   );
  // };

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
    const requiredIds = [
      "firstName",
      "emailId",
      "description",
      "followupDate",
      "mobileNo",
    ];

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
    } else if (id === "mobileNo") {
      if (value.length < 5) {
        setErrorMessage((prevData) => ({
          ...prevData,
          mobileNo: "Minimum length is 5 digits",
        }));
      } else if (!/^[0-9]+$/.test(value)) {
        setErrorMessage((prevData) => ({
          ...prevData,
          mobileNo: "Only numbers are allowed",
        }));
      }
    } else if (id === "followupDate") {
      const selectedDate = new Date(value);
      const minDate = new Date("1950-01-01");
      const today = new Date();
      if (
        selectedDate < minDate ||
        selectedDate.getFullYear() > today.getFullYear()
      ) {
        setErrorMessage((prevData) => ({
          ...prevData,
          followupDate: "Date must be between 1950 and today's date",
        }));
      }
    }
  };
  const handleDateChange = (newDate, item) => {
    if (newDate) {
      setSelectedDate(newDate);
      const formattedDate = newDate.format("YYYY-MM-DD");
      setEditFormData((prevData) => ({
        ...prevData,
        followupDate: formattedDate,
      }));
      setErrorMessage((prevData) => ({
        ...prevData,
        followupDate: "",
      }));
    }
  };
  const handleUpdate = async () => {
    if (!isPhoneNumberValid) {
      // setErrorMessage((prevData) => ({
      //   ...prevData,
      //   mobileNo:t("invalidPhone")
      // }))
      setError("mobileNo", { message: t("min_length_is") });
    }
    const isValid = await trigger();
    if (isValid & isPhoneNumberValid) {
      const payload = {
        ...editFormData,
        leadId,
      };
      leadUpdateMutation.mutate(payload, {
        onSuccess: (res) => {
          if (res.status) {
            queryClient.invalidateQueries({ queryKey: ["leads"] });
            toast.success(t(res.data));
            setFormVisible(false);
            setIsEditModeEnabled(false);
          }
        },
      });
    }
  };
  return (
    <>
      <div className="ewallet_table_section">
        <div className="ewallet_table_section_cnt">
          <LeadsTableFilter
            replicaUrl={props?.replicaUrl}
            setTableData={setTableData}
          />
          <div className="table-responsive min-hieght-table">
            <TableMain
              headers={headers}
              data={tableData?.leads?.rows}
              startPage={1}
              currentPage={props?.currentPage}
              totalPages={props?.totalPages}
              type="leads"
              itemsPerPage={props?.itemsPerPage}
              setItemsPerPage={props?.setItemsPerPage}
              setCurrentPage={props?.setCurrentPage}
              handleEditClick={handleEditClick}
            />
          </div>
        </div>
      </div>
      <Modal
        show={formVisible}
        onHide={() => setFormVisible(false)}
        dialogClassName="modal-dialog modal-lg"
      >
        <Modal.Header closeButton>
          <Modal.Title>{t("lead_view")}</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <div className="editBg">
            <span className="leadviewEditBtn">
              <i
                onClick={toggleEditMode}
                className="fa-solid fa-pen-to-square"
                style={{ color: "#32009c" }}
              ></i>
            </span>
          </div>
          <Row className="lead_view_pop">
            <Col md={12}>
              <Form.Group>
                <Form.Label>{t("first_name")}</Form.Label>
                <Form.Control
                  id="firstName"
                  type="text"
                  value={editFormData.firstName}
                  onChange={(e) => handleChange(e)}
                  isInvalid={errorMessage?.firstName !== null}
                  disabled={!isEditModeEnabled}
                />
                <Form.Control.Feedback type="invalid">
                  {errorMessage.firstName}
                </Form.Control.Feedback>
              </Form.Group>
            </Col>
            <Col md={6}>
              <Form.Group>
                <Form.Label>{t("last_name")}</Form.Label>
                <Form.Control
                  id="lastName"
                  type="text"
                  value={editFormData.lastName}
                  onChange={(e) => handleChange(e)}
                  disabled={!isEditModeEnabled}
                />
              </Form.Group>
            </Col>
            <Col md={6}>
              <Form.Group>
                <Form.Label>{t("email")}</Form.Label>
                <Form.Control
                  id="emailId"
                  type="text"
                  value={editFormData.emailId}
                  onChange={(e) => handleChange(e)}
                  isInvalid={errorMessage?.emailId !== null}
                  disabled={!isEditModeEnabled}
                />
                <Form.Control.Feedback type="invalid">
                  {errorMessage.emailId}
                </Form.Control.Feedback>
              </Form.Group>
            </Col>

            <Col md={6}>
              <Form.Group>
                <Form.Label>{t("skype")}</Form.Label>
                <Form.Control
                  id="skypeId"
                  type="text"
                  value={editFormData.skypeId}
                  onChange={(e) => handleChange(e)}
                  disabled={!isEditModeEnabled}
                />
              </Form.Group>
            </Col>

            <Col md={6}>
              <Form.Group>
                <Form.Label>{t("mobile")}</Form.Label>
                <PhoneInput
                  defaultCountry="us"
                  value={editFormData?.mobileNo}
                  onChange={handlePhoneNumber}
                  disabled={!isEditModeEnabled}
                />
                {errors.mobileNo && (
                  <span className="validation-error-message">
                    {errors.mobileNo.message}
                  </span>
                )}
              </Form.Group>
            </Col>

            <Col md={6}>
              <Form.Group>
                <Form.Label>{t("country")}</Form.Label>
                <Select
                  id="countryId"
                  value={countryList(countries)?.find(
                    (item) => item.value === editFormData.countryId
                  )}
                  options={countryList(countries)}
                  onChange={changeCountry}
                  isDisabled={!isEditModeEnabled}
                />
              </Form.Group>
            </Col>

            <Col md={6}>
              <Form.Group>
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
                  isDisabled={!isEditModeEnabled}
                />
              </Form.Group>
            </Col>

            <Col md={6}>
              <Form.Group id="follow_up">
                <Form.Label>{t("next_followup_date")}</Form.Label>
                <DatePickerComponent
                  date={selectedDate}
                  handleDateChange={(newDate) => handleDateChange(newDate)}
                  isCalenderOpen={isCalenderOpen}
                  openCalender={openCalender}
                  closeCalender={closeCalender}
                  disabled={!isEditModeEnabled}
                />
              </Form.Group>
            </Col>
            <Col md={6}>
              <Form.Group>
                <Form.Label>{t("lead_status")}</Form.Label>
                <Select
                  isSearchable={false}
                  name="leadStatus"
                  id="leadStatus"
                  onChange={changeLeadStatus}
                  value={leadStatusOptions.find(
                    (item) => item.value === editFormData?.leadStatus
                  )}
                  options={leadStatusOptions}
                  isDisabled={!isEditModeEnabled}
                />
              </Form.Group>
            </Col>
            <Col md={12}>
              <Form.Group>
                <Form.Label>{t("description")}</Form.Label>
                <Form.Control
                  id="description"
                  as="textarea"
                  type="text"
                  placeholder="Description"
                  value={editFormData.description}
                  onChange={(e) => handleChange(e)}
                  isInvalid={errorMessage?.description !== null}
                  disabled={!isEditModeEnabled}
                />
                <Form.Control.Feedback type="invalid">
                  {errorMessage.description}
                </Form.Control.Feedback>
              </Form.Group>
            </Col>
          </Row>
        </Modal.Body>
        <Modal.Footer>
          <SubmitButton
            className="btn btn-secondary"
            text={leadUpdateMutation.isLoading ? "Updating" : "Update"}
            click={handleUpdate}
            isSubmitting={!isEditModeEnabled}
          />
        </Modal.Footer>
      </Modal>
    </>
  );
};

export default LeadsTable;
