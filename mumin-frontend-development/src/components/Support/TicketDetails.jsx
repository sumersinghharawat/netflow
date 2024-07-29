import React, { useEffect, useRef, useState } from "react";
import { useTranslation } from "react-i18next";
import { Link, useParams } from "react-router-dom";
import { useForm } from "react-hook-form";
import { ApiHook } from "../../hooks/apiHook";
import { ticketFormatDate } from "../../utils/formateDate";
import { useSelector } from "react-redux";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";

const TicketDetails = () => {
  const { t } = useTranslation();
  const {
    formState: { errors },
  } = useForm({});
  const [files, setFiles] = useState([]);
  const [msg, setMsg] = useState("");
  const param = useParams();
  const queryClient = useQueryClient();
  const userImage = useSelector(
    (state) => state.dashboard?.appLayout?.user?.image
  );

  //--------------------------------------------- API ---------------------------------------

  const details = ApiHook.CallTicketDetails(param.trackId);
  const replies = ApiHook.CallTicketReplies(param.trackId);
  const ticketReplyMutation = ApiHook.CallTicketReply();
  const messageBodyAreaRef = useRef(null);
  const handleFileUpload = (e) => {
    const file = e.target.files;
    setFiles([...file]);
  };

  const handleMessage = (e) => {
    const message = e.target.value;
    setMsg(message);
  };

  const onSubmit = () => {
    const data = {
      files: files,
      message: msg,
      trackId: param.trackId,
    };
    if (msg.trim() !== "") {
      ticketReplyMutation.mutate(data, {
        onSuccess: (res) => {
          if (res.status) {
            setMsg("");
            document.getElementById("msg").value = "";
            document.getElementById("ticketAttachments").value = "";
            setFiles([]);
            toast.success(t(res.data));
            queryClient.invalidateQueries({ queryKey: ["ticket-replies"] });
          } else {
            if (res?.data?.code) {
              toast.error(res.data?.description);
            }
          }
        },
      });
    } else {
      toast.error("msgShouldNotBeEmpty");
    }
  };
  useEffect(() => {
    // Scroll to the bottom when the replies data changes
    if (messageBodyAreaRef.current) {
      messageBodyAreaRef.current.scrollTop =
        messageBodyAreaRef.current.scrollHeight;
    }
  }, [replies.data]);

  return (
    <div>
      <div className="page_head_top">{t("ticket_details")}</div>
      <div className="support_cnt_box">
        <div className="row">
          <div className="col-md-6">
            <h4>
              <Link to={"/support-center"} className="back_btn">
                <i className="fa-solid fa-arrow-left"></i>
              </Link>
            </h4>
            <div className="support_chat_left_box">
              <div className="bootstrap snippets bootdey">
                <div className="tile tile-alt" id="messages-main">
                  <div className="ms-body">
                    <div className="action-header clearfix">
                      <div className="pull-left avatar_top">
                        <img
                          src={
                            userImage ??
                            "https://bootdey.com/img/Content/avatar/avatar2.png"
                          }
                          alt=""
                          className="img-avatar m-r-10"
                        />
                        <div className="lv-avatar pull-left"></div>
                        <span>{details.data?.fullName}</span>
                      </div>
                    </div>
                    <div className="message_body_area" ref={messageBodyAreaRef}>
                      {replies.data?.map((feed, index) => (
                        <div key={index}>
                          <div
                            key={feed.id}
                            className={`message-feed ${
                              feed.isLeft ? "right" : "media"
                            }`}
                          >
                            {feed.image !== null && (
                              <div className="pull-left">
                                <img
                                  src={feed.image ?? "/images/user-profile.png"}
                                  alt=""
                                  className="img-avatar"
                                />
                              </div>
                            )}
                            <div className="media-body">
                              <div className="mf-content">{feed.message}</div>
                              <small className="mf-date">
                                <i className="fa fa-clock-o"></i>{" "}
                                {ticketFormatDate(feed.createdAt)}
                              </small>
                            </div>
                          </div>
                          {feed.attachments && (
                            <div
                              className={`message-feed msgimageFeed ${
                                feed.isLeft ? "right" : "left"
                              }`}
                            >
                              <div className="media-body">
                                {feed.attachments?.map((attachment, index) => (
                                  <div key={index} className="messageImageBox">
                                    <img
                                      src={attachment}
                                      alt=""
                                      className="img-avatar"
                                    />
                                  </div>
                                ))}
                                <small className="mf-date">
                                  <i className="fa fa-clock-o"></i>
                                  {ticketFormatDate(feed.createdAt)}
                                </small>
                              </div>
                            </div>
                          )}
                        </div>
                      ))}
                    </div>

                    <div className="msb-reply">
                      <textarea
                        id="msg"
                        placeholder="What's on your mind..."
                        defaultChecked={msg}
                        onChange={handleMessage}
                      >
                        {errors.msg && (
                          <span
                            role="alert"
                            className="error-message-validator"
                          >
                            {errors.msg}
                          </span>
                        )}
                      </textarea>
                      <div className="fileAttachmentBox">
                        <input
                          id="ticketAttachments"
                          type="file"
                          className="fileAttachment"
                          accept="image/jpeg, image/png, image/jpg"
                          defaultChecked={files}
                          multiple
                          onChange={handleFileUpload}
                        />
                        {files?.length !== 0 && (
                          <span className="fileAttachCount">
                            {files?.length}
                          </span>
                        )}
                      </div>
                      <p></p>
                      <button
                        onClick={onSubmit}
                        disabled={ticketReplyMutation.status === "loading"}
                      >
                        <i className="fa-regular fa-paper-plane"></i>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div className="col-md-6">
            <table className="table border">
              <tbody>
                {details.data?.trackId && (
                  <tr>
                    <th>{t("ticket_id")}</th>
                    <td>:</td>
                    <td>{details.data?.trackId}</td>
                  </tr>
                )}
                {details.data?.status && (
                  <tr>
                    <th>{t("ticket_status")}</th>
                    <td>:</td>
                    <td>{details.data?.status}</td>
                  </tr>
                )}
                {details.data?.createdAt && (
                  <tr>
                    <th>{t("created_on")}</th>
                    <td>:</td>
                    <td>{details.data?.createdAt}</td>
                  </tr>
                )}
                {details.data?.updatedAt && (
                  <tr>
                    <th>{t("last_updated")}</th>
                    <td>:</td>
                    <td>{details.data?.updatedAt}</td>
                  </tr>
                )}
                {details.data?.category && (
                  <tr>
                    <th>{t("category")}</th>
                    <td>:</td>
                    <td>{details.data?.category}</td>
                  </tr>
                )}
                {details.data?.priority && (
                  <tr>
                    <th>{t("priority")}</th>
                    <td>:</td>
                    <td>{details.data?.priority}</td>
                  </tr>
                )}
                {details.data?.tags?.length != 0 && (
                  <tr>
                    <th>{t("tags")}</th>
                    <td>:</td>
                    <td>
                      {details.data?.tags ? details.data.tags.join(", ") : null}
                    </td>
                  </tr>
                )}
              </tbody>
            </table>
            {details.data?.attachments?.length != null && (
              <>
                <p>
                  {t("ticket_attachments")}
                  <span>:</span>
                </p>
                <div className="ticket-attachment">
                  {details.data?.attachments?.map((item, index) => (
                    <img key={index} src={`${item}`} alt="" />
                  ))}
                </div>
              </>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

export default TicketDetails;
