import React, { useEffect, useRef, useState } from "react";
import { OverlayTrigger, Popover } from "react-bootstrap";
import { formatDate } from "../../../utils/formateDate";
import anime from "animejs/lib/anime.es.js";
import Loader from "react-js-loader";

const TreeViewNode = ({
  data,
  setSelectedUserId,
  selectedUserId,
  isLoading,
  key,
}) => {
  const NoProfile = "/images/user-profile.png";
  const [expanded, setExpanded] = useState(false);
  const [hoveredItemId, setHoveredItemId] = useState([]);
  const treeviewContentRef = useRef(null);

  const handleItemHover = (itemId) => {
    setHoveredItemId(itemId);
  };

  const handleClick = (data) => {
    if (data?.hasChildren) {
      setSelectedUserId(data?.id);
      setExpanded(!expanded);
    }
  };

  useEffect(() => {
    if (expanded) {
      anime({
        targets: treeviewContentRef.current,
        translateY: [`${-20}px`, `${0}px`],
        opacity: [0, 1],
        duration: 500,
        easing: "easeInQuad",
      });
    }
  }, [expanded]);

  const popover = (
    <Popover id="popover">
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
                alt="profile"
              />
            </div>
            <h5 className="card-title">
              {hoveredItemId?.tooltipData?.username}
            </h5>
            <p className="card-text">{hoveredItemId?.tooltipData?.fullName}</p>
          </div>
          <div className="card-body">
            <div className="user_detail_tabl">
              <table>
                <tbody>
                  {hoveredItemId?.tooltipData?.tableData &&
                    Object?.entries(hoveredItemId?.tooltipData?.tableData)?.map(
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
    <>
      <li>
        {data?.children && (
          <span onClick={() => handleClick(data)}>
            {data?.hasChildren ? (
              expanded ? (
                <i className="fa fa-minus-square"></i>
              ) : (
                <i className="fa fa-plus-square"></i>
              )
            ) : null}
            {isLoading && data?.id === selectedUserId ? (
              <>
                <Loader
                  type="bubble-scale"
                  bgColor={"#954cea"}
                  color={"#954cea"}
                  size={25}
                />
              </>
            ) : (
              <>
                <div
                  className="treeview_content_box"
                  onMouseEnter={() => handleItemHover(data)}
                  onMouseLeave={() => handleItemHover([])}
                >
                  <div className="treeview_content_box_img">
                    <img
                      src={data?.profilePic ? data?.profilePic : NoProfile}
                      alt=""
                    />
                  </div>
                  <div className="treeview_content_box_usr_dtl">
                    <strong>{data?.title}</strong>
                    <div>{data?.fullName}</div>
                  </div>
                  <div className="treeview_content_box_left_level">
                    <strong>{data?.level}</strong>Level
                  </div>
                  <OverlayTrigger
                    trigger={["hover", "focus"]}
                    placement="right"
                    overlay={popover}
                  >
                    <div className="treeview_content_box_left_info">
                      <i className="fa fa-info"></i>
                    </div>
                  </OverlayTrigger>
                </div>
              </>
            )}
          </span>
        )}
        {!data.children && (
          <div className="treeview_content_box">
            <div className="treeview_content_box_img">
              <img src={data.image} alt="" />
            </div>
            <div className="treeview_content_box_usr_dtl">
              <strong>{data.id}</strong>
              <div>{data.name}</div>
            </div>
            <div className="treeview_content_box_left_level">
              <strong>{data.level}</strong>Level
            </div>
            <div className="treeview_content_box_left_info">
              <i className="fa fa-info"></i>
            </div>
          </div>
        )}
        {expanded && data.children && (
          <ul
            ref={treeviewContentRef}
            style={{ transform: "translateY(-20px)", opacity: 0 }}
          >
            {data?.children?.map((child) => (
              <TreeViewNode
                key={child.id}
                data={child}
                setSelectedUserId={setSelectedUserId}
                selectedUserId={selectedUserId}
                isLoading={isLoading}
              />
            ))}
          </ul>
        )}
      </li>
    </>
  );
};

export default TreeViewNode;
