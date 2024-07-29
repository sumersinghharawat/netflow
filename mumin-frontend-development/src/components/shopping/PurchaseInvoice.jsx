import React, { useRef } from "react";
import { useTranslation } from "react-i18next";
import { useNavigate, useParams } from "react-router";
import { useReactToPrint } from "react-to-print";
import { ApiHook } from "../../hooks/apiHook";
import { useSelector } from "react-redux";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { formatDate } from "../../utils/formateDate";

const PurchaseInvoice = () => {
  const { t } = useTranslation();
  const navigate = useNavigate()
  const params = useParams();
  const tableRef = useRef();
  const orderId = params.id;

  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const userSelectedCurrency = useSelector(
    (state) => state?.user?.selectedCurrency
  );

  const handlePrint = useReactToPrint({
    content: () => tableRef.current,
  });

  const handleBack = () => {
    navigate('/repurchase-report')
  }

  //---------------------------------- API ------------------------------------
  const invoice = ApiHook.CallRepurchaseInvoice(orderId);

  return (
    <>
      <div className="page_head_top">{t("purchase_invoice")}</div>
      <div className="row">
        <div className="col-md-8 m-auto">
          <div className="purchase_invoice" ref={tableRef}>
              <button className="print-button" style={{backgroundColor:'white'}} onClick={handleBack}>
                <i className="fa fa-angle-left" style={{fontSize: "1.3em"}}></i>
              </button>
            <div className="row p-2">
              <div className="col-md-6 d-flex align-items-center">
                <img src="/images/logo_user.png" alt="" />
              </div>
              <div className="col-md-6 text-end">
                <p className="font-weight-bold mb-1">
                  {`${t("invoice")}: #${invoice.data?.invoiceNo}`}
                </p>
                <p className="text-muted">
                  Date: {formatDate(invoice.data?.date)}
                </p>
                <button
                  type="button"
                  className="btn btn-labeled btn-primary print-button"
                  onClick={handlePrint}
                >
                  <span className="btn-label">
                    <i className="fa fa-print"></i>
                  </span>{" "}
                  {t("print")}
                </button>
              </div>
            </div>

            <hr className="my-1" />

            <div className="row pb-1 p-1">
              <div className="col-md-6">
                <p className="font-weight-bold mb-4">
                  {t("client_information")}
                </p>
                <p className="mb-1">{invoice.data?.clientInfo?.name}</p>
                <p className="mb-1">{invoice.data?.clientInfo?.address}</p>
                <p className="mb-1">{invoice.data?.clientInfo?.city}</p>
                <p className="mb-1">{invoice.data?.clientInfo?.zip}</p>
              </div>

              <div className="col-md-6 text-end">
                <p className="font-weight-bold mb-4">{t("payment_details")}</p>
                <p className="mb-1">
                  <span className="text-muted">{t("payment_method")}: </span>
                  {invoice.data?.paymentDetails?.paymentMethod}
                </p>
              </div>
            </div>

            <div className="row p-1">
              <div className="col-md-12">
                <table className="table">
                  <thead>
                    <tr>
                      <th className="text-uppercase small font-weight-bold">
                        #
                      </th>
                      <th className="text-uppercase small font-weight-bold">
                        {t("package")}
                      </th>
                      <th className="text-uppercase small font-weight-bold">
                        {t("quantity")}
                      </th>
                      <th className="text-uppercase small font-weight-bold text-end">
                        {t("total")}
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {invoice.data?.items?.map((item, index) => (
                      <tr key={index}>
                        <td>{index + 1}</td>
                        <td>{item.package}</td>
                        <td>{item.quantity}</td>
                        <td className="text-end">
                          {`${
                            userSelectedCurrency.symbolLeft
                          } ${CurrencyConverter(
                            item.amount,
                            conversionFactor
                          )}`}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>

            <div className="d-flex flex-row-reverse text-white invoice-total p-4">
              <div className="py-1 px-1 text-end">
                <div className="mb-2">{t("grand_total")}</div>
                <div className="h2 font-weight-light">
                  {`${userSelectedCurrency.symbolLeft} ${CurrencyConverter(
                    invoice.data?.grandTotal,
                    conversionFactor
                  )}`}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default PurchaseInvoice;
