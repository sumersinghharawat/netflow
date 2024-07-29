import React, { useRef } from "react";
import LabelledButton from "../buttons/LabelledButton";
// import SubmitButton from "../buttons/SubmitButton";
import { useReactToPrint } from "react-to-print";
import TableContent from "./TableContent";
import { exportToCSV, exportToExcel } from "../../../utils/tableExports";

const RepurchaseTableFilter = ({ headers, data, type }) => {
  const tableRef = useRef();

  // const handleSearch = () => {};

  const handlePrint = useReactToPrint({
    content: () => tableRef.current,
  });
  
  return (
    <div className="filter_Section">
      <div className="row justify-content-end">
        {/* <div className="col-md-4">
          <div className="right_search_div d-flex gap-1">
            <input type="text" className="form-control" />
            <SubmitButton
              isSubmitting=""
              text="Search"
              className="btn btn-primary"
              click={handleSearch}
              id="searchButton"
            />
            <SubmitButton
              isSubmitting=""
              text="Reset"
              className="btn btn-secondary"
              click={handleSearch}
              id="resetButton"
            />
          </div>
        </div> */}
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
            type={"repurchase-report"}
          />
        </div>
      </div>
    </div>
  );
};

export default RepurchaseTableFilter;
