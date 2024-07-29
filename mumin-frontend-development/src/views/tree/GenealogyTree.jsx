import React, { useState } from "react";
import 'react-loading-skeleton/dist/skeleton.css'
import TreeViewNavbar from "../../components/Tree/TreeViewNavbar";
import GenealogyTreeContent from "../../components/Tree/genealogy-tree/GenealogyTreeContent";

const GenealogyTree = () => {
  const [selectedUserId, setSelectedUserId] = useState("");
  const [searchUsername, setSearchUsername] = useState("");
  const [doubleUser, setDoubleUser] = useState("");

  
  return (
    <>
      <TreeViewNavbar
        menu={"genealogyTree"}
        searchUsername={searchUsername}
        setSearchUsername={setSearchUsername}
      />
      <GenealogyTreeContent
        selectedUserId={selectedUserId}
        setSelectedUserId={setSelectedUserId}
        doubleUser={doubleUser}
        setDoubleUser={setDoubleUser}
        searchUsername={searchUsername}
        setSearchUsername={setSearchUsername}
      />
    </>
  );
};

export default GenealogyTree;
