import { useQueryClient } from "@tanstack/react-query";
import React, { useState } from "react";
import { Offcanvas, Form } from "react-bootstrap";
import { MultiSelect } from "react-multi-select-component";
import { ApiHook } from "../../hooks/apiHook";
import SubmitButton from "../Common/buttons/SubmitButton";
import { useTranslation } from "react-i18next";
import { toast } from "react-toastify";
import Select from "react-select";

const EpinTransfer = ({ show, handleClose }) => {
  const { t } = useTranslation();
  const [formState, setFormState] = useState({
    toUsername: "",
    Epins: [],
  });
  const [errorMessage, setErrorMessage] = useState({
    toUsername: null,
    Epins: null,
  });
  const queryClient = useQueryClient();
  const transferEpinList = ApiHook.CallPurchasedEpinList();
  const transferMutation = ApiHook.CallEpinTransfer();

  const handleChange = (e) => {
    const { id, value } = e.target;
    setFormState((prevData) => ({
      ...prevData,
      [id]: value,
    }));
    setErrorMessage((prevData) => ({
      ...prevData,
      [id]: null,
    }));
    if (value === null || value === "") {
      setErrorMessage((prev) => ({
        ...prev,
        [id]: "*Required",
      }));
    }
  };
  const handleEpinChange = (Epins) => {
    if (Epins?.length === 0) {
      setErrorMessage((prev) => ({
        ...prev,
        Epins: "*Required",
      }));
    } else {
      setErrorMessage((prev) => ({
        ...prev,
        Epins: null,
      }));
    }
    setFormState((prevData) => ({
      ...prevData,
      Epins,
    }));
  };
  const isFormValid = () => {
    return formState?.Epins?.length > 0 && formState?.toUsername.trim() !== "";
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    if (!isFormValid()) {
      return;
    }
    const epinValues = formState.Epins.map((option) => option.value);
    const data = {
      toUsername: formState.toUsername,
      epin: epinValues,
    };
    transferMutation.mutateAsync(data, {
      onSuccess: (res) => {
        if (res.status === 200) {
          handleClose(false);
          setFormState({
            toUsername: "",
            Epins: [],
          })
          queryClient.invalidateQueries({ queryKey: ["epin-tiles"] });
          queryClient.invalidateQueries({ queryKey: ["epin-lists"] });
          queryClient.invalidateQueries({queryKey:['purchased-epin-list']})
        } else {
          if (res?.data?.code === 1011) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              toUsername: t(res?.data?.description),
            }));
          } else if (res?.data?.code === 406) {
            setErrorMessage((prevErrors) => ({
              ...prevErrors,
              toUsername: t(res?.data?.description),
            }));
          } else if (res?.data?.code) {
            toast.error(res?.data?.description);
          } else {
            toast.error(res?.data?.message);
          }
        }
      },
    });
  };

  return (
    <Offcanvas show={show} onHide={handleClose} placement="end">
      <Offcanvas.Header closeButton>
        <Offcanvas.Title>{t("epinTransfer")}</Offcanvas.Title>
      </Offcanvas.Header>
      <Offcanvas.Body>
        <Form>
          <Form.Group className="mb-3">
            <Form.Label>{t("username")}</Form.Label>
            <Form.Control
              id="toUsername"
              type="text"
              value={formState.toUsername}
              placeholder="To Username"
              required
              onChange={(e) => handleChange(e)}
              isInvalid={errorMessage?.toUsername !== null}
            />
            <Form.Control.Feedback type="invalid">
              {errorMessage.toUsername}
            </Form.Control.Feedback>
          </Form.Group>
          <Form.Group className="mb-3">
            <Form.Label>{t("epin")}</Form.Label>
            <Select
              isMulti
              options={transferEpinList?.data?.epinTransferList || []}
              value={formState.Epins}
              onChange={handleEpinChange}
            />
            <div className="required_dropDown">
              {errorMessage?.Epins ? errorMessage?.Epins : ""}
            </div>
          </Form.Group>
          <SubmitButton
            isSubmitting={!isFormValid() || transferMutation.isLoading}
            click={handleSubmit}
            text="epinTransfer"
            isLoading={transferMutation.isLoading}
            className={"btn btn-primary submit_btn"}
          />
        </Form>
      </Offcanvas.Body>
    </Offcanvas>
  );
};

export default EpinTransfer;
