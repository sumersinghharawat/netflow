import React, { useEffect, useState } from "react";
import TableContent from "../Common/table/TableContent";
import TablePagination from "../Common/table/TablePagination";
import { ApiHook } from "../../hooks/apiHook";
import SubmitButton from "../Common/buttons/SubmitButton";
import { useTranslation } from "react-i18next";
import Select from "react-select";
const DownlineMembersTable = (props) => {
  const { t } = useTranslation();
  const [itemsPerPage, setItemsPerPage] = useState(10);
  const headers = [t("member_name"), t("placement"), t("sponsor"), t("level")];
  const [level, setlevel] = useState("all");
  const [selectedLevel, setselectedLvel] = useState("all");
  const downlineMember = ApiHook.CallDownlineMembers(
    level,
    props.currentPage,
    itemsPerPage
  );
  const downlineHeader = ApiHook.CallDownlineHead();

  const [levels, setLevels] = useState([]);
  useEffect(() => {
    const options = [];
    options.push({ value: "all", label: `${t("all")}` });
    for (let i = 1; i <= downlineHeader?.data?.data?.totalLevel; i++) {
      options.push({ value: i.toString(), label: i.toString() });
    }
    setLevels(options);
  }, [downlineHeader?.data?.data?.totalLevel]);

  const levelChangehandler = (data) => {
    setselectedLvel(data?.value);
  };

  const SearchHandler = () => {
    setlevel(selectedLevel);
    props.setCurrentPage(1);
  };

  const resetHandler = () => {
    setlevel("all");
    setselectedLvel("all");
  };
  let startPage = 1;
  const toNextPage = () => {
    if (
      downlineMember.data.data.currentPage < downlineMember.data.data.totalPages
    ) {
      props.setCurrentPage(downlineMember.data.data.currentPage + 1);
    }
  };

  const toLastPage = () => {
    props.setCurrentPage(downlineMember.data.data.totalPages);
  };
  const toPreviousPage = () => {
    if (downlineMember.data.data.currentPage > startPage) {
      props.setCurrentPage(downlineMember.data.data.currentPage - 1);
    }
  };

  const toFirstPage = () => {
    props.setCurrentPage(startPage);
  };
  const handleItemsPerPageChange = (event) => {
    const selectedValue = parseInt(event.target.value);
    setItemsPerPage(selectedValue);
    props.setCurrentPage(1);
  };

  return (
    <>
      <div className="ewallet_table_section">
        <div className="ewallet_table_section_cnt">
          <div className="filter_Section">
            <div className="row justify-content-between align-items-center">
              <div className="col-md-4">
                <div className="right_search_div d-flex gap-1">
                  <div className="downMembBg">
                    <h5>{t("totalDownlineMembers")}</h5>
                    <h4>{downlineMember?.data?.data?.totalCount ?? "0"}</h4>
                  </div>
                  <div className="downMembBg">
                    <h5>{t("totalLevels")}</h5>
                    <h4>{downlineHeader?.data?.data?.totalLevel ?? "0"}</h4>
                  </div>
                </div>
              </div>
              <div className="col-md-4">
                <div className="right_search_div d-flex gap-1">
                  <Select
                    isSearchable={false}
                    value={levels.find((item) => item?.value === selectedLevel)}
                    onChange={levelChangehandler}
                    options={levels}
                  />
                  <SubmitButton
                    isSubmitting=""
                    text="search"
                    className="btn btn-primary"
                    click={SearchHandler}
                    id="searchButton"
                  />
                  <SubmitButton
                    isSubmitting=""
                    text="reset"
                    className="btn btn-secondary"
                    click={resetHandler}
                    id="resetButton"
                  />
                </div>
              </div>
            </div>
          </div>
          <div className="table-responsive min-hieght-table">
            <TableContent
              headers={headers}
              data={downlineMember?.data?.data?.data}
              type="downline"
            />
            {downlineMember?.data?.data?.data &&
              downlineMember?.data?.data?.data?.length !== 0 && (
                <TablePagination
                  startPage={startPage}
                  currentPage={downlineMember?.data?.data?.currentPage}
                  totalPages={downlineMember?.data?.data?.totalPages}
                  setCurrentPage={props.setCurrentPage}
                  itemsPerPage={itemsPerPage}
                  setItemsPerPage={setItemsPerPage}
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
    </>
  );
};

export default DownlineMembersTable;