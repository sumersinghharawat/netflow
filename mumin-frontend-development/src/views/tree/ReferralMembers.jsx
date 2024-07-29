import React from "react";
import ReferralMembersTable from "../../components/Tree/ReferralMembersTable";
import { useState } from "react";
import TreeViewNavbar from "../../components/Tree/TreeViewNavbar";

function ReferralMembers() {
  const [currentPage, setCurrentPage] = useState(1);
  
  return (
    <>
      <TreeViewNavbar menu={"referralMembers"} />
      <ReferralMembersTable
        currentPage={currentPage}
        setCurrentPage={setCurrentPage}
      />
    </>
  );
}

export default ReferralMembers;
