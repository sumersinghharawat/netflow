import React, { useState } from "react";
import { useTranslation } from "react-i18next";

const NumberInput = ({
  register,
  value,
  isEditModeEnabled,
  name,
  style,
  placeholder,
  onBlur,
  type
}) => {
  const { t } = useTranslation();

  const inputProps = {
    ...register("mobile", {
      required: t("this_field_is_required"),
      minLength: {
        value: 5,
        message: t("min_length_of_5"),
      },
      pattern: {
        value: /^[0-9]+$/,
        message: t("only_number"),
      }
    }),
    placeholder: placeholder || "",
    type: "text",
    className: style || "",
    defaultValue: value,
    disabled: !isEditModeEnabled
  };

  if (type === "register") {
    inputProps.onBlur = onBlur || "";
    inputProps.name = name || "";
  }

  return <input {...inputProps} />;
};

export default NumberInput;