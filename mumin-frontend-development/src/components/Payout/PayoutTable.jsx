import React from "react";
import TableMain from "../Common/table/TableMain";
import PayoutTableFilter from "../Common/table/PayoutTableFilter";
import { getLastPage } from "../../utils/getLastPage";
import { useTranslation } from "react-i18next";

const PayoutTable = (props) => {
  const { t } = useTranslation();
  const headers = [t("date"), t("amount"), t("payout_method"), t("status")];
  const lastPage = getLastPage(props.itemsPerPage, props?.data?.totalCount);

  return (
    <div className="ewallet_table_section">
      <div className="ewallet_table_section_cnt">
        <PayoutTableFilter
          setActiveTab={props.setActiveTab}
          activeTab={props.activeTab}
          headers={headers}
          data={props.data?.data}
          type={props.type}
        />
        <div className="table-responsive min-hieght-table">
          <TableMain
            headers={headers}
            data={props.data?.data}
            startPage={1}
            currentPage={props.currentPage}
            totalPages={lastPage}
            type={props.type}
            itemsPerPage={props.itemsPerPage}
            setItemsPerPage={props.setItemsPerPage}
            setCurrentPage={props.setCurrentPage}
          />
        </div>
      </div>
    </div>
  );
};

export default PayoutTable;
