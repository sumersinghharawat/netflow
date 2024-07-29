import React from "react";
import { Form } from "react-bootstrap";
import SubmitButton from "../buttons/SubmitButton";
import { useState } from "react";
import { ApiHook } from "../../../hooks/apiHook";

const LeadsTableFilter = (props) => {
  const [searchKey, setSearchKey] = useState("");
  const searchResultMutation = ApiHook.CallSearchLeads();

  const handleChange = (e) => {
    const { value } = e.target;
    setSearchKey(value);
  };
  const handleSearch = () => {
    searchResultMutation.mutateAsync(searchKey, {
      onSuccess: (res) => {
        props?.setTableData({ leads: { rows: res?.data?.data } });
      },
    });
  };

  const handleKeyPress = (e) => {
    if (e.key === "Enter") {
      // If Enter key is pressed, trigger the search
      handleSearch();
    }
  };

  const resetHandler = () => {
    setSearchKey("");
    searchResultMutation.mutateAsync("", {
      onSuccess: (res) => {
        props?.setTableData({ leads: { rows: res?.data?.data } });
      },
    });
  };

  return (
    <>
      <div className="filter_Section">
        <div className="row justify-content-end">
          <div className="col-md-4">
            <div className="right_search_div d-flex gap-1 nav-bar-flex">
              <Form.Group>
                <Form.Control
                  id="Search"
                  type="text"
                  placeholder="Search"
                  onChange={(e) => handleChange(e)}
                  onKeyPress={(e) => handleKeyPress(e)}
                  value={searchKey}
                  required
                />
              </Form.Group>
              <SubmitButton
                className="btn btn-primary"
                text="search"
                click={handleSearch}
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
    </>
  );
};

export default LeadsTableFilter;
