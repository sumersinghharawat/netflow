import React from "react";
import TreeViewNavbar from "../../components/Tree/TreeViewNavbar";
import TreeViewContent from "../../components/Tree/tree-view/TreeViewContent";

const TreeView = () => {
  return (
    <>
      <TreeViewNavbar menu={"treeView"} />
      <TreeViewContent
      />
    </>
  );
};

export default TreeView;
