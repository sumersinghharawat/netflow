import React, { useRef, useState } from "react";
import LabelledButton from "../buttons/LabelledButton";
import SubmitButton from "../buttons/SubmitButton";
import { useTranslation } from "react-i18next";
import { useReactToPrint } from "react-to-print";
import { exportToCSV, exportToExcel } from "../../../utils/tableExports";
import TableContent from "./TableContent";
import { MultiSelect } from "react-multi-select-component";

const PayoutTableFilter = ({
  setActiveTab,
  activeTab,
  headers,
  data,
  type,
}) => {
  const { t } = useTranslation();
  const tableRef = useRef();
  const options = [
    { value: "paid", label: `${t("paid")}` },
    { value: "requested", label: `${t("requested")}` },
    { value: "approved", label: `${t("approved")}` },
    { value: "rejected", label: `${t("rejected")}` },
  ];

  // Initialize selectedValue to "paid" when the component is mounted
  const [selectedValue, setSelectedValue] = useState(activeTab);

  const handleTabChange = (e) => {
    if (e.target.id === "searchButton") {
      if (selectedValue.length === 0) {
        setActiveTab([]);
      } else {
        setActiveTab(selectedValue);
      }
    } else if (e.target.id === "resetButton") {
      setActiveTab([
        { value: "paid", label: `${t("paid")}` },
        { value: "requested", label: `${t("requested")}` },
        { value: "approved", label: `${t("approved")}` },
        { value: "rejected", label: `${t("rejected")}` },
      ]);
      setSelectedValue([
        { value: "paid", label: `${t("paid")}` },
        { value: "requested", label: `${t("requested")}` },
        { value: "approved", label: `${t("approved")}` },
        { value: "rejected", label: `${t("rejected")}` },
      ]);
    }
  };

  const handleSelectChange = (selectedOptions) => {
    setSelectedValue(selectedOptions);
  };

  const handlePrint = useReactToPrint({
    content: () => tableRef.current,
  });

  return (
    <>
      <div className="filter_Section">
        <div className="row justify-content-between">
          <div className="col-md-3 payout-filter">
            <div className="right_search_div d-flex gap-1">
              <MultiSelect
                id="payoutFilter"
                options={options}
                value={selectedValue}
                onChange={handleSelectChange}
                className="form-control"
              />
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
          <div className="col-md-4 text-end mob_filter_right">
            <LabelledButton
              className="fa fa-file-excel"
              text=" Excel"
              click={() => exportToExcel(data, headers, type)}
            />
            <LabelledButton
              className="fa fa-file-text"
              text=" CSV"
              click={() => exportToCSV(data, headers, type)}
            />
            <LabelledButton
              className="fa fa-print"
              text=" Print"
              click={handlePrint}
            />
          </div>
          <div style={{ display: "none", padding: "10px" }}>
            <TableContent
              tableRef={tableRef}
              data={data}
              headers={headers}
              type={"payout"}
            />
          </div>
        </div>
      </div>
    </>
  );
};

export default PayoutTableFilter;
