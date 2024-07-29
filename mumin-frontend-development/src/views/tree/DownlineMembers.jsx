import React from "react";
import DownlineMembersTable from "../../components/Tree/DownlineMembersTable";
import { useState } from "react";
import TreeViewNavbar from "../../components/Tree/TreeViewNavbar";

const DownlineMembers = () => {
  const [currentPage, setCurrentPage] = useState(1);

  return (
    <>
      <TreeViewNavbar menu={"downlineMembers"} />
      <DownlineMembersTable
        currentPage={currentPage}
        setCurrentPage={setCurrentPage}
      />
    </>
  );
};

export default DownlineMembers;
