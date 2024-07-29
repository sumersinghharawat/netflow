import React, { useState } from "react";
import TableMain from "../Common/table/TableMain";
import EwalletTableFilter from "../Common/table/EwalletTableFilter";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";
import { Link } from "react-router-dom";
import { useSelector } from "react-redux";

const EwalletTable = (props) => {
  const { t } = useTranslation();
  const [activeTab, setActiveTab] = useState("statement");
  const [selectStatement, setSelectedStatement] = useState(false);
  const [selectedTransfer, setSelectedTransfer] = useState(false);
  const [selectedPurchase, setSelectedPurchase] = useState(false);
  const [selectedEarnings, setSelectedEarnings] = useState(false);
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const [selectedCategory, setSelectedCategory] = useState([]);
  const [dateRange, setDateRange] = useState({ startDate: "", endDate: "" });
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );
  //------------------------------------------- API -------------------------------------------
  const statement = ApiHook.CallEwalletStatement(
    props.currentPage,
    itemsPerPage,
    selectStatement
  );
  const transferHistory = ApiHook.CallTransferHistory(
    props.currentPage,
    itemsPerPage,
    selectedTransfer,
    setSelectedTransfer,
    selectedCategory,
    dateRange?.startDate,
    dateRange?.endDate
  );
  const purchaseHistory = ApiHook.CallPurchaseHistory(
    props.currentPage,
    itemsPerPage,
    selectedPurchase,
    setSelectedPurchase
  );
  const myEarnings = ApiHook.CallMyEarnings(
    props.currentPage,
    itemsPerPage,
    selectedEarnings,
    setSelectedEarnings,
    selectedCategory,
    dateRange?.startDate,
    dateRange?.endDate
  );
   
  const handleTabChange = (tab) => {
    if(tab === 'transfer_history' || tab === 'my_earnings'){
      setSelectedCategory("")
    }
    setActiveTab(tab);
    props.setCurrentPage(1);
    setApiTab(tab);
  };
  const setApiTab = (tab) => {
    if (tab === "transfer_history") {
      setSelectedTransfer(true);
    } else if (tab === "purchase_wallet") {
      setSelectedPurchase(true);
    } else if (tab === "my_earnings") {
      setSelectedEarnings(true);
    } else {
      setSelectedStatement(true);
    }
  };
  const headers =
    activeTab !== "my_earnings"
      ? [t("description"), t("amount"), t("transaction_date"), t("balance")]
      : [
          t("description"),
          t("total_amount"),
          t("TDS"),
          t("service_charge"),
          t("amount_payable"),
          t("transaction_date"),
        ];

  return (
    <div className="ewallet_table_section">
      <div className="ewallet_table_section_cnt">
        <div className="ewallet_table_section_cnt_tab_head">
          <Link
            className={`ewallet_tab_btn ${
              activeTab === "statement" ? "active" : ""
            }`}
            onClick={() => handleTabChange("statement")}
          >
            {t("statement")}
          </Link>
          <Link
            className={`ewallet_tab_btn ${
              activeTab === "transfer_history" ? "active" : ""
            }`}
            onClick={() => handleTabChange("transfer_history")}
          >
            {t("transfer_history")}
          </Link>
          {!!moduleStatus?.purchase_wallet && (
            <Link
              className={`ewallet_tab_btn ${
                activeTab === "purchase_wallet" ? "active" : ""
              }`}
              onClick={() => handleTabChange("purchase_wallet")}
            >
              {t("purchase_wallet")}
            </Link>
          )}
          <Link
            className={`ewallet_tab_btn ${
              activeTab === "my_earnings" ? "active" : ""
            }`}
            onClick={() => handleTabChange("my_earnings")}
          >
            {t("my_earnings")}
          </Link>
        </div>
      </div>
      <div className="table-responsive min-hieght-table">
        {activeTab === "statement" && (
          <TableMain
            headers={headers}
            data={statement?.data?.data}
            startPage={1}
            currentPage={statement?.data?.currentPage}
            totalPages={statement?.data?.totalPages}
            setCurrentPage={props.setCurrentPage}
            type={"ewallet"}
            itemsPerPage={itemsPerPage}
            setItemsPerPage={setItemsPerPage}
            activeTab={activeTab}
            setApiTab={setApiTab}
          />
        )}
        {activeTab === "transfer_history" && (
          <>
            <EwalletTableFilter
              type={activeTab}
              setApiTab={setApiTab}
              selectedCategory={selectedCategory}
              setSelectedCategory={setSelectedCategory}
              setDateRange={setDateRange}
            />
            <TableMain
              headers={headers}
              data={transferHistory?.data?.data}
              startPage={1}
              currentPage={transferHistory?.data?.currentPage}
              totalPages={transferHistory?.data?.totalPages}
              setCurrentPage={props.setCurrentPage}
              type={"ewallet"}
              itemsPerPage={itemsPerPage}
              setItemsPerPage={setItemsPerPage}
              activeTab={activeTab}
              setApiTab={setApiTab}
            />
          </>
        )}
        {activeTab === "purchase_wallet" && (
          <TableMain
            headers={headers}
            data={purchaseHistory?.data?.data}
            startPage={1}
            currentPage={props.currentPage}
            totalPages={purchaseHistory?.data?.totalPages}
            setCurrentPage={props.setCurrentPage}
            type={"ewallet"}
            itemsPerPage={itemsPerPage}
            setItemsPerPage={setItemsPerPage}
            activeTab={activeTab}
            setApiTab={setApiTab}
          />
        )}
        {activeTab === "my_earnings" && (
          <>
            <EwalletTableFilter 
            type={activeTab}
            setApiTab={setApiTab}
            selectedCategory={selectedCategory}
            setSelectedCategory={setSelectedCategory}
            setDateRange={setDateRange}
            category={myEarnings.data?.dropdown} />
            <TableMain
              headers={headers}
              data={myEarnings?.data?.data}
              startPage={1}
              currentPage={props.currentPage}
              totalPages={myEarnings?.data?.totalPages}
              setCurrentPage={props.setCurrentPage}
              type={"ewallet"}
              itemsPerPage={itemsPerPage}
              setItemsPerPage={setItemsPerPage}
              activeTab={activeTab}
              setApiTab={setApiTab}
            />
          </>
        )}
      </div>
    </div>
  );
};

export default EwalletTable;
