import { useState } from "react";
import WebTreeNavbar from "../../components/Web/WebTreeNavbar";
import TreeViewContentWeb from "../../components/Web/TreeViewContentWeb";

const TreeViewWeb = () => {
  const [selectedUserId, setSelectedUserId] = useState("");
  const [searchUsername, setSearchUsername] = useState("");
  const [doubleUser, setDoubleUser] = useState("");
  return (
    <>
      {/* <WebTreeNavbar
        searchUsername={searchUsername}
        setSearchUsername={setSearchUsername}
      /> */}
      <TreeViewContentWeb />
    </>
  );
};

export default TreeViewWeb;
