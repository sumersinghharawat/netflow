import React, { useState } from "react";
import { Button, Form, Offcanvas } from "react-bootstrap";
import ReactQuill from "react-quill";
import "react-quill/dist/quill.snow.css";
import { useQueryClient } from "@tanstack/react-query";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";

const MailCompose = ({ showCompose, setShowCompose, setPage, setApiTab, activeTab }) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const [composeMailPayload, setComposeMailPayload] = useState({
    type: "individual",
    username: "",
    subject: "",
    message: "",
  });

  const modules = {
    toolbar: [
      [{ list: "ordered" }, { list: "bullet" }],
      ["bold", "italic", "underline"],
      [{ align: [] }],
    ],
  };

  const composeInternalMailMutation =
    ApiHook.CallSendInternalMail(composeMailPayload);
  const handleComposeMail = (e) => {
    e.preventDefault();
    if (!composeMailPayload.message || composeMailPayload.message.trim().length === 0) {
      toast.error(t("Mail_Body_Is_Empty"));
      return;
    }
    composeInternalMailMutation.mutateAsync(composeMailPayload, {
      onSuccess: (res) => {
        if (res?.status) {
          toast.success(t(res?.data));
          setShowCompose(false);
          setApiTab(activeTab);
          setPage(1);
          queryClient.invalidateQueries({ queryKey: [activeTab] });
          setComposeMailPayload({
            type: "individual",
            username: "",
            subject: "",
            message: "",
          });
        } else {
          toast.error(t(res?.data?.description));
        }
      },
    });
  };

  const handleChange = (type, value) => {
    setComposeMailPayload((prevData) => ({
      ...prevData,
      [type]: value,
    }));
  };

  const handleMailContentChange = (value) => {
    setComposeMailPayload((prevData) => ({
      ...prevData,
      message: value,
    }));
  };

  return (
    <>
      <Offcanvas
        id="composemail"
        show={showCompose}
        onHide={() => setShowCompose(false)}
        placement="end"
        style={{ backgroundColor: "white" }}
      >
        <Offcanvas.Header closeButton>
          <Offcanvas.Title>{t("new_mail")}</Offcanvas.Title>
        </Offcanvas.Header>
        <Offcanvas.Body>
          <main>
            <Form>
              <Form.Group className="mb-3" controlId="to">
              <Form.Label>To:</Form.Label>

                <Form.Control
                  type="text"
                  placeholder="username"
                  value={composeMailPayload.username}
                  onChange={(e) => handleChange("username", e.target.value)}
                />
                {/* {errors.to && <div className="text-danger">{t(errors.to)}</div>} */}
              </Form.Group>
              <Form.Group className="mb-3" controlId="to">
                <Form.Label>Subject:</Form.Label>
                <Form.Control
                  type="text"
                  placeholder="Subject"
                  value={composeMailPayload.subject}
                  onChange={(e) => handleChange("subject", e.target.value)}
                  maxLength={67} // Set the maximum length to 100 characters
                />
                {/* {errors.to && <div className="text-danger">{t(errors.to)}</div>} */}
              </Form.Group>
              <Form.Group className="mt-4" style={{ height: "230px" }}>
                <ReactQuill
                  value={composeMailPayload.message}
                  onChange={handleMailContentChange}
                  modules={modules}
                  style={{ height: "200px" }}
                  maxLength={3000} // Set the maximum length to 3000 characters
                />
              </Form.Group>
              <Form.Group className="mt-4">
                <Button
                  variant="success"
                  type="submit"
                  onClick={handleComposeMail}
                >
                  {t("send")}
                </Button>
              </Form.Group>
            </Form>
          </main>
        </Offcanvas.Body>
      </Offcanvas>
    </>
  );
};

export default MailCompose;
