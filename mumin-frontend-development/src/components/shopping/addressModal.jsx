import React from "react";
import { Modal, Button, Form } from "react-bootstrap";
import { useTranslation } from "react-i18next";
import { useForm, Controller } from "react-hook-form";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";
import { PhoneInput } from "react-international-phone";

function AddressModal({ show, setShow }) {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const {
    handleSubmit,
    control,
    reset,
    setError,
    formState: { errors },
  } = useForm();

  const addAddressMutation = ApiHook.CallAddAddress();

  const onSubmit = (data) => {

    if (data.phoneNumber.length <= 6) {
      setError("phoneNumber", {
        type: "manual",
        message: t("min_length"),
      });
      return;
    }

    addAddressMutation.mutate(data, {
      onSuccess: (res) => {
        if (res?.status) {
          setShow(false);
          reset();
          queryClient.invalidateQueries({ queryKey: ["get-address"] });
          toast.success(res?.message);
        } else {
          toast.error(res?.data?.description);
        }
      },
    });
  };

  const handleClose = () => {
    setShow(false);
    reset();
  };

  return (
    <>
      <Modal show={show} onHide={() => setShow(false)}>
        <Modal.Header closeButton>
          <Modal.Title>{t("add_address")}</Modal.Title>
        </Modal.Header>
        <Form onSubmit={handleSubmit(onSubmit)}>
          <Modal.Body>
            <Form.Group className="mb-2">
              <Form.Label>{t("name")}:</Form.Label>
              <Controller
                name="name"
                control={control}
                defaultValue=""
                rules={{
                  required: t("this_field_is_required"),
                  pattern: {
                    value: /^[a-zA-Z0-9\s]+$/,
                    message: t("invalid_format_space"),
                  },
                }}
                render={({ field }) => (
                  <>
                    <Form.Control {...field} type="text" />
                    {errors.name && (
                      <span className="text-danger">
                        {errors.name.message}
                      </span>
                    )}
                  </>
                )}
              />
            </Form.Group>

            <Form.Group className="mb-2">
              <Form.Label>{t("address")}:</Form.Label>
              <Controller
                name="address"
                control={control}
                defaultValue=""
                rules={{
                  required: t("this_field_is_required"),
                  pattern: {
                    value: /^[a-zA-Z0-9\s]+$/,
                    message: t("invalid_format_space"),
                  },
                }}
                render={({ field }) => (
                  <>
                    <Form.Control {...field} type="text" />
                    {errors.address && (
                      <span className="text-danger">
                        {errors.address.message}
                      </span>
                    )}
                  </>
                )}
              />
            </Form.Group>

            <Form.Group className="mb-2">
              <Form.Label>{t("zipCode")}</Form.Label>
              <Controller
                name="zipCode"
                control={control}
                defaultValue=""
                rules={{
                  required: t("this_is_required"),
                  pattern: {
                    value: /^[0-9]+$/,
                    message: t("invalid_format_number"),
                  },
                }}
                render={({ field }) => (
                  <>
                    <Form.Control {...field} type="text" />
                    {errors.zipCode && (
                      <span className="text-danger">
                        {errors.zipCode.message}
                      </span>
                    )}
                  </>
                )}
              />
            </Form.Group>

            <Form.Group className="mb-2">
              <Form.Label>{t("city")}</Form.Label>
              <Controller
                name="city"
                control={control}
                defaultValue=""
                rules={{
                  required: t("this_field_is_required"),
                  pattern: {
                    value: /^[a-zA-Z0-9\s]+$/,
                    message: t("invalid_format_space"),
                  },
                }}
                render={({ field }) => (
                  <>
                    <Form.Control {...field} type="text" />
                    {errors.city && (
                      <span className="text-danger">
                        {errors.city.message}
                      </span>
                    )}
                  </>
                )}
              />
            </Form.Group>

            <Form.Group className="mb-2">
              <Form.Label>{t("phone_number")}</Form.Label>
              <Controller
                name="phoneNumber"
                control={control}
                defaultValue=""
                rules={{
                  required: t("this_field_is_required"),
                }}
                render={({ field }) => (
                  <>
                    <PhoneInput
                      defaultCountry="us"
                      value={field.value}
                      onChange={(value) => field.onChange(value)}
                    />
                    {errors.phoneNumber && (
                      <span className="text-danger">
                        {errors.phoneNumber.message}
                      </span>
                    )}
                  </>
                )}
              />
            </Form.Group>
          </Modal.Body>
          <Modal.Footer>
            <Button variant="primary" type="submit">
              {t("save_changes")}
            </Button>
            <Button variant="secondary" onClick={handleClose}>
              {t("close")}
            </Button>
          </Modal.Footer>
        </Form>
      </Modal>
    </>
  );
}

export default AddressModal;