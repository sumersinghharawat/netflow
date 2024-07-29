import React, { useEffect } from "react";
import TableContent from "../Common/table/TableContent";
import TablePagination from "../Common/table/TablePagination";
import { ApiHook } from "../../hooks/apiHook";
import { useState } from "react";
import SubmitButton from "../Common/buttons/SubmitButton";
import { useTranslation } from "react-i18next";
import Select from "react-select";
function ReferralMembersTable(props) {
  const { t } = useTranslation();
  const [itemsPerPage, setItemsPerPage] = useState(10);

  const [level, setlevel] = useState("all");
  const [selectedLevel, setselectedLvel] = useState("all");
  const referralMember = ApiHook.CallReferralMembers(
    level,
    props.currentPage,
    itemsPerPage
  );
  const referralHeader = ApiHook.CallReferralHead();
  const [levels, setLevels ] = useState([]);
  useEffect(() => {
    const options = [];
    options.push({ value: "all", label:`${t("all")}`});
    for (let i = 1; i <= referralHeader?.data?.data?.totalLevel; i++) {
      options.push({ value: i.toString(), label: i.toString() });
    }
    setLevels(options);
  }, [referralHeader?.data?.data?.totalLevel])
  const headers = [t("member_name"), t("placement"), t("sponsor"), t("level")];

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
      referralMember.data.data.currentPage < referralMember.data.data.totalPages
    ) {
      props.setCurrentPage(referralMember.data.data.currentPage + 1);
    }
  };

  const toLastPage = () => {
    props.setCurrentPage(referralMember?.data?.data?.totalPages);
  };
  const toPreviousPage = () => {
    if (referralMember?.data?.data.currentPage > startPage) {
      props.setCurrentPage(referralMember.data.data.currentPage - 1);
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
    <div className="ewallet_table_section">
      <div className="ewallet_table_section_cnt">
        <div className="filter_Section">
          <div className="row justify-content-between align-items-center">
            <div className="col-md-4">
              <div className="right_search_div d-flex gap-1">
                <div className="downMembBg">
                  <h5>{t("totalReferralMembers")}</h5>
                  <h4>{referralMember?.data?.data?.totalCount ?? "0"}</h4>
                </div>
                <div className="downMembBg">
                  <h5>{t("totalLevels")}</h5>
                  <h4>{referralHeader?.data?.data?.totalLevel ?? "0"}</h4>
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
            data={referralMember?.data?.data?.data}
            type="referral"
          />
          {referralMember?.data?.data?.data &&
            referralMember?.data?.data?.data?.length !== 0 && (
              <TablePagination
                startPage={startPage}
                currentPage={referralMember?.data?.data?.currentPage}
                totalPages={referralMember?.data?.data?.totalPages}
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
  );
}

export default ReferralMembersTable;
