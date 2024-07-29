import React, { useEffect, useRef, useState } from "react";
import { OverlayTrigger, Popover } from "react-bootstrap";
import { useSelector } from "react-redux";
import { formatDate } from "../../../utils/formateDate";
import { useTranslation } from "react-i18next";
import anime from "animejs/lib/anime.es.js";
import { ApiHook } from "../../../hooks/apiHook";
import Loader from "react-js-loader";

const SponserTreeNode = (props) => {
  const { t } = useTranslation();
  const NoProfile = "/images/user-profile.png";
  const [expanded, setExpanded] = useState(true);
  const [hoveredItemId, setHoveredItemId] = useState([]);
  const [showLoader, setShowLoader] = useState(false);
  const [isMoreId, setIsMoreId] = useState({
    sponserId: null,
    position: null,
    fatherId: null,
  });
  const [showPopover, setShowPopover] = useState(false);
  const [copied, setCopied] = useState(false);
  const listRef = useRef(null);
  const updatedTree = ApiHook.CallSponserTreeMore(isMoreId);
  const setSelectedUserId = props.setSelectedUserId;
  const setDoubleClickedUser = props.setDoubleClickedUser;

  const HandleExpand = (data) => {
    if (data?.children?.length === 0 && data?.attributes?.childrenCount > 0) {
      setSelectedUserId(data?.attributes?.id);
    } else {
      setExpanded(!expanded);
    }
  };

  const handleItemHover = (itemId) => {
    setHoveredItemId(itemId);
  };

  const handleDoubleClick = (data) => {
    setDoubleClickedUser(data?.attributes?.id);
  };

  const handleIsMore = (data) => {
    setIsMoreId((prev) => ({
      ...prev,
      sponsorId: data?.sponsorId,
      position: data?.position,
      fatherId: data?.fatherId,
    }));
    if (updatedTree?.isLoading) {
      setShowLoader(true);
    } else {
      setShowLoader(false);
    }
  };

  const handlePopoverClick = (e) => {
    e.stopPropagation();
  };
  const handlePopoverDoubleClick = (e) => {
    e.stopPropagation();
  };
  const copyToClipboard = async (text) => {
    if (navigator && navigator.clipboard) {
      try {
        await navigator.clipboard.writeText(text);
        setCopied(true);
        // Reset copied status after a delay (e.g., 2 seconds)
        setTimeout(() => {
          setCopied(false);
        }, 2000);
      } catch (error) {
        console.error("Clipboard copy failed:", error);
      }
    } else {
      // Clipboard API is not supported, provide a fallback method
      try {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand("copy");
        document.body.removeChild(textArea);
        setCopied(true);
        // Reset copied status after a delay (e.g., 2 seconds)
        setTimeout(() => {
          setCopied(false);
        }, 2000);
      } catch (error) {
        console.error("Fallback clipboard copy failed:", error);
      }
    }
  };
  const handleMouseLeave = (e) => {
    const relatedTarget = e.relatedTarget;
    const currentTarget = e.currentTarget;
  
    if (relatedTarget instanceof Node && currentTarget instanceof Node) {
      const isMouseOutsidePopover = !relatedTarget || !currentTarget.contains(relatedTarget);
  
      if (isMouseOutsidePopover) {
        setShowPopover(false);
      }
    }else{
      setShowPopover(false);
    }
  };
  const handleMouseEnterPopover = () => {
    setShowPopover(true);
  };
  const handleMouseLeavePopover = () => {
    // Uncomment this line if you want to hide the popover when mouse leaves the popover content
    setShowPopover(false);
  };

  useEffect(() => {
    if (expanded) {
      anime({
        targets: listRef.current,
        translateY: [`${-20}px`, `${0}px`],
        opacity: [0, 1],
        duration: 500,
        easing: "easeInQuad",
      });
    }
  }, [expanded]);
  const renderPopover = (content) => (
    <Popover>
      <Popover.Body>{`${content}`}</Popover.Body>
    </Popover>
  );
  const popover = (
    <Popover
      id="popover"
      onClick={handlePopoverClick}
      onDoubleClick={handlePopoverDoubleClick}
      onMouseEnter={handleMouseEnterPopover}
      onMouseLeave={handleMouseLeavePopover}
    >
      <div id="treeview_pop">
        <div className="card">
          <div className="card-img-top">
            <div className="card-img-top_img">
              <img
                src={
                  hoveredItemId?.tooltipData?.profilePic
                    ? hoveredItemId?.tooltipData?.profilePic
                    : NoProfile
                }
                alt="profile_photo"
              />
            </div>
            <h5 className="card-title">
              {hoveredItemId?.tooltipData?.username}
              <OverlayTrigger
                trigger={["hover", "focus"]}
                placement="top"
                overlay={renderPopover(copied ? "Copied" : t("copyUsername"))}
              >
                <span
                  onClick={() =>
                    copyToClipboard(hoveredItemId?.tooltipData?.username)
                  }
                >
                  <svg
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    height="1em"
                    width="1em"
                  >
                    <path d="M19 21H8V7h11m0-2H8a2 2 0 00-2 2v14a2 2 0 002 2h11a2 2 0 002-2V7a2 2 0 00-2-2m-3-4H4a2 2 0 00-2 2v14h2V3h12V1z" />
                  </svg>
                </span>
              </OverlayTrigger>
            </h5>
            <p className="card-text">
              {hoveredItemId?.tooltipData?.fullName
                ? hoveredItemId?.tooltipData?.fullName
                : "username"}
            </p>
          </div>
          <div className="card-body">
            <div className="user_detail_tabl">
              <table>
                <tbody>
                  {hoveredItemId?.tooltipData?.tableData &&
                    Object.entries(hoveredItemId.tooltipData.tableData).map(
                      ([key, value]) => (
                        <tr key={key}>
                          <td>{key}</td>
                          <td>
                            {key === "joinDate" ? formatDate(value) : value}
                          </td>
                        </tr>
                      )
                    )}
                </tbody>
              </table>
            </div>
            {hoveredItemId?.tooltipData?.rankDetails &&
              hoveredItemId?.tooltipData?.rankDetails?.name && (
                <a
                  className="btn btn-rank"
                  style={{
                    backgroundColor:
                      hoveredItemId?.tooltipData?.rankDetails?.color,
                  }}
                >
                  {hoveredItemId?.tooltipData?.rankDetails?.name}
                </a>
              )}
          </div>
        </div>
      </div>
    </Popover>
  );

  return (
    <li>
      {props.data?.isPlaceholder ? (
        <>
          <a>
            <div className="member-view-box">
              <div className="member-image">
                {props?.data?.isMore && (
                  <>
                    {showLoader ? (
                      <>
                        <Loader
                          type="spinner-default"
                          bgColor={"#954cea"}
                          color={"#954cea"}
                          size={25}
                        />
                      </>
                    ) : (
                      <>
                        <div
                          className="right_more_user_expand_btn"
                          onClick={() => handleIsMore(props?.data?.attributes)}
                        >
                          <i className="fas fa-angle-double-right"></i>
                        </div>
                        <div className="member-details-dwnl-bx">
                          {props?.data?.attributes?.moreChildren} {t("more")}
                        </div>
                      </>
                    )}
                  </>
                )}
              </div>
            </div>
          </a>
        </>
      ) : (
        <>
          {props?.data?.children?.length === 0 &&
          props?.data?.attributes?.id === props?.selectedUserId ? (
            <div className="member-view-box">
              <div className="member-image">
                <Loader
                  type="spinner-default"
                  bgColor={"#954cea"}
                  color={"#954cea"}
                  size={25}
                />
              </div>
            </div>
          ) : (
            <a
              onClick={() => HandleExpand(props.data)}
              onMouseEnter={() => handleItemHover(props.data)}
              onMouseLeave={() => handleItemHover([])}
              onDoubleClick={() => handleDoubleClick(props.data)}
            >
              <div className="member-view-box">
                <OverlayTrigger
                  trigger="manual"
                  placement="bottom"
                  overlay={popover}
                  show={showPopover}
                  onMouseEnter={handleMouseEnterPopover}
                  onMouseLeave={handleMouseLeave}
                >
                  <div className="member-image">
                    <img
                      src={
                        props.data?.attributes?.treeIcon === null ||
                        props.data?.attributes?.treeIcon === ""
                          ? NoProfile
                          : props.data?.attributes?.treeIcon
                      }
                      alt="Member"
                      onMouseEnter={() => setShowPopover(true)}
                      onMouseLeave={handleMouseLeave}
                    />
                    <div className="member-details">
                      <h3>{props.data?.username}</h3>
                      <div className="member-details-downline-dtl">
                        <div className="member-details-dwnl-bx">
                          {t("count")}:{" "}
                          <strong>
                            {props.data?.attributes?.childrenCount}
                          </strong>
                        </div>
                      </div>
                    </div>
                  </div>
                </OverlayTrigger>
              </div>
            </a>
          )}
          {props.data?.children?.length > 0 && expanded && (
            <ul ref={listRef}>
              {props.data?.children?.map((child, index) => {
                return (
                  <SponserTreeNode
                    key={index}
                    data={child}
                    plan={props.plan}
                    selectedUserId={props.selectedUserId}
                    setSelectedUserId={props.setSelectedUserId}
                    doubleClickedUser={props.doubleClickedUser}
                    setDoubleClickedUser={props.setDoubleClickedUser}
                  />
                );
              })}
            </ul>
          )}
        </>
      )}
    </li>
  );
};
const SponserTreeComponent = (props) => {
  const mlmPlan = useSelector(
    (state) => state.user?.loginResponse?.moduleStatus?.mlm_plan
  );
  const data = useSelector((state) => state.tree?.sponserTreeList);
  return (
    <SponserTreeNode
      data={data}
      plan={mlmPlan}
      selectedUserId={props.selectedUserId}
      setSelectedUserId={props.setSelectedUserId}
      doubleClickedUser={props.doubleClickedUser}
      setDoubleClickedUser={props.setDoubleClickedUser}
    />
  );
};
export default SponserTreeComponent;
