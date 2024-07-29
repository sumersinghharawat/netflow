import React from "react";
import TableMain from "../Common/table/TableMain";
import { useTranslation } from "react-i18next";
import { getLastPage } from "../../utils/getLastPage";
import TicketFilter from "./TicketFilter";
import { ApiHook } from "../../hooks/apiHook";

const TicketsTable = (props) => {
  const { t } = useTranslation();
  const lastPage = getLastPage(props.itemsPerPage, props?.data?.totalCount);
  const headers = [
    t("slno"),
    t("ticket_id"),
    t("subject"),
    t("assignee"),
    t("status"),
    t("category"),
    t("priority"),
    t("created_on"),
    t("last_updated"),
    t("timeline"),
  ];

  // ---------------------------------------- API --------------------------------
  const partials = ApiHook.CallTicketPartials();

  
  return (
    <div className="ewallet_table_section">
      <div className="ewallet_table_section_cnt">
        <TicketFilter
          selectedOptions={props.selectedOptions}
          setSelectedOptions={props.setSelectedOptions}
          partials={partials}
        />
        <div className="table-responsive min-hieght-table ticket_system">
          <TableMain
            headers={headers}
            data={props.data?.data}
            startPage={1}
            currentPage={props?.data?.currentPage}
            totalPages={lastPage}
            setCurrentPage={props.setCurrentPage}
            type={'ticket'}
            itemsPerPage={props.itemsPerPage}
            setItemsPerPage={props.setItemsPerPage}
          />
        </div>
      </div>
    </div>
  );
};

export default TicketsTable;
