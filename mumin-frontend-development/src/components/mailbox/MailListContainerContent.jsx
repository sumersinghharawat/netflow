import React, { useEffect, useState, useRef } from "react";
import { useTranslation } from "react-i18next";
import moment from "moment";
import DOMPurify from "dompurify";
import { useLocation } from "react-router";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";

const MailListContainerContent = ({
  mailContent,
  mailData,
  setSingleMailId,
  setMailCheck,
  activeTab,
  setSelectedContacts,
  setShowMailPlaceholder,
  page,
  setPage,
  selectedContacts,
  setApiTab,
  selectedCheckboxes,
  setSelectedCheckboxes,
  setSelectAllChecked,
  selectAllChecked,
  setReplyBtn
}) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const [messageArray, setMessageArray] = useState([]);
  const [isFetching, setIsFetching] = useState(false);
  const [dropdownOpen, setDropdownOpen] = useState(false);
  const [isDeletable, setIsDeletable] = useState(false);
  const dropdownUserRef = useRef(null);
  const location = useLocation();

  const deleteMailMutation = ApiHook.CallDeleteMail();
  const areAllChecked = () => {
    const allChecked = Object.values(selectedCheckboxes).every(
      (value) => value === true
    );
    return (
      allChecked &&
      mailContent.length > 0 &&
      Object.keys(selectedCheckboxes).length === mailContent.length
    );
  };

  const handleSingleMail = (id) => {
    setReplyBtn(false);
    if (activeTab !== "replicaInbox") {
      setSingleMailId(id);
      setMailCheck(true);
    } else {
      setSelectedContacts(id);
      setShowMailPlaceholder(false);
    }
  };

  const toggleDropdown = () => {
    setDropdownOpen(!dropdownOpen);
  };

  const toggleAllCheckboxes = () => {
    const allChecked = areAllChecked();
    let updatedSelectedCheckboxes = {};
    if (!allChecked) {
      mailContent.forEach((item) => {
        updatedSelectedCheckboxes[item.id] = !allChecked;
      });
    } else {
      updatedSelectedCheckboxes = {};
    }
    setSelectedCheckboxes(updatedSelectedCheckboxes);
    setSelectAllChecked(!allChecked);
  };

  const toggleCheckbox = (id) => {
    setSelectedCheckboxes((prevSelectedCheckboxes) => ({
      ...prevSelectedCheckboxes,
      [id]: !prevSelectedCheckboxes[id],
    }));
  };

  const handleScroll = () => {
    const container = document.getElementById("mail-list-container");
    if (
      container.scrollTop + container.clientHeight === container.scrollHeight &&
      !isFetching
    ) {
      setIsFetching(true);
    }
  };
  const deleteMail = () => {
    const selectedIds = Object.keys(selectedCheckboxes);

    const data = {
      mailId: activeTab === "replicaInbox" ? selectedIds : selectedIds,
      type: activeTab === "replicaInbox" ? "contacts" : "inbox",
    };
    if (isDeletable) {
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
    } else {
      toast.error(t("noMailSelected"));
    }
  };
  useEffect(() => {
    const isSelectedMail = Object.values(selectedCheckboxes).some(
      (value) => value === true
    );
    if (isSelectedMail) {
      setIsDeletable(true);
    }
    setSelectAllChecked(areAllChecked());
  }, [selectedContacts, selectedCheckboxes]);
  useEffect(() => {
    if (location.pathname) {
      setDropdownOpen(false);
    }

    const handleOutsideClick = (event) => {
      const isClickInsideDropdown =
        dropdownUserRef.current &&
        dropdownUserRef.current.contains(event.target);

      if (!isClickInsideDropdown) {
        setDropdownOpen(false);
      }
    };

    document.addEventListener("click", handleOutsideClick);

    return () => {
      document.removeEventListener("click", handleOutsideClick);
    };
  }, [location.pathname]);

  useEffect(() => {
    window.addEventListener("scroll", handleScroll);
    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, [isFetching]);

  useEffect(() => {
    if (isFetching) {
      setTimeout(() => {
        // Update the state with the new data
        setIsFetching(false);

        // Implement logic to determine the next page
        // For example, you can calculate totalPages based on totalAmount and perPage
        const totalPages = Math.ceil(mailData?.totalCount / 10);

        if (page < totalPages) {
          setPage((prevPage) => prevPage + 1);
        }
      }, 500); // Simulating a delay for API call, adjust as needed
    }
  }, [isFetching, mailData]);

  useEffect(() => {
    const messages = mailContent;
    const parsedMessages = messages?.map((message) => {
      const parser = new DOMParser();
      const doc = parser.parseFromString(message.message, "text/html");

      // Use innerText directly if no <p> tag is found
      const trimmedContent = doc.body.innerText.trim();

      return {
        id: message.id,
        username: message?.fromUsername || message?.toUsername || "",
        subject: message.subject,
        createdAt: message.createdAt,
        message: trimmedContent,
        name: message?.toUser || message?.name || message?.fromUser || "",
      };
    });

    setMessageArray(parsedMessages);
  }, [mailContent]);
  return (
    <>
      <div className="border-bottom pb-4 mb-3 px-3 selectall_checkbox">
        <label
          htmlFor="mailcheckbox"
          className="form-check-label"
          style={{ marginRight: "12px", transform: "scale(1.5)" }}
        >
          <input
            id="mailcheckbox"
            type="checkbox"
            className="form-check-input"
            checked={selectAllChecked}
            onChange={toggleAllCheckboxes}
            style={{display:activeTab === "adminInbox" ? "none" : ""}}
          />
          <i className="input-helper"></i>
        </label>
        {t(activeTab)}

        <div
          className={`right_notiifcation_mail_ico user_avatar ${
            dropdownOpen ? "show" : ""
          }`}
          ref={dropdownUserRef}
          style={{ display: activeTab === "adminInbox" ? "none" : "" }}
        >
          <a
            href="#"
            className=""
            data-bs-toggle="dropdown"
            aria-expanded={dropdownOpen}
            onClick={toggleDropdown}
          >
            <svg
              className="more"
              viewBox="0 0 24 24"
              height="24px"
              width="24px"
            >
              <path d="M14 6a2 2 0 11-4 0 2 2 0 014 0zM14 12a2 2 0 11-4 0 2 2 0 014 0zM14 18a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </a>
          <div
            className={`dropdown-menu usr_prfl right-0 animation slideDownIn ${
              dropdownOpen ? "show" : ""
            }`}
          >
            <ul className="">
              <li key="profile" onClick={deleteMail}>
                <a className="dropdown-item">{t("delete")}</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <div className="mail-list-container-scrl">
        <>
          {mailContent?.length === 0 ? (
            <div className="no-data-mail-cnt">
              <img src="/images/no-mail-image1.jpg" alt="" />
            </div>
          ) : (
            <>
              {messageArray?.map((item, index) => (
                <div className="mail-list" key={index}>
                  <div className="form-check">
                    <label
                      htmlFor={`checkbox-${index}`}
                      className="form-check-label"
                    >
                      <input
                        id={`checkbox-${index}`}
                        type="checkbox"
                        className="form-check-input"
                        checked={selectedCheckboxes[item.id] || false}
                        onChange={() => toggleCheckbox(item.id)}
                        style={{display:activeTab === "adminInbox" ? "none" : ""}}
                      />
                      <i className="input-helper"></i>
                    </label>
                  </div>
                  <div
                    className="content"
                    onClick={() => handleSingleMail(item.id)}
                  >
                    <p className="sender-name">
                      {item.name}
                      {item.username !== "" && ` ( ${item.username} )`}
                    </p>
                    <p className="sender-name">
                      {" "}
                      {moment(item.createdAt).format(
                        "ddd, MMM D, YYYY [at] h:mm A"
                      )}
                    </p>
                    <p
                      className="message_text"
                      dangerouslySetInnerHTML={{
                        __html: DOMPurify.sanitize(item.subject),
                      }}
                    />
                  </div>
                </div>
              ))}
            </>
          )}
        </>
      </div>
    </>
  );
};

export default MailListContainerContent;
