import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import RepurchaseTable from "../../components/shopping/RepurchaseTable";
import { ApiHook } from "../../hooks/apiHook";

const RepurchaseReport = () => {
  const { t } = useTranslation();
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage, setItemsPerPage] = useState(10);

  //-------------------------------- API ---------------------------------------
  const report = ApiHook.CallRepurchaseReport(currentPage,itemsPerPage)
  
  return (
    <>
      <div className="page_head_top">{t("repurchase_report")}</div>
      <RepurchaseTable
        data={report.data}
        type={"repurchase-report"}
        setCurrentPage={setCurrentPage}
        currentPage={currentPage}
        itemsPerPage={itemsPerPage}
        setItemsPerPage={setItemsPerPage}
      />
    </>
  );
};

export default RepurchaseReport;
