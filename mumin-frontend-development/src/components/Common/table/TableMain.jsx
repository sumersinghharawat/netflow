import React from "react";
import TableContent from "./TableContent";
import TablePagination from "./TablePagination";

const TableMain = ({
  headers,
  data,
  type,
  startPage,
  currentPage,
  totalPages,
  setCurrentPage,
  itemsPerPage,
  setItemsPerPage,
  activeTab,
  setApiTab,
  handleEditClick,
}) => {
  const toNextPage = () => {
    if (currentPage < totalPages) {
      setCurrentPage(currentPage + 1);
      if (type === "ewallet" || type === "epin") {
        setApiTab(activeTab);
      }
    }
  };

  const toLastPage = () => {
    setCurrentPage(totalPages);
    if (type === "ewallet" || type === "epin") {
      setApiTab(activeTab);
    }
  };

  const toPreviousPage = () => {
    if (currentPage > startPage) {
      setCurrentPage(currentPage - 1);
      if (type === "ewallet" || type === "epin") {
        setApiTab(activeTab);
      }
    }
  };

  const toFirstPage = () => {
    setCurrentPage(startPage);
    if (type === "ewallet" || type === "epin") {
      setApiTab(activeTab);
    }
  };

  const handleItemsPerPageChange = (event) => {
    const selectedValue = parseInt(event.target.value);
    setItemsPerPage(selectedValue);
    setCurrentPage(1);
    if (type === "ewallet" || type === "epin") {
      setApiTab(activeTab);
    }
  };
  return (
    <>
      <TableContent
        headers={headers}
        data={data}
        type={type}
        handleEditClick={handleEditClick}
      />
      {data && data?.length !== 0 && (
        <TablePagination
          startPage={startPage}
          currentPage={currentPage}
          totalPages={totalPages}
          setCurrentPage={setCurrentPage}
          itemsPerPage={itemsPerPage}
          setItemsPerPage={setItemsPerPage}
          toNextPage={toNextPage}
          toLastPage={toLastPage}
          toPreviousPage={toPreviousPage}
          toFirstPage={toFirstPage}
          handleItemsPerPageChange={handleItemsPerPageChange}
        />
      )}
    </>
  );
};

export default TableMain;
