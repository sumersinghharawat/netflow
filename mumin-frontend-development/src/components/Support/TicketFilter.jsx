import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import Select from "react-select";

const TicketFilter = ({
  selectedOptions,
  setSelectedOptions,
  partials
}) => {
  const { t } = useTranslation();
  const [category, setCategory] = useState([]);
  const [prority, setPriority] = useState([]);
  const [status, setStatus] = useState([]);
  const [ticketId, setTicketId] = useState(selectedOptions.ticketId);


  const handleTicketIdChange = (event) => {
    setTicketId(event.target.value);
  };

  const handleCategoryChange = (selectedOption) => {
    setCategory(selectedOption);
  };

  const handlePriorityChange = (selectedOption) => {
    setPriority(selectedOption);
  };

  const handleStatusChange = (selectedOption) => {
    setStatus(selectedOption);
  };

  const handleSearch = () => {
    setSelectedOptions({
      ...selectedOptions,
      selectedCategoryOption: category.map((option) => option.value),
      selectedPriorityOption: prority.map((option) => option.value),
      selectedStatusOption: status.map((option) => option.value),
      ticketId: ticketId,
    });
    
  };

  const handleReset = () => {
    setSelectedOptions({
      ticketId: "",
      selectedCategoryOption: "",
      selectedPriorityOption: "",
      selectedStatusOption: "",
    });
    setCategory([])
    setPriority([])
    setStatus([])
    setTicketId([])
  };

  return (
    <div className="filter_Section">
      <div className="row align-items-center">
        <div className="col-md-2">
          <div className="right_search_div">
            <label htmlFor="ticketId">{t("ticket_id")}</label>
            <input
              id="ticketId"
              type="text"
              value={ticketId}
              className="form-control"
              placeholder="Ticket Id"
              onChange={handleTicketIdChange}
            />
          </div>
        </div>
        <div className="col-md-3">
          <div className="right_search_div">
            <label htmlFor="category">{t("category")}</label>
            <Select
              id="category"
              value={category}
              onChange={handleCategoryChange}
              options={partials.data?.categories}
              isMulti
            />
          </div>
        </div>
        <div className="col-md-2">
          <div className="right_search_div">
            <label htmlFor="priority">{t("priority")}</label>
            <Select
              id="priority"
              value={prority}
              onChange={handlePriorityChange}
              options={partials.data?.priorities}
              isMulti
            />
          </div>
        </div>
        <div className="col-md-3">
          <div className="right_search_div">
            <label htmlFor="status">{t("status")}</label>
            <Select
              id="status"
              value={status}
              onChange={handleStatusChange}
              options={partials.data?.status}
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

export default TicketFilter;
