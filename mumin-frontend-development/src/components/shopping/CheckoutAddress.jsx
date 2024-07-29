import React, { useEffect } from "react";
import { useTranslation } from "react-i18next";
import { useSelector } from "react-redux";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import AddressModal from "./addressModal";
import { ApiHook } from "../../hooks/apiHook";
import { useQueryClient } from "@tanstack/react-query";
import { toast } from "react-toastify";

const CheckoutAddress = ({
  totalAmount,
  handleNext,
  show,
  setShow,
  address,
  register,
  formValues,
  setValue,
  errors,
}) => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );

  const removeAddressMutation = ApiHook.CallRemoveAddress();
  const defaultAddressMutation = ApiHook.CallDefaultAddressChange();

  useEffect(() => {
    const defaultAddress = address.find((addr) => addr.isDefault === true);
    if (defaultAddress) {
      setValue("addressId", defaultAddress.id);
    }
  }, [address, setValue]);

  const handleAddressSelect = (addressId) => {
    setValue("addressId", addressId);
  };

  const handleRemoveAddress = (addressId) => {
    removeAddressMutation.mutate(
      { addressId: addressId },
      {
        onSuccess: (res) => {
          if (res?.status) {
            toast.success(t(res?.data?.data));
            queryClient.invalidateQueries({ queryKey: ["get-address"] });
          } else if (res?.data?.code) {
            toast.error(t(res?.data?.description));
          }
        },
      }
    );
  };

  const handleDefaultAddress = (id) => {
    defaultAddressMutation.mutate(id, {
      onSuccess: (res) => {
        if (res.status) {
          toast.success(t(res?.data?.data));
          queryClient.invalidateQueries({ queryKey: ["get-address"] });
        } else {
          toast.error(t(res?.data?.description));
        }
      },
    });
  };
  return (
    <>
      <div className="checkout_address_secion_view">
        <div className="row">
          {address?.length === 0 && (
            <div className="col-md-4">
              <div
                className={`checkout_address_secion_view_box`}
                onClick={() => setShow(true)}
              >
                {!address.isDefault && (
                  <button
                    className="makeThisPrimaryBtn"
                    onClick={() => setShow(true)} // add new address
                  >
                    {t("add_new_address")}
                  </button>
                )}
                <strong>{t("add_new_address")}</strong>
                <p>{t("no_address_found")}</p>
              </div>
            </div>
          )}
          {address?.map((address, index) => (
            <div className="col-md-4" key={index}>
              <div
                className={`checkout_address_secion_view_box ${
                  formValues.addressId === address.id ||
                  (address.isDefault === true && !formValues.addressId)
                    ? "selected_address defaultSelected"
                    : ""
                }`}
                {...register("addressId", { required: true })}
                onClick={() => handleAddressSelect(address?.id)}
              >
                {address.isDefault && (
                  <div className="defaultAddressCheck">
                    <i className="fa fa-check"></i>
                  </div>
                )}
                {!address.isDefault && (
                  <button
                    className="makeThisPrimaryBtn"
                    onClick={(e) => {
                      e.stopPropagation(); // Prevent the click event from propagating to the parent element
                      handleDefaultAddress(address?.id);
                    }}
                  >
                    {t("make_this_primary")}
                  </button>
                )}
                <div className="address_action_row">
                  <button
                    className="checkout_address_btn"
                    onClick={(e) => {
                      e.stopPropagation(); // Prevent the click event from propagating to the parent element
                      handleRemoveAddress(address?.id);
                    }}
                  >
                    <i className="fa fa-trash"></i>
                  </button>
                </div>
                <strong>{address?.name}</strong>
                <p>
                  {address?.address} {address?.city} {address?.mobile}
                  <br />
                  {address.zip}
                </p>
              </div>
            </div>
          ))}
          {errors.addressId && !formValues.addressId && (
            <span className="error-message-validator">
              {t("this_field_is_required")}
            </span>
          )}
        </div>
      </div>

      <div className="checkout_cnt_ttl_amnt">
        <span>{`${t("totalAmount")}: `}</span>
        <strong>{`${userSelectedCurrency.symbolLeft} ${CurrencyConverter(
          totalAmount,
          conversionFactor
        )}`}</strong>
      </div>

      <div className="checkout_continuew_btn">
        <button
          className="btn btn-primary checkout_cnt_btn"
          onClick={handleNext}
        >
          {t("continue")}
        </button>
      </div>
      <AddressModal show={show} setShow={setShow} />
    </>
  );
};

export default CheckoutAddress;
