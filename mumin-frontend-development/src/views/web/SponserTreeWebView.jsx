import { useState } from "react";
import SponserTreeWeb from "../../components/Web/SponserTreeWeb";
import WebTreeNavbar from "../../components/Web/WebTreeNavbar";

const SponserTreeWebView = () => {
  const [selectedUserId, setSelectedUserId] = useState("");
  const [searchUsername, setSearchUsername] = useState("");
  const [doubleClickedUser, setDoubleClickedUser] = useState("");

  return (
    <>
    <WebTreeNavbar
        menu={"genealogyTree"}
        searchUsername={searchUsername}
        setSearchUsername={setSearchUsername}
      />
      <SponserTreeWeb
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

export default SponserTreeWebView;