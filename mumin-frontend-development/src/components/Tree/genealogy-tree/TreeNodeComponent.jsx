import React, { useEffect, useRef, useState } from "react";
import { OverlayTrigger, Popover } from "react-bootstrap";
import { useSelector } from "react-redux";
import { formatDate } from "../../../utils/formateDate";
import { ApiHook } from "../../../hooks/apiHook";
import { useNavigate } from "react-router-dom";
import anime from "animejs/lib/anime.es.js";
import Loader from "react-js-loader";
import { useTranslation } from "react-i18next";

const TreeNode = (props) => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const data = props?.data;
  const plan = props?.plan;

  const NoProfile = "/images/user-profile.png";
  const [expanded, setExpanded] = useState(true);
  const [hoveredItemId, setHoveredItemId] = useState([]);
  const [showPopover, setShowPopover] = useState(false);
  const [copied, setCopied] = useState(false);
  const listRef = useRef(null);

  const HandleExpand = (data) => {
    if (data?.attributes?.childrenCount > 0 && data?.children?.length === 0) {
      props.setSelectedUserId(data?.attributes?.id);
    } else {
      setExpanded(!expanded);
    }
  };

  const handleItemHover = (itemId) => {
    setHoveredItemId(itemId);
  };

  const handleDoubleClick = (data) => {
    props.setDoubleUser(data?.attributes?.id);
  };

  const handleIsMore = (data) => {
    let refetch = false;
    if (
      data?.fatherId === props.isMoreId.fatherId &&
      data?.position === props.isMoreId?.position
    ) {
      refetch = true;
    }
    props.setIsMoreId((prev) => ({
      ...prev,
      fatherId: data?.fatherId,
      position: data?.position,
    }));
    if (refetch) {
      props.updatedTree?.refetch();
    }
  };

  const handleRegistration = (data) => {
    props.setParamsList({
      placement: data?.attributes?.parent,
      position: data?.attributes?.position,
      isRegFromTree: 1,
    });

    if (props.ecomStatus) {
      props.setLinkRegisterCheck(true);
    } else {
      navigate("/register", {
        state: {
          parent: data?.attributes?.parent,
          position: data?.attributes?.position,
        },
      });
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
      const isMouseOutsidePopover =
        !relatedTarget || !currentTarget.contains(relatedTarget);

      if (isMouseOutsidePopover) {
        setShowPopover(false);
      }
    } else {
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
                    Object?.entries(hoveredItemId.tooltipData.tableData)?.map(
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
      {data?.isPlaceholder ? (
        <>
          <a>
            <div className="member-view-box">
            <div className="member-image">
                {data?.isMore && plan === "Unilevel" ? (
                  <>
                    {props.unilevelLoading === "fetching" &&
                    props.isMoreId.position !== null &&
                    data?.attributes?.fatherId === props.isMoreId?.fatherId &&
                    data?.attributes?.position === props.isMoreId?.position ? (
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
                          onClick={() => handleIsMore(data?.attributes)}
                        >
                          <i className="fas fa-angle-double-right"></i>
                        </div>
                        <div className="member-details-dwnl-bx">
                          {data?.attributes?.moreChildren} more
                        </div>
                      </>
                    )}
                  </>
                ) : (
                  <span
                    className="pulse-button"
                    onClick={() => handleRegistration(data)}
                  >
                    +
                  </span>
                )}
              </div>
            </div>
          </a>
        </>
      ) : (
        <>
          {data?.attributes?.childrenCount > 0 &&
          data?.children?.length === 0 &&
          data?.attributes?.id === props?.selectedUserId ? (
            <>
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
            </>
          ) : (
            <>
              <a
                onClick={() => HandleExpand(data)}
                onMouseEnter={() => handleItemHover(data)}
                onMouseLeave={() => handleItemHover([])}
                onDoubleClick={() => handleDoubleClick(data)}
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
                          data?.attributes?.treeIcon === null ||
                          data?.attributes?.treeIcon === ""
                            ? NoProfile
                            : data?.attributes?.treeIcon
                        }
                        alt="Member"
                        onMouseEnter={() => setShowPopover(true)}
                        onMouseLeave={handleMouseLeave}
                      />
                      <div className="member-details">
                        <h3>{data?.username}</h3>
                        <div className="member-details-downline-dtl">
                          {plan === "Binary" ? (
                            <>
                              <div className="member-details-dwnl-bx">
                                {t("left")}:{" "}
                                <strong>
                                  {data?.attributes?.leftChildrenCount}
                                </strong>
                              </div>
                              <div className="member-details-dwnl-bx">
                                {t("right")}:{" "}
                                <strong>
                                  {data?.attributes?.rightChildrenCount}
                                </strong>
                              </div>
                            </>
                          ) : (
                            <>
                              <div className="member-details-dwnl-bx">
                                Children:{" "}
                                <strong>
                                  {data?.attributes?.childrenCount}
                                </strong>
                              </div>
                            </>
                          )}
                        </div>
                      </div>
                    </div>
                  </OverlayTrigger>
                  {/* {data?.attributes?.childrenCount > 0 &&
                      data?.children?.length === 0 && (
                        <ul>
                          <li>
                            <div
                              className="right_more_user_expand_btn"
                              onClick={() => HandleExpand(data)}
                            >
                              <i className="fas fa-angle-double-down"></i>
                            </div>
                          </li>
                        </ul>
                      )} */}
                </div>
              </a>
            </>
          )}
          {data?.children?.length > 0 && expanded && (
            <ul ref={listRef}>
              {data?.children?.map((child, index) => {
                return (
                  <TreeNode
                    key={index}
                    data={child}
                    plan={plan}
                    ecomStatus={props.ecomStatus}
                    selectedUserId={props.selectedUserId}
                    setSelectedUserId={props.setSelectedUserId}
                    doubleUser={props.doubleUser}
                    setDoubleUser={props.setDoubleUser}
                    linkRegisterCheck={props.linkRegisterCheck}
                    setLinkRegisterCheck={props.setLinkRegisterCheck}
                    paramsList={props.paramsList}
                    setParamsList={props.setParamsList}
                    unilevelLoading={props.unilevelLoading}
                    setIsMoreId={props.setIsMoreId}
                    isMoreId={props.isMoreId}
                    updatedTree={props.updatedTree}
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

const TreeNodeComponent = (props) => {
  const moduleStatus = useSelector(
    (state) => state.dashboard?.appLayout?.moduleStatus
  );
  const [linkRegisterCheck, setLinkRegisterCheck] = useState(false);
  const [paramsList, setParamsList] = useState({
    placement: "",
    position: "",
    isRegFromTree: "",
  });
  const [isMoreId, setIsMoreId] = useState({
    fatherId: null,
    position: null,
  });
  const genealogyList = useSelector((state) => state.tree?.genealogyList);
  const updatedTree = ApiHook.CallUnilevelMore(isMoreId);
  const RegisterLink = ApiHook.CallRegisterLink(
    linkRegisterCheck,
    setLinkRegisterCheck,
    paramsList.placement,
    paramsList.position,
    paramsList.isRegFromTree
  );
  if (RegisterLink.isFetched) {
    window.location.href = RegisterLink.data?.link;
  }

  return (
    <TreeNode
      data={genealogyList}
      plan={moduleStatus?.mlm_plan}
      ecomStatus={moduleStatus?.ecom_status}
      selectedUserId={props.selectedUserId}
      setSelectedUserId={props.setSelectedUserId}
      doubleUser={props.doubleUser}
      setDoubleUser={props.setDoubleUser}
      linkRegisterCheck={linkRegisterCheck}
      setLinkRegisterCheck={setLinkRegisterCheck}
      paramsList={paramsList}
      setParamsList={setParamsList}
      unilevelLoading={updatedTree.fetchStatus}
      setIsMoreId={setIsMoreId}
      isMoreId={isMoreId}
      updatedTree={updatedTree}
    />
  );
};

export default TreeNodeComponent;
