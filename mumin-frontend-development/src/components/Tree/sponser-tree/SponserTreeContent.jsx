import React, { useState, useRef } from "react";
import SponserTreeComponent from "./SponserTreeComponent";
import { PanZoom } from "react-easy-panzoom";
import Loader from "react-js-loader";
import { useDispatch, useSelector } from "react-redux";
import { disableSponserBackToParent } from "../../../store/reducers/treeReducer";
import { ApiHook } from "../../../hooks/apiHook";
const SponserTreeContent = (props) => {
  const dispatch = useDispatch();
  const [isFullScreen, setIsFullScreen] = useState(false);
  const [showLoader, setShowLoader] = useState(false);
  const backEnabled = useSelector((state) => state?.tree?.sponserBackToParent);
  const panZoomRef = useRef(null);

  const treeData = ApiHook.CallSponsorTreeList(
    props?.selectedUserId,
    props?.doubleClickedUser
  );

  const handleZoomIn = () => {
    panZoomRef.current.zoomIn();
  };

  const handleZoomOut = () => {
    panZoomRef.current.zoomOut();
  };

  const handleZoomReset = () => {
    panZoomRef.current.reset();
  };

  const handleExpand = () => {
    const bodyElement = document.querySelector(
      ".body.genealogy-body.genealogy-scroll"
    );
    if (!isFullScreen) {
      if (bodyElement.requestFullscreen) {
        bodyElement.requestFullscreen();
      } else if (bodyElement.mozRequestFullScreen) {
        bodyElement.mozRequestFullScreen();
      } else if (bodyElement.webkitRequestFullscreen) {
        bodyElement.webkitRequestFullscreen();
      } else if (bodyElement.msRequestFullscreen) {
        bodyElement.msRequestFullscreen();
      }
    } else {
      if (document.exitFullscreen) {
        document.exitFullscreen();
      } else if (document.mozCancelFullScreen) {
        document.mozCancelFullScreen();
      } else if (document.webkitExitFullscreen) {
        document.webkitExitFullscreen();
      } else if (document.msExitFullscreen) {
        document.msExitFullscreen();
      }
    }

    setIsFullScreen(!isFullScreen);
  };

  const backToParent = () => {
    props.setSelectedUserId("");
    props.setDoubleClickedUser("");
    props.setSearchUsername("");
    dispatch(disableSponserBackToParent());
  };

  return (
    <div>
      <div className="tree_view_content_section">
        <div className="body genealogy-body genealogy-scroll">
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

          <div className="genealogy-tree">
            <div className="tree_view_action_btn">
              <a className="btn btn-tree-act" onClick={handleZoomIn}>
                <i className="fa fa-plus"></i>
              </a>
              <a className="btn btn-tree-act" onClick={handleZoomOut}>
                <i className="fa fa-minus"></i>
              </a>
              <a className="btn btn-tree-act" onClick={handleZoomReset}>
                <i className="fa fa-refresh"></i>
              </a>
              <a className="btn btn-tree-act" onClick={handleExpand}>
                <i className="fa fa-expand"></i>
              </a>
            </div>
            <div id="container">
              <PanZoom
                disableScrollZoom
                disableDoubleClickZoom
                ref={panZoomRef}
              >
                {backEnabled && (
                  <span
                    title="Back to parant"
                    className="parent_back_btn"
                    style={{ marginTop: "17px" }}
                    onClick={backToParent}
                  >
                    <i className="fa fa-angle-left"></i>
                  </span>
                )}
                <ul>
                  {treeData.isLoading && props?.selectedUserId === "" ? (
                    <>
                      <div className="member-view-box">
                        <div className="member-image">
                          <img src="/images/user-profile.png" alt="Member" />
                          <Loader
                            type="spinner-default"
                            bgColor={"#954cea"}
                            color={"#954cea"}
                            size={25}
                          />
                        </div>
                      </div>
                    </>
                  ) : (
                    <SponserTreeComponent
                      setShowLoader={setShowLoader}
                      selectedUserId={props.selectedUserId}
                      setSelectedUserId={props.setSelectedUserId}
                      doubleClickedUser={props.doubleClickedUser}
                      setDoubleClickedUser={props.setDoubleClickedUser}
                    />
                  )}
                </ul>
              </PanZoom>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default SponserTreeContent;
