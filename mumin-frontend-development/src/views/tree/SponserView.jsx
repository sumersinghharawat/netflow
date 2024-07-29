import TreeViewNavbar from "../../components/Tree/TreeViewNavbar";
import SponserTreeContent from "../../components/Tree/sponser-tree/SponserTreeContent";
import { useState } from "react";

const SponserTree = () => {
  const [selectedUserId, setSelectedUserId] = useState("");
  const [searchUsername, setSearchUsername] = useState("");
  const [doubleClickedUser, setDoubleClickedUser] = useState("");

  return (
    <>
      <TreeViewNavbar
        menu={"sponsorTree"}
        searchUsername={searchUsername}
        setSearchUsername={setSearchUsername}
      />
      <SponserTreeContent
        selectedUserId={selectedUserId}
        setSelectedUserId={setSelectedUserId}
        doubleClickedUser={doubleClickedUser}
        setDoubleClickedUser={setDoubleClickedUser}
        searchUsername={searchUsername}
        setSearchUsername={setSearchUsername}
      />
    </>
  );
};

export default SponserTree;
