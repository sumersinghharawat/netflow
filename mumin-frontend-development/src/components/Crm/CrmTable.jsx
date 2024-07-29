import React from "react";
import TablePagination from "../Common/table/TablePagination";
import CrmTableContent from "../Common/table/CrmTableContent";

const CrmTable = ({
  title,
  data,
  headers,
  setShowLeadEditModal,
  setShowAddLeadModal,
  setShowNextFollowUp,
  setLeadId,
  setLeadData,
  startPage,
  currentPage,
  totalPages,
  setCurrentPage,
  itemsPerPage,
  setItemsPerPage,
  type,
  handleEditClick,
  setApiCheck,
  check
}) => {

  const toNextPage = () => {
    if (currentPage < totalPages) {
      if (type === "today") {
        setCurrentPage({
          today: currentPage + 1,
          missed: currentPage,
          recent: currentPage,
        });
      } else if (type === "missed") {
        setCurrentPage({
          today: currentPage,
          missed: currentPage + 1,
          recent: currentPage,
        });
      } else {
        setCurrentPage({
          today: currentPage,
          missed: currentPage,
          recent: currentPage + 1,
        });
        if(check){
          setApiCheck(true)
        }
      }
    }
  };

  const toLastPage = () => {
    if (type === "today") {
      setCurrentPage({
        today: totalPages,
        missed: 1,
        recent: 1,
      });
    } else if (type === "missed") {
      setCurrentPage({
        today: 1,
        missed: totalPages,
        recent: 1,
      });
    } else {
      setCurrentPage({
        today: 1,
        missed: 1,
        recent: totalPages,
      });
      if(check){
        setApiCheck(true)
      }
    }
  };

  const toPreviousPage = () => {
    if (currentPage > startPage) {
      if (type === "today") {
        setCurrentPage({
          today: currentPage - 1,
          missed: 1,
          recent: 1,
        });
      } else if (type === "missed") {
        setCurrentPage({
          today: 1,
          missed: currentPage - 1,
          recent: 1,
        });
      } else {
        setCurrentPage({
          today: 1,
          missed: 1,
          recent: currentPage - 1,
        });
        if(check){
          setApiCheck(true)
        }
      }
    }
  };

  const toFirstPage = () => {
    if (type === "today") {
      setCurrentPage({
        today: startPage,
        missed: 1,
        recent: 1,
      });
    } else if (type === "missed") {
      setCurrentPage({
        today: 1,
        missed: startPage,
        recent: 1,
      });
    } else {
      setCurrentPage({
        today: 1,
        missed: 1,
        recent: startPage,
      });
      if(check){
        setApiCheck(true)
      }
    }
  };
  const handleItemsPerPageChange = (event) => {
    const selectedValue = parseInt(event.target.value);
    if (type === "today") {
      setItemsPerPage({ ...itemsPerPage, today: selectedValue });
    } else if (type === "missed") {
      setItemsPerPage({ ...itemsPerPage, missed: selectedValue });
    } else {
      setItemsPerPage({ ...itemsPerPage, recent: selectedValue });
    }
    setCurrentPage({ today: 1, missed: 1, recent: 1 });
  };
  return (
    <div className="joining_Teammbr_section">
      <div className="row">
        <div className="col-xl-12 col-lg-12 col-md-12">
          <div className="crm_panel_body__31wq1 pannel-sec">
            <legend>
              <span>{title}</span>
            </legend>
            <div className="crm_table_responsive__cut_1">
              <CrmTableContent
                data={data}
                headers={headers}
                setShowLeadEditModal={setShowLeadEditModal}
                setShowAddLeadModal={setShowAddLeadModal}
                setShowNextFollowUp={setShowNextFollowUp}
                setLeadId={setLeadId}
                setLeadData={setLeadData}
                type={type}
                handleEditClick={handleEditClick}
              />
              {data?.length > 0 && (
                <TablePagination
                  startPage={startPage}
                  currentPage={currentPage}
                  totalPages={totalPages}
                  itemsPerPage={
                    type === "today"
                      ? itemsPerPage.today
                      : type === "missed"
                      ? itemsPerPage.missed
                      : itemsPerPage.recent
                  }
                  toNextPage={toNextPage}
                  toLastPage={toLastPage}
                  toPreviousPage={toPreviousPage}
                  toFirstPage={toFirstPage}
                  handleItemsPerPageChange={handleItemsPerPageChange}
                />
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CrmTable;
