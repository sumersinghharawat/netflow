import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import { useSelector } from "react-redux";
import SubmitButton from "../Common/buttons/SubmitButton";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";

const AdditionalDetails = ({ additional }) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const [isEditModeEnabled, setIsEditModeEnabled] = useState(false);
  const userSelectedLanguage = useSelector(
    (state) => state?.user?.selectedLanguage
  );
  const [additionalDetails, setAdditionalDetails] = useState([]);

  const additionalMutation = ApiHook.CallAdditionalDetails();

  const toggleEditMode = () => {
    setIsEditModeEnabled(!isEditModeEnabled);
  };

  const handleChange = (event) => {
    const { value, id } = event.target;
    setAdditionalDetails((prevCredentials) => {
      // Ensure that fields is initialized as an array if it's undefined
      const fields = prevCredentials.fields || [];

      // Check if an entry with the same customfieldId exists and update it
      const updatedFields = fields.map((field) => {
        if (field.customfieldId === id) {
          return {
            ...field,
            value: value,
          };
        }
        return field;
      });

      // If the field doesn't exist, add a new entry
      const fieldExists = updatedFields.some(
        (field) => field.customfieldId === id
      );
      if (!fieldExists) {
        updatedFields.push({
          customfieldId: id,
          value: value,
        });
      }

      return {
        ...prevCredentials,
        fields: updatedFields,
      };
    });
  };

  const onSubmit = () => {
    additionalMutation.mutate(additionalDetails, {
      onSuccess: (res) => {
        toast.success(res?.data);
        queryClient.invalidateQueries({ queryKey: ["profile"] });
        setIsEditModeEnabled(false)
      },
    });
  };

  return (
    <div id="fifthTab" className="tabcontent">
      <div className="editSec">
        <div className={`editSec ${isEditModeEnabled ? "disabled" : ""}`}>
          <span
            style={{ textDecoration: "none", cursor: "pointer" }}
            onClick={toggleEditMode}
          >
            <i
              className="fa-solid fa-pen-to-square"
              style={{ color: "#32009c" }}
            ></i>
          </span>
        </div>
      </div>
      <h3>{t("additionalDetails")}</h3>
      <div className="tabcontent_form_section">
        <div className="mb-3 row tabBlockClass">
          {Object.entries(additional).map(([key, custom]) => (
            <React.Fragment key={key}>
              {custom.CustomfieldLangs?.map((customLabel) => {
                if (customLabel.languageId === userSelectedLanguage.id) {
                  return (
                    <React.Fragment key={customLabel.customfieldId}>
                      <label className="col-sm-3 col-form-label labelWidthClass">
                        {customLabel.value}:
                      </label>
                      <div className="col-md-9 col-sm-12 col-12 additionalField">
                        {custom.type === "textarea" ? (
                          <textarea
                            id={customLabel.customfieldId}
                            name={customLabel.value}
                            className="form-control"
                            disabled={!isEditModeEnabled}
                            defaultValue={custom.CustomfieldValue?.value}
                            onChange={handleChange}
                          />
                        ) : (
                          <input
                            id={customLabel.customfieldId}
                            type={custom.type}
                            name={customLabel.value}
                            className="form-control"
                            disabled={!isEditModeEnabled}
                            defaultValue={custom.CustomfieldValue?.value}
                            onChange={handleChange}
                          />
                        )}
                      </div>
                    </React.Fragment>
                  );
                }
              })}
            </React.Fragment>
          ))}
        </div>
        <div
          className={`paymenytLinkBtn ${isEditModeEnabled ? "disabled" : ""}`}
        >
          <SubmitButton
            id={"1"}
            isSubmitting={!isEditModeEnabled}
            click={onSubmit}
            text="update"
            className="btn"
          />
        </div>
      </div>
    </div>
  );
};

export default AdditionalDetails;
