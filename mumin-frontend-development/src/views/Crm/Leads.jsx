import React, { useState } from "react";
import LeadsTable from "../../components/Crm/LeadsTable";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
const Leads = () => {
  const { t } = useTranslation();
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const leadsData = ApiHook.CallGetLeads(currentPage, itemsPerPage);
  return (
    <>
      <div className="page_head_top">
        {t('leads')}
        <div className="right_btn_mob_toggle">
          <i className="fa fa-bars"></i>
        </div>
      </div>
      <LeadsTable
        tableData={leadsData?.data}
        currentPage={leadsData?.data?.leads?.currentPage}
        totalPages={leadsData?.data?.leads?.totalPages}
        setCurrentPage={setCurrentPage}
        itemsPerPage={itemsPerPage}
        setItemsPerPage={setItemsPerPage}
        replicaUrl={leadsData?.data?.replicaUrl}
      />
    </>
  );
};

export default Leads;
