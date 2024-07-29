import React, { useState } from "react";
import { useSelector } from "react-redux";
import TreeViewNode from "./TreeViewNode";
import { TreeViewSkeleton } from "../TreeViewSkeleton";
import { ApiHook } from "../../../hooks/apiHook";
const TreeViewContent = () => {
  const [selectedUserId, setSelectedUserId] = useState("");
  const treeListData = ApiHook.CallTreeViewList(selectedUserId);
  const TreeViewList = useSelector((state) => state.tree?.treeViewList);
  return (
    <div className="tree_view_content_section">
      <svg
        className="tree__background"
        style={{ height: "100%", width: "100%" }}
      >
        <pattern
          id="pattern-83098"
          x="6"
          y="0"
          width="10"
          height="10"
          patternUnits="userSpaceOnUse"
        >
          <circle cx="0.4" cy="0.4" r="0.4" fill="#81818a"></circle>
        </pattern>
        <rect
          x="0"
          y="0"
          width="100%"
          height="100%"
          fill="url(#pattern-83098)"
        ></rect>
      </svg>
      <div id="collapseDVR3" className="">
        <div className="tree">
          {(treeListData.isLoading && selectedUserId ==="") ? (
            <TreeViewSkeleton />
          ) : (
            <ul>
              {Array.isArray(TreeViewList?.data) &&
                TreeViewList.data.map((item, index) => {
                  return (
                    <TreeViewNode
                      data={item}
                      key={index}
                      setSelectedUserId={setSelectedUserId}
                      selectedUserId={selectedUserId}
                      isLoading={treeListData.isLoading}
                    />
                  );
                })}
            </ul>
          )}
        </div>
      </div>
    </div>
  );
};

export default TreeViewContent;
