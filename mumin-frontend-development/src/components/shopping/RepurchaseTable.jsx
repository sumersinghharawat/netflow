import React from "react";
import TableMain from "../Common/table/TableMain";
import { getLastPage } from "../../utils/getLastPage";
import { useTranslation } from "react-i18next";
import RepurchaseTableFilter from "../Common/table/RepurchaseTableFilter";

const RepurchaseTable = (props) => {
  const { t } = useTranslation();
  const headers = [
    t('slno'),
    t("invoice_no"),
    t("total_amount"),
    t("payment_method"),
    t("purchase_date"),
    t('status')
  ];
  const lastPage = getLastPage(props.itemsPerPage, props?.data?.totalCount);

  return (
    <div className="ewallet_table_section">
      <div className="ewallet_table_section_cnt">
        <RepurchaseTableFilter headers={headers} data={props.data?.data} type={props.type}/>
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

export default RepurchaseTable;
