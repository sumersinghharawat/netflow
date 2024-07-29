import React from "react";
import { useTranslation } from "react-i18next";
import { formatDate } from "../../../utils/formateDate";
import { Link } from "react-router-dom";
import { useSelector } from "react-redux";
import { ApiHook } from "../../../hooks/apiHook";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";
import CurrencyConverter from "../../../Currency/CurrencyConverter";
import { TableSkeleton } from "./TableSkeleton";

const TableContent = ({ headers, data, type, handleEditClick, tableRef }) => {
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const conversionFactor = useSelector(
    (state) => state?.user?.conversionFactor
  );
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const epinRefundMutation = ApiHook.CallEpinRefund();
  const handleRefund = async (row) => {
    const epinRefundPayload = {
      epin: row?.numbers,
    };
    epinRefundMutation.mutateAsync(epinRefundPayload, {
      onSuccess: (res) => {
        if (res?.status === 200) {
          toast.success(res?.data?.data);
          queryClient.invalidateQueries({ queryKey: ["epin-tiles"] });
        } else {
          toast.error(res?.data?.data?.description);
        }
      },
    });
  };

  return (
    <div className="table-container" ref={tableRef}>
      <table className="striped" style={{ width: "100%" }}>
        <thead>
          <tr>
            {headers?.map((header, index) => (
              <th key={index}>{header}</th>
            ))}
          </tr>
        </thead>
        {type === "ewallet" && !data ? (
          <tbody>
            <TableSkeleton rowCount={6} cellCount={6} />
          </tbody>
        ) : type === "ewallet" && data?.length === 0 ? (
          <tbody>
            <tr>
              <td colSpan="6">
                <div className="nodata-table-view">
                  <div className="nodata-table-view-box">
                    <div className="nodata-table-view-box-img">
                      <img src="/images/no-data-image1.jpg" alt="" />
                    </div>
                    <div className="nodata-table-view-box-txt">
                      {t("sorry_no_data_found")}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        ) : (
          type === "ewallet" && (
            <tbody>
              {data?.map((row, rowIndex) => (
                <tr key={rowIndex}>
                  {row.ewalletType === "commission" && (
                    <td>
                      <div className="profile_table">
                        <img src="/images/icons-money-bag.png" alt="" />
                      </div>
                      {`${t(row.amountType)} ${row.fromUser ? t("from") : ""} ${
                        row.fromUser ? row.fromUser.toUpperCase() :  ""
                      }`}
                    </td>
                  )}
                  {row.ewalletType === "fund_transfer" && (
                    <td>
                      <div className="profile_table">
                        <img src="/images/icons-transfer.png" alt="" />
                      </div>
                      {`${t(row.amountType)} ${
                        row.type === "credit"
                          ? row.fromUser ?? ""
                          : row.toUser ?? ""
                      } ${
                        row.transactionFee !== 0
                          ? ` ( ${t("transactionFee")} : ${
                              userSelectedCurrency?.symbolLeft
                            } ${row.transactionFee} )`
                          : ""
                      }`}
                    </td>
                  )}
                  {row.ewalletType === "pin_purchase" && (
                    <td>
                      <div className="profile_table">
                        <img src="/images/icons-request-money.png" alt="" />
                      </div>
                      {`${t(row.amountType)}`}
                    </td>
                  )}
                  {row.ewalletType === "ewallet_payment" && (
                    <td>
                      <div className="profile_table">
                        <img src="/images/icons-credit-card.png" alt="" />
                      </div>
                      {`${t(row.amountType)} ${t("using_ewallet")}`}
                    </td>
                  )}
                  {row.ewalletType === "payout" && (
                    <td>
                      <div className="profile_table">
                        <img src="/images/icons-withdrawal.png" alt="" />
                      </div>
                      {`${t(row.amountType)}`}
                    </td>
                  )}
                  {row.totalAmount && (
                    <td>
                      <span className="up_ewallet">
                        {`${
                          userSelectedCurrency?.symbolLeft
                        } ${CurrencyConverter(
                          row.totalAmount,
                          conversionFactor
                        )} `}
                        <i className="fa fa-arrow-up"></i>
                      </span>
                    </td>
                  )}
                  {row.tds && (
                    <td>{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(row.tds, conversionFactor)}`}</td>
                  )}
                  {row.serviceCharge && (
                    <td>{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      row.serviceCharge,
                      conversionFactor
                    )}`}</td>
                  )}
                  {row.amountPayable && (
                    <td>
                      <span className="up_ewallet">
                        {`${
                          userSelectedCurrency?.symbolLeft
                        } ${CurrencyConverter(
                          row.amountPayable,
                          conversionFactor
                        )} `}
                        <i className="fa fa-arrow-up"></i>
                      </span>
                    </td>
                  )}
                  {row.amount && (
                    <td>
                      {row.type === "credit" ? (
                        <span className="up_ewallet">
                          {`${
                            userSelectedCurrency?.symbolLeft
                          } ${CurrencyConverter(
                            row.amount,
                            conversionFactor
                          )} `}
                          <i className="fa fa-arrow-up"></i>
                        </span>
                      ) : (
                        <span className="down_ewallet">
                          {`${
                            userSelectedCurrency?.symbolLeft
                          } ${CurrencyConverter(
                            row.amount,
                            conversionFactor
                          )} `}
                          <i className="fa fa-arrow-down"></i>
                        </span>
                      )}
                    </td>
                  )}
                  {row.dateAdded && (
                    <td>{formatDate(row.dateAdded)}</td>
                  )}
                  {row.balance && (
                    <td>
                      <span className="balance_bx">{`${
                        userSelectedCurrency?.symbolLeft
                      } ${CurrencyConverter(
                        row.balance,
                        conversionFactor
                      )}`}</span>
                    </td>
                  )}
                </tr>
              ))}
            </tbody>
          )
        )}
        {type === "payout" && !data ? (
          <tbody>
            <TableSkeleton rowCount={6} cellCount={6} />
          </tbody>
        ) : type === "payout" && data?.length === 0 ? (
          <tbody>
            <tr>
              <td colSpan="6">
                <div className="nodata-table-view">
                  <div className="nodata-table-view-box">
                    <div className="nodata-table-view-box-img">
                      <img src="/images/no-data-image1.jpg" alt="" />
                    </div>
                    <div className="nodata-table-view-box-txt">
                      {t("sorry_no_data_found")}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        ) : (
          type === "payout" && (
            <tbody>
              {data?.map((row, rowIndex) => (
                <tr key={rowIndex}>
                  {row.updatedAt && <td>{formatDate(row.updatedAt)}</td>}
                  {row.amount && (
                    <td>
                      <span className="up_ewallet">
                        {`${
                          userSelectedCurrency?.symbolLeft
                        } ${CurrencyConverter(row.amount, conversionFactor)} `}
                        <i className="fa fa-arrow-up"></i>
                      </span>
                    </td>
                  )}
                  {row.paymentMethod && (
                    <td>
                      <span className="balance_bx">
                        {t(`${row.paymentMethod}`)}
                      </span>
                    </td>
                  )}
                  {row.status && (
                    <td>
                      <span className="balance_bx">{t(`${row.status}`)}</span>
                    </td>
                  )}
                </tr>
              ))}
            </tbody>
          )
        )}
        {type === "epin" && !data ? (
          <tbody>
            <TableSkeleton rowCount={6} cellCount={6} />
          </tbody>
        ) : type === "epin" && data?.length === 0 ? (
          <tbody>
            <tr>
              <td colSpan="6">
                <div className="nodata-table-view">
                  <div className="nodata-table-view-box">
                    <div className="nodata-table-view-box-img">
                      <img src="/images/no-data-image1.jpg" alt="" />
                    </div>
                    <div className="nodata-table-view-box-txt">
                      {t("sorry_no_data_found")}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        ) : (
          type === "epin" && (
            <tbody>
              {data?.map((row, rowIndex) => (
                <tr key={rowIndex}>
                  {row.name && <td>{row.name}</td>}
                  {row.epin && <td>{row.epin}</td>}
                  {row.numbers && <td>{row.numbers}</td>}
                  {row.amount && (
                    <td>
                      <span className="balance_bx">{`${
                        userSelectedCurrency?.symbolLeft
                      } ${CurrencyConverter(
                        row.amount,
                        conversionFactor
                      )} `}</span>
                    </td>
                  )}
                  {(row.balanceAmount || row.balanceAmount === 0) && (
                    <td>
                      <span className="up_ewallet">
                        {`${
                          userSelectedCurrency?.symbolLeft
                        } ${CurrencyConverter(
                          row.balanceAmount,
                          conversionFactor
                        )} `}
                        <i className="fa fa-arrow-up"></i>
                      </span>
                    </td>
                  )}
                  {row.status && <td>{row.status}</td>}
                  {row.requestedDate && (
                    <td>{formatDate(row.requestedDate)}</td>
                  )}
                  {row.transferredDate && (
                    <td>{formatDate(row.transferredDate)}</td>
                  )}
                  {row.expiryDate && <td>{formatDate(row.expiryDate)}</td>}
                  {row.requestedPinCount && <td>{row.requestedPinCount}</td>}
                  {row.pinAmount && (
                    <td>
                      <span className="balance_bx">{`${userSelectedCurrency?.symbolLeft} ${row.pinAmount} `}</span>
                    </td>
                  )}
                  {row.purchaseStatus === 1 ? (
                    <td>
                      <Link
                        className="btn_tab_2"
                        onClick={() => handleRefund(row)}
                      >
                        {t("refund")}
                      </Link>
                    </td>
                  ): <td></td>}
                  {row.action && <td>{row.action}</td>}
                </tr>
              ))}
            </tbody>
          )
        )}
        {type === "downline" && !data ? (
          <tbody>
            <TableSkeleton rowCount={6} cellCount={6} />
          </tbody>
        ) : type === "downline" && data?.length === 0 ? (
          <tbody>
            <tr>
              <td colSpan="6">
                <div className="nodata-table-view">
                  <div className="nodata-table-view-box">
                    <div className="nodata-table-view-box-img">
                      <img src="/images/no-data-image1.jpg" alt="" />
                    </div>
                    <div className="nodata-table-view-box-txt">
                      {t("sorry_no_data_found")}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        ) : (
          type === "downline" && (
            <tbody>
              {data?.map((row, rowIndex) => (
                <tr key={rowIndex}>
                  {row.fullName && (
                    <td>
                      <div className="profile_table">
                        <img
                          src={row.image ?? "/images/user-profile.png"}
                          alt=""
                        />
                      </div>
                      {row.fullName}
                      <br />
                      {row.username}
                    </td>
                  )}
                  {row.placement && <td>{row.placement}</td>}
                  {row.sponsor && <td>{row.sponsor}</td>}
                  {row.childLevel && <td>{row.childLevel}</td>}
                </tr>
              ))}
            </tbody>
          )
        )}
        {type === "referral" && !data ? (
          <tbody>
            <TableSkeleton rowCount={6} cellCount={6} />
          </tbody>
        ) : type === "referral" && data?.length === 0 ? (
          <tbody>
            <tr>
              <td colSpan="6">
                <div className="nodata-table-view">
                  <div className="nodata-table-view-box">
                    <div className="nodata-table-view-box-img">
                      <img src="/images/no-data-image1.jpg" alt="" />
                    </div>
                    <div className="nodata-table-view-box-txt">
                      {t("sorry_no_data_found")}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        ) : (
          type === "referral" && (
            <tbody>
              {data?.map((row, rowIndex) => (
                <tr key={rowIndex}>
                  {row.fullName && (
                    <td>
                      <div className="profile_table">
                        <img
                          src={row.image ?? "/images/user-profile.png"}
                          alt=""
                        />
                      </div>
                      {row.fullName}
                      <br />
                      {row.username}
                    </td>
                  )}
                  {row.placement && <td>{row.placement}</td>}
                  {row.sponsor && <td>{row.sponsor}</td>}
                  {row.childLevel && <td>{row.childLevel}</td>}
                </tr>
              ))}
            </tbody>
          )
        )}
        {type === "leads" && !data ? (
          <tbody>
            <TableSkeleton rowCount={6} cellCount={6} />
          </tbody>
        ) : type === "leads" && data?.length === 0 ? (
          <tbody>
            <tr>
              <td colSpan="12">
                <div className="nodata-table-view">
                  <div className="nodata-table-view-box">
                    <div className="nodata-table-view-box-img">
                      <img src="/images/no-data-image1.jpg" alt="" />
                    </div>
                    <div className="nodata-table-view-box-txt">
                      {t("sorry_no_data_found")}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        ) : (
          type === "leads" && (
            <tbody>
              {data?.map((row, rowIndex) => (
                <tr key={rowIndex}>
                  {row.id && <td>{rowIndex + 1}</td>}
                  {row.firstName ? <td>{row.firstName}</td> : <td></td>}
                  {row.lastName ? <td>{row.lastName}</td> : <td></td>}
                  {row.leadStatus === 0 && <td>{t("rejected")}</td>}
                  {row.leadStatus === 1 && <td>{t("ongoing")}</td>}
                  {row.leadStatus === 2 && <td>{t("accepted")}</td>}
                  {row.emailId ? <td>{row.emailId}</td> : <td></td>}
                  {row.mobileNo ? <td>{row.mobileNo}</td> : <td></td>}
                  {row.skypeId ? <td>{row.skypeId}</td> : <td></td>}
                  <td>
                    {" "}
                    <button
                      type="button"
                      className="btn btn-labeled btn-primary"
                      onClick={() => handleEditClick(row.id)}
                    >
                      {t("edit")}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          )
        )}
        {type === "repurchase-report" && !data ? (
          <tbody>
            <TableSkeleton rowCount={6} cellCount={6} />
          </tbody>
        ) : type === "repurchase-report" && data?.length === 0 ? (
          <tbody>
            <tr>
              <td colSpan="12">
                <div className="nodata-table-view">
                  <div className="nodata-table-view-box">
                    <div className="nodata-table-view-box-img">
                      <img src="/images/no-data-image1.jpg" alt="" />
                    </div>
                    <div className="nodata-table-view-box-txt">
                      {t("sorry_no_data_found")}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        ) : (
          type === "repurchase-report" && (
            <tbody>
              {data?.map((row, rowIndex) => (
                <tr key={rowIndex}>
                  {row.id && <td>{row.id}</td>}
                  {row.invoiceNo &&
                    (row.status === "1" ? (
                      <td>
                        <Link to={`/repurchase-invoice/${row.id}`}>
                          {row.invoiceNo}
                        </Link>
                      </td>
                    ) : (
                      <td>{row.invoiceNo}</td>
                    ))}
                  {row.totalAmount ? (
                    <td>{`${
                      userSelectedCurrency?.symbolLeft
                    } ${CurrencyConverter(
                      row.totalAmount,
                      conversionFactor
                    )}`}</td>
                  ) : (
                    <td></td>
                  )}
                  {row.paymentMethod ? <td>{row.paymentMethod}</td> : <td></td>}
                  {row.orderDate ? (
                    <td>{formatDate(row.orderDate)}</td>
                  ) : (
                    <td></td>
                  )}
                  {row.status && row.status === "1" ? (
                    <td>{t("approved")}</td>
                  ) : (
                    <td>{t("pending")}</td>
                  )}
                </tr>
              ))}
            </tbody>
          )
        )}
        {type === "ticket" && !data ? (
          <tbody>
            <TableSkeleton rowCount={6} cellCount={10} />
          </tbody>
        ) : type === "ticket" && data?.length === 0 ? (
          <tbody>
            <tr>
              <td colSpan="12">
                <div className="nodata-table-view">
                  <div className="nodata-table-view-box">
                    <div className="nodata-table-view-box-img">
                      <img src="/images/no-data-image1.jpg" alt="" />
                    </div>
                    <div className="nodata-table-view-box-txt">
                      {t("sorry_no_data_found")}
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          </tbody>
        ) : (
          type === "ticket" && (
            <tbody>
              {data?.map((row, rowIndex) => (
                <tr key={rowIndex}>
                  <td>{rowIndex + 1}</td>
                  {row.trackId && (
                    <td>
                      <Link to={`/ticket-details/${row.trackId}`}>
                        {row.trackId}
                      </Link>
                    </td>
                  )}
                  {row.subject && <td>{row.subject}</td>}
                  {row.assignee ? <td>{row.assignee}</td> : <td></td>}
                  {row.status && <td>{row.status}</td>}
                  {row.category && <td>{row.category}</td>}
                  {row.priority ? <td>{row.priority}</td> : <td></td>}
                  {row.createdAt && <td>{formatDate(row.createdAt)}</td>}
                  {row.lastUpdated && <td>{formatDate(row.lastUpdated)}</td>}
                  <td>
                    <Link to={`/ticket-timeline/${row.trackId}`}>
                      <i className="fa-solid fa-expand"></i>
                    </Link>
                  </td>
                </tr>
              ))}
            </tbody>
          )
        )}
      </table>
    </div>
  );
};

export default TableContent;
