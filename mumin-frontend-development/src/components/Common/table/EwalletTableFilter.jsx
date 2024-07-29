import React, { useState } from "react";
import SubmitButton from "../buttons/SubmitButton";
import Select from "react-select";
import { useTranslation } from "react-i18next";
import { DatePicker } from "antd";
const { RangePicker } = DatePicker;

const EwalletTableFilter = ({
  type,
  setApiTab,
  selectedCategory,
  setSelectedCategory,
  setDateRange,
  category,
}) => {
  const categoryList = [];
  const listOptions = [];
  const { t } = useTranslation();
  const [selectedValue, setSelectedValue] = useState(selectedCategory);
  const [dateKey, setDateKey] = useState(Date.now());
  const transferCategory = [
    { label: t("credit"), value: "credit" },
    { label: t("debit"), value: "debit" },
  ];
  // Adding lang to the category
  category?.map((item) => {
    const data = {
      label: t(`${item.label}`),
      value: item.value,
    };
    listOptions.push(data);
  });

  const handleTabChange = (e) => {
    if (e.target.id === "searchButton") {
      if (type === "transfer_history") {
        setApiTab(type);
      } else if (type === "my_earnings") {
        setApiTab(type);
      }
    } else if (e.target.id === "resetButton") {
      if (type === "transfer_history") {
        setDateRange({ startDate: "", endDate: "" });
        setSelectedCategory([]);
        setSelectedValue([])
        setApiTab(type);
        setDateKey(Date.now());
        
      } else if (type === "my_earnings") {
        setSelectedCategory([]);
        setSelectedValue([])
        setDateRange({ startDate: "", endDate: "" });
        setApiTab(type);
        setDateKey(Date.now());
      }
    }
  };

  const handleSelectChange = (values) => {
    Object.entries(values).map(([key, value]) => {
      categoryList.push(value.value);
    });
    setSelectedCategory(categoryList);
    setSelectedValue(values);
  };

  const handleDateRange = (dates) => {
    if (Array.isArray(dates) && dates.length === 2) {
      const [toDate, fromDate] = dates;
      setDateRange({
        startDate: toDate.format("YYYY-MM-DD H:m:s"),
        endDate: fromDate.format("YYYY-MM-DD H:m:s")
      });
    }
  };

  return (
    <div className="filter_Section">
      <div className="row">
        <div key={"1"} className="col-xl-2 col-md-3">
          <div className="right_search_div">
            <label className="la-control">{t("date")}</label>
            <RangePicker key={dateKey} onChange={(dates) => handleDateRange(dates)} />
          </div>
        </div>
        {!!(type === "transfer_history") && (
          <div key={"2"} className="col-xl-2 col-md-3">
            <div className="right_search_div">
              <label className="la-control">{t("category")}</label>
              <Select
                options={transferCategory}
                value={selectedValue}
                onChange={handleSelectChange}
                isMulti
              />
            </div>
          </div>
        )}
        {!!(type === "my_earnings") && (
          <div key={"2"} className="col-xl-3 col-md-3">
            <div className="right_search_div">
              <label className="la-control">{t("category")}</label>
              <Select
                options={listOptions}
                value={selectedValue}
                onChange={handleSelectChange}
                isMulti
              />
            </div>
          </div>
        )}
        <div className="col-xl-2 col-md-3 mt-4">
          <div className="right_search_div d-flex gap-1">
            <SubmitButton
              isSubmitting=""
              text="search"
              className="btn btn-primary"
              click={handleTabChange}
              id="searchButton"
            />
            <SubmitButton
              isSubmitting=""
              text="reset"
              className="btn btn-secondary"
              click={handleTabChange}
              id="resetButton"
            />
          </div>
        </div>
      </div>
    </div>
  );
};

export default EwalletTableFilter;
