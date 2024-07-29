import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import TicketHeader from "../../components/Support/TicketHeader";
import TicketsTable from "../../components/Support/TicketsTable";
import { ApiHook } from "../../hooks/apiHook";

const MainSupport = () => {
  const { t } = useTranslation();
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [currentPage, setCurrentPage] = useState(1);
  const [selectedOptions, setSelectedOptions] = useState({
    ticketId: "",
    selectedCategoryOption: "",
    selectedPriorityOption: "",
    selectedStatusOption: "",
  });

  //------------------------------------------- API -----------------------------------------

  const tickets = ApiHook.CallTickets(
    currentPage,
    itemsPerPage,
    selectedOptions.selectedCategoryOption,
    selectedOptions.selectedPriorityOption,
    selectedOptions.ticketId,
    selectedOptions.selectedStatusOption,
  );

  return (
    <>
      <div className="page_head_top">{t("support")}</div>
      <TicketHeader />
      <TicketsTable
        data={tickets?.data}
        type={"ticket"}
        setCurrentPage={setCurrentPage}
        currentPage={currentPage}
        itemsPerPage={itemsPerPage}
        setItemsPerPage={setItemsPerPage}
        selectedOptions={selectedOptions}
        setSelectedOptions={setSelectedOptions}
      />
    </>
  );
};

export default MainSupport;
