import React, { useEffect, useRef, useState } from "react";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";
import moment from "moment";
import { toast } from "react-toastify";
import ReactQuill from "react-quill";
import "react-quill/dist/quill.snow.css";
import DOMPurify from "dompurify";
import { useQueryClient } from "@tanstack/react-query";
import { useSelector } from "react-redux";

const MailViewContent = ({
  mailContent,
  mailData,
  mailId,
  setSingleMailId,
  mailCheck,
  setMailCheck,
  activeTab,
  inboxes,
  selectedContacts,
  showMailPlaceholder,
  setShowMailPlaceholder,
  setApiTab,
  setPage,
  replyBtn,
  setReplyBtn
}) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const replyBoxRef = useRef(null);
  const [replyMessage, setReplyMessage] = useState("");
  const [expandedItems, setExpandedItems] = useState([]);

  const [replyMail, setReplyMail] = useState({
    parentMailId: mailId,
    subject: "",
    message: "",
  });
  const companyProfile = useSelector(
    (state) => state?.dashboard?.appLayout?.companyProfile
  );
  //----------------------------------------- API -------------------------------------------
  const singleMailDetails = ApiHook.CallSingleMailDetails(
    mailId,
    mailCheck,
    setMailCheck,
    activeTab
  );
  const replyMutation = ApiHook.CallReplyMail(replyMail);
  const deleteMailMutation = ApiHook.CallDeleteMail(mailId);

  const modules = {
    toolbar: [
      [{ list: "ordered" }, { list: "bullet" }],
      ["bold", "italic", "underline"],
      [{ align: [] }],
    ],
  };
  //----------------------------------------- Functions -------------------------------------------
  const toggleAccordionItem = (index) => {
    if (expandedItems.includes(index)) {
      setExpandedItems(expandedItems.filter((item) => item !== index));
    } else {
      setExpandedItems([...expandedItems, index]);
    }
  };

  const sendReply = () => {
    const lastIndex = singleMailDetails?.data?.data?.length - 1;
    const data = {
      parentMailId: singleMailDetails?.data?.data?.[lastIndex].id,
      subject: singleMailDetails?.data?.data?.[lastIndex].subject,
      message: replyMessage,
    };
    replyMutation.mutate(data, {
      onSuccess: (res) => {
        if (res.status) {
          toast.success(res.data);
          setReplyBtn(false);
          setMailCheck(true);
          setReplyMessage("");
          queryClient.invalidateQueries({ queryKey: ["inboxes"] });
        } else {
          toast.error(res.data?.description);
        }
      },
    });
  };

  const deleteMail = (id, field, e) => {
    e.stopPropagation();

    const data = {
      mailId: activeTab === "replicaInbox" ? [selectedContacts] : [id],
      type: activeTab === "replicaInbox" ? "contacts" : "inbox",
    };

    deleteMailMutation.mutateAsync(data, {
      onSuccess: (res) => {
        if (res?.status) {
          toast.success(t(res?.data));
          setSingleMailId("");
          setMailCheck(false);
          setShowMailPlaceholder(true);
          setApiTab(activeTab);
          setPage(1);
          queryClient.invalidateQueries({ queryKey: [activeTab] });
        }
      },
    });
  };

  const isDeletable = () => {
    if (activeTab !== "replicaInbox") {
      return (
        !singleMailDetails?.data?.data ||
        singleMailDetails?.data?.data?.length === 0 ||
        showMailPlaceholder ||
        activeTab === "adminInbox"
      );
    } else {
      return showMailPlaceholder;
    }
  };

  const isReply = () => {
    return (
      !singleMailDetails?.data?.data ||
      singleMailDetails?.data?.data?.length === 0 ||
      showMailPlaceholder ||
      activeTab === "adminInbox"
    );
  };

  useEffect(() => {
    if (singleMailDetails?.data?.data) {
      setShowMailPlaceholder(false);
    }
    const lastIndex = singleMailDetails?.data?.data?.length - 1;
    if (lastIndex >= 0) {
      setExpandedItems([...Array(lastIndex).keys()]); // Collapse all items except the last one
    }
  }, [singleMailDetails?.data?.data]);

  useEffect(() => {
    if (replyBtn && replyBoxRef.current) {
      replyBoxRef.current.scrollIntoView({
        behavior: "smooth",
      });
    }
  }, [replyBtn]);
  
  return (
    <>
      <div className="row">
        <div className="col-md-12 mb-4 mt-4">
          <div className="btn-toolbar">
            <div className="btn-group">
              <button
                type="button"
                className="btn btn-sm btn-outline-secondary"
                onClick={() => {
                  setReplyBtn(true);

                  // Scroll to the reply box
                  if (replyBoxRef.current) {
                    replyBoxRef.current.scrollIntoView({
                      behavior: "auto",
                    });
                  }
                }}
                disabled={isReply()}
                style={{ display: isReply() ? "none" : "block" }}
              >
                <i className="fa-solid fa-reply"></i> {t("reply")}
              </button>
            </div>
            <div className="btn-group">
              <button
                type="button"
                className="btn btn-sm btn-outline-secondary"
                onClick={(e) => deleteMail(mailId, "thread", e)}
                disabled={isDeletable()}
                style={{ display: isDeletable() ? "none" : "block" }}
              >
                <i className="fa-regular fa-trash-can"></i> {t("delete")}
              </button>
            </div>
          </div>
        </div>
      </div>
      {showMailPlaceholder ? (
        <div className="no-mail-content">
          <img src="/images/no-mail.png" alt="" />
        </div>
      ) : (
        <div className="message-body">
          <div className="accordion" id="accordionExample">
            {activeTab !== "replicaInbox" ? (
              singleMailDetails?.data?.data?.map((item, index) => (
                <div className="accordion-item" key={index}>
                  <h2 className="accordion-header" id={`heading${index}`}>
                    <button
                      className="accordion-button"
                      type="button"
                      aria-expanded={expandedItems.includes(index)}
                      onClick={() => toggleAccordionItem(index)}
                    >
                      <div className="sender-details">
                        <img
                          className="img-sm rounded-circle mr-3"
                          src={
                            item?.fromUserImage === null ||
                            item?.fromUserImage === undefined
                              ? "/images/user-profile.png"
                              : item.fromUserImage
                          }
                          alt=""
                        />
                        <div className="details">
                          <p className="msg-subject">
                            {t("subject")} : {item.subject}
                          </p>
                          <p className="sender-email">
                            {t("from")}:{" "}
                            {item.fromUsername
                              ? item.fromUsername
                              : item?.name ? item?.name : companyProfile?.name}{" "}
                            (
                            <a className="maildtl" href="#">
                              {item.fromUserMail
                                ? item.fromUserMail
                                : companyProfile?.address}
                            </a>
                            ) <br />
                            <span>
                              {t("date")}:{" "}
                              {moment(item.createdAt).format(
                                "ddd, MMM D, YYYY [at] h:mm A"
                              )}
                            </span>
                            <i className="mdi mdi-account-multiple-plus"></i>
                          </p>
                        </div>
                      </div>
                    </button>
                  </h2>
                  <div
                    id={`collapse${index}`}
                    className={`accordion-collapse ${
                      expandedItems.includes(index) ? "collapse" : ""
                    } `}
                    aria-labelledby={`heading${index}`}
                    data-bs-parent="#accordionExample"
                  >
                    <div className="accordion-body">
                      <div className="message-content">
                        <p
                          dangerouslySetInnerHTML={{
                            __html: DOMPurify.sanitize(item.message),
                          }}
                        />
                      </div>
                    </div>
                  </div>
                </div>
              ))
            ) : (
              <div className="accordion-item">
                {mailContent
                  .filter((inboxItem) => inboxItem.id === selectedContacts)
                  .map((inboxItem, index) => (
                    <div key={index}>
                      <h2 className="accordion-header">
                        <button
                          className="accordion-button"
                          type="button"
                        >
                          <div className="sender-details">
                            <img
                              className="img-sm rounded-circle mr-3"
                              src="/images/user-profile.png"
                              alt=""
                            />
                            <div className="details">
                              <p className="msg-subject">
                                {t("subject")} : {inboxItem.subject}
                              </p>
                              <p className="sender-email">
                                {t("from")}:{" "}
                                {inboxItem.fromUsername
                                  ? inboxItem.fromUsername
                                  : companyProfile?.name}{" "}
                                (
                                <a className="maildtl" href="#">
                                  {inboxItem.fromUserMail
                                    ? inboxItem.fromUserMail
                                    : companyProfile?.address}
                                </a>
                                ) <br />
                                <span>
                                  {t("date")}:{" "}
                                  {moment(inboxItem.createdAt).format(
                                    "ddd, MMM D, YYYY [at] h:mm A"
                                  )}
                                </span>
                                <i className="mdi mdi-account-multiple-plus"></i>
                              </p>
                            </div>
                          </div>
                        </button>
                      </h2>
                      <div
                        data-bs-parent="#accordionExample"
                      >
                        <div className="accordion-body">
                          <div className="message-content">
                            <p
                              dangerouslySetInnerHTML={{
                                __html: DOMPurify.sanitize(inboxItem.message),
                              }}
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  ))}
              </div>
            )}
          </div>

          {replyBtn && (
            <>
              <div className="reply_message mt-4" ref={replyBoxRef}>
                <ReactQuill
                  value={replyMessage}
                  onChange={setReplyMessage}
                  modules={modules}
                  style={{ height: "200px" }}
                />
                <button className="send_btn mt-5" onClick={() => sendReply()}>
                  {t("send")}
                </button>
              </div>
            </>
          )}
        </div>
      )}
    </>
  );
};

export default MailViewContent;
