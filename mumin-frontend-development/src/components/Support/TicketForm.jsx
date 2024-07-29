import React, { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";
import Select from "react-select";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";

const TicketForm = ({ partials }) => {
  const { t } = useTranslation();
  const {
    register,
    setValue,
    handleSubmit,
    watch,
    reset,
    trigger,
    formState: { errors },
  } = useForm({});

  const formValue = watch();
  const [category, setCategory] = useState("");
  const queryClient = useQueryClient();
  //--------------------------------------------- API ----------------------------------------------

  const trackId = ApiHook.CallTrackId();
  const ticketMutation = ApiHook.CallCreateTicket();

  useEffect(() => {
    setValue("ticketId", trackId.data);
  }, [trackId.data]);

  const handleCategory = (data) => {
    setValue("category", data.value);
    setCategory(data);
  };

  const onSubmit = (data) => {
    ticketMutation.mutate(data, {
      onSuccess: (res) => {
        if (res.status) {
          toast.success(t(res.data));
          queryClient.invalidateQueries({ queryKey: ["get-trackId"] });
          setCategory("");
          reset();
        } else {
          if (res?.data?.code) {
            toast.error(res.data?.description);
          }
        }
      },
    });
  };

  return (
    <>
      <div className="page_head_top">{t("create_ticket")}</div>
      <div className="support_cnt_box">
        <div className="row">
          <div className="col-md-6">
            <form onSubmit={handleSubmit(onSubmit)}>
              <div className="create_ticket_row">
                <label htmlFor="ticketId">
                  {t("ticket_id")} <span className="text-danger">*</span>
                </label>
                <input
                  id="ticketId"
                  name="ticketId"
                  type="text"
                  className="form-control"
                  defaultValue={trackId.data}
                  {...register("ticketId")}
                  disabled
                />
              </div>
              <div className="create_ticket_row">
                <label htmlFor="category">
                  {t("category")} <span className="text-danger">*</span>
                </label>
                <Select
                  id="category"
                  name="category"
                  value={category}
                  {...register("category", {
                    required: t("this_field_is_required"),
                  })}
                  onBlur={async () => await trigger("category")}
                  onChange={handleCategory}
                  options={partials?.categories}
                />
                {errors.category && (
                  <span role="alert" className="error-message-validator">
                    {errors.category.message}
                  </span>
                )}
              </div>
              <div className="create_ticket_row">
                <label htmlFor="subject">
                  {t("subject")} <span className="text-danger">*</span>
                </label>
                <input
                  type="text"
                  className="form-control"
                  id="subject"
                  {...register("subject", {
                    required: t("this_field_is_required"),
                  })}
                />
                {errors.subject && (
                  <span role="alert" className="error-message-validator">
                    {errors.subject.message}
                  </span>
                )}
              </div>
              <div className="create_ticket_row">
                <label htmlFor="message">
                  {t("message_to_admin")} <span className="text-danger">*</span>
                </label>
                <textarea
                  className="form-control"
                  id="message"
                  {...register("message", {
                    required: t("this_field_is_required"),
                  })}
                  cols="30"
                  rows="5"
                ></textarea>
                {errors.message && (
                  <span role="alert" className="error-message-validator">
                    {errors.message.message}
                  </span>
                )}
              </div>
              <div className="create_ticket_row">
                <label htmlFor="attachment">{t("attachment")}</label>
                <input
                  type="file"
                  className="form-control"
                  id="attachment"
                  multiple
                  {...register("attachment")}
                />
              </div>
              <div className="col-md-12">
                <button type="submit" className="btn btn-primary float-end">
                  {t("submit")}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </>
  );
};

export default TicketForm;
