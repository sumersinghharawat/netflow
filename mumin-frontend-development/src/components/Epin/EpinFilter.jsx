import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import Select from "react-select";

const EpinFilter = ({ selectedOptions, setSelectedOptions, partials }) => {
  const { t } = useTranslation();
  const [epinSearch, setEpinSearch] = useState(selectedOptions.epin);
  const [amount, setAmount] = useState([]);
  const [status, setStatus] = useState([]);

  const handleEpinSearchChange = (event) => {
    setEpinSearch(event.target.value);
  };

  const handleAmountChange = (selectedOption) => {
    setAmount(selectedOption);
  };

  const handleStatusChange = (selectedOption) => {
    setStatus(selectedOption);
  };

  const handleSearch = () => {
    setSelectedOptions({
      ...selectedOptions,
      selectedAmountOption: amount.map((option) => option.value),
      selectedStatusOption: status.map((option) => option.value),
      epin: epinSearch,
    });
  };

  const handleReset = () => {
    setSelectedOptions({
      epin: "",
      selectedAmountOption: "",
      selectedStatusOption: "",
    });
    setAmount([]);
    setStatus([]);
    setEpinSearch("");
  };

  return (
    <div className="filter_Section">
      <div className="row align-items-center">
        <div className="col-md-2">
          <div className="right_search_div">
            <label htmlFor="epinSearch">{t("epin")}</label>
            <input
              id="epinSearch"
              type="text"
              value={epinSearch}
              className="form-control"
              placeholder="E-pin"
              onChange={handleEpinSearchChange}
            />
          </div>
        </div>
        <div className="col-md-2">
          <div className="right_search_div">
            <label htmlFor="amount">{t("amount")}</label>
            <Select
              id="amount"
              value={amount}
              onChange={handleAmountChange}
              options={partials?.amounts}
              isMulti
            />
          </div>
        </div>
        <div className="col-md-2">
          <div className="right_search_div">
            <label htmlFor="status">{t("status")}</label>
            <Select
              id="status"
              value={status}
              onChange={handleStatusChange}
              options={partials?.status}
              isMulti
            />
          </div>
        </div>
        <div className="col-md-2 mt-4">
          <div className="right_search_div d-flex gap-1">
            <button className="btn btn-primary" onClick={handleSearch}>
              {t("search")}
            </button>
            <button className="btn btn-secondary" onClick={handleReset}>
              {t("reset")}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

export default EpinFilter;
