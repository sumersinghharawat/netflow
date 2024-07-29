import React, { useState } from "react";
import "react-loading-skeleton/dist/skeleton.css";
import GenealogyTreeWeb from "../../components/Web/GenealogyTreeWeb";
import WebTreeNavbar from "../../components/Web/WebTreeNavbar";

const GenealogyTreeWebView = () => {
  const [selectedUserId, setSelectedUserId] = useState("");
  const [searchUsername, setSearchUsername] = useState("");
  const [doubleUser, setDoubleUser] = useState("");

  return (
    <>
      <WebTreeNavbar
        searchUsername={searchUsername}
        setSearchUsername={setSearchUsername}
      />
      <GenealogyTreeWeb
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

export default GenealogyTreeWebView;
