import React, { useState } from "react";
import TableMain from "../Common/table/TableMain";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
import EpinFilter from "./EpinFilter";

const EpinTable = ({ type, selectedPending, setSelectedPending }) => {
  let headers;
  const { t } = useTranslation();
  const [activeTab, setActiveTab] = useState("epin-list");
  const [selectedHistory, setSelectedHistory] = useState(false);
  const [currentPage, setCurrentPage] = useState(1);
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [selectedOptions, setSelectedOptions] = useState({
    epin: "",
    selectedAmountOption: "",
    selectedStatusOption: "",
  });

  //------------------------------------ API -------------------------------------------

  const epinList = ApiHook.CallEpinList(
    currentPage,
    itemsPerPage,
    selectedOptions.epin,
    selectedOptions.selectedAmountOption,
    selectedOptions.selectedStatusOption
  );
  const partials = ApiHook.CallEpinPartials();
  const epinPending = ApiHook.CallEpinPendingRequest(
    currentPage,
    itemsPerPage,
    selectedPending,
    setSelectedPending
  );
  const epinHistory = ApiHook.CallEpinHistory(
    currentPage,
    itemsPerPage,
    selectedHistory,
    setSelectedHistory
  );

  const setApiTab = (tab) => {
    if (tab === "epin-pending") {
      setSelectedPending(true);
    } else if (tab === "epin-transfer") {
      setSelectedHistory(true);
    }
  };
  if (activeTab === "epin-list") {
    headers = [
      t("epin"),
      t("amount"),
      t("balance_amount"),
      t("status"),
      t("expiry_date"),
      t("action"),
    ];
  } else if (activeTab === "epin-pending") {
    headers = [
      t("requested_date"),
      t("expiry_date"),
      t("requested_pin_count"),
      t("amount"),
    ];
  } else {
    headers = [
      t("member_name"),
      t("epin"),
      t("amount"),
      t("transfered_date"),
      t("transfered_received"),
    ];
  }
  const handleTabChange = (tab) => {
    setActiveTab(tab);
    setApiTab(tab);
  };

  return (
    <div className="ewallet_table_section">
      <div className="ewallet_table_section_cnt">
        <div className="ewallet_table_section_cnt_tab_head">
          <a
            href="#"
            className={`ewallet_tab_btn ${
              activeTab === "epin-list" ? "active" : ""
            }`}
            onClick={() => handleTabChange("epin-list")}
          >
            {t("epinList")}
          </a>
          <a
            href="#"
            className={`ewallet_tab_btn ${
              activeTab === "epin-pending" ? "active" : ""
            }`}
            onClick={() => handleTabChange("epin-pending")}
          >
            {t("pendingEpinRequest")}
          </a>
          <a
            href="#"
            className={`ewallet_tab_btn ${
              activeTab === "epin-transfer" ? "active" : ""
            }`}
            onClick={() => handleTabChange("epin-transfer")}
          >
            {t("epinTransferHistory")}
          </a>
        </div>
        <div className="table-responsive min-hieght-table">
          {activeTab === "epin-list" && (
            <>
              <EpinFilter
                selectedOptions={selectedOptions}
                setSelectedOptions={setSelectedOptions}
                partials={partials.data}
              />
              <TableMain
                headers={headers}
                data={epinList?.data?.data}
                startPage={1}
                currentPage={currentPage}
                totalPages={epinList?.data?.totalPages}
                setCurrentPage={setCurrentPage}
                itemsPerPage={itemsPerPage}
                setItemsPerPage={setItemsPerPage}
                activeTab={activeTab}
                setApiTab={setApiTab}
                type={type}
              />
            </>
          )}
          {activeTab === "epin-pending" && (
            <TableMain
              headers={headers}
              data={epinPending?.data?.data}
              startPage={1}
              currentPage={currentPage}
              totalPages={epinPending?.data?.totalPages}
              setCurrentPage={setCurrentPage}
              itemsPerPage={itemsPerPage}
              setItemsPerPage={setItemsPerPage}
              activeTab={activeTab}
              setApiTab={setApiTab}
              type={type}
              setSelectedPending={setSelectedPending}
            />
          )}
          {activeTab === "epin-transfer" && (
            <TableMain
              headers={headers}
              data={epinHistory?.data?.data}
              startPage={1}
              currentPage={currentPage}
              totalPages={epinHistory?.data?.totalPages}
              setCurrentPage={setCurrentPage}
              itemsPerPage={itemsPerPage}
              setItemsPerPage={setItemsPerPage}
              activeTab={activeTab}
              setApiTab={setApiTab}
              type={type}
            />
          )}
        </div>
      </div>
    </div>
  );
};

export default EpinTable;
