import React from "react";
import SubmitButton from "../Common/buttons/SubmitButton";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import { useTranslation } from "react-i18next";
import Skeleton from "react-loading-skeleton";
import { Link } from "react-router-dom";

const UpgradeComponents = ({
  data,
  handleUpgradeClick,
  currency,
  conversionFactor,
}) => {
  const { t } = useTranslation();
  return (
    <div className="package_upgrade_cnt_sec">
      <h4>
        <Link to={"/dashboard"} className="back_btn">
          <i className="fa-solid fa-arrow-left"></i>
        </Link>
      </h4>
      <div className="plan-options">
        {!data ? (
          <>
            <Skeleton
              height={550}
              highlightColor="#d9d1e1"
              width={350}
              count={1}
            />
            <Skeleton
              height={550}
              highlightColor="#d9d1e1"
              width={350}
              count={1}
            />
            <Skeleton
              height={550}
              highlightColor="#d9d1e1"
              width={350}
              count={1}
            />
          </>
        ) : (
          data?.map((item, index) => (
            <div key={index}>
              {item?.upgradable === 0 && (
                <div className="plan old_plan" key={index}>
                  <div className="plan-header">
                    <h5 className="plan-heading">{item?.pack?.name}</h5>
                  </div>
                  <div className="upgrade_desc">
                    <p className="amnt_view_plan">
                      {currency?.symbolLeft}
                      <span className="plan-amount">
                        {CurrencyConverter(item?.pack?.price, conversionFactor)}
                      </span>
                      <span className="pvValuewPlan">{`${t("pv")} : ${
                        item?.pack?.pairValue
                      }`}</span>
                    </p>
                  </div>
                  <ul className="plan-feature">
                    <li>
                      {t("upgradeValidity")} : {item?.pack?.validity}
                    </li>
                    {!!item?.binaryCommission?.status &&
                      (item?.binaryCommission?.type === "flat" ? (
                        <li>
                          {t("leg")} :{" "}
                          {`${currency?.symbolLeft} ${CurrencyConverter(
                            item?.binaryCommission?.value,
                            conversionFactor
                          )}`}
                        </li>
                      ) : (
                        <li>
                          {t("leg")} : {`${item?.binaryCommission?.value} %`}
                        </li>
                      ))}
                    {!!item?.referralCommission?.status &&
                      (item?.referralCommission?.type === "flat" ? (
                        <li>
                          {t("referral")} :{" "}
                          {`${currency?.symbolLeft} ${CurrencyConverter(
                            item?.referralCommission?.value,
                            conversionFactor
                          )}`}
                        </li>
                      ) : (
                        <li>
                          {t("referral")} :{" "}
                          {`${item?.referralCommission?.value} %`}
                        </li>
                      ))}
                    {!!item?.levelCommission?.status && (
                      <li>
                        {t("level_commission")}:
                        <ul style={{ listStyleType: "disc" }}>
                          {item.levelCommission.value.map(
                            (levelData, levelIndex) => (
                              <li key={levelIndex}>
                                {item?.levelCommission?.type === "flat"
                                  ? `${t("level")} ${levelData.level}: ${
                                      currency?.symbolLeft
                                    } ${CurrencyConverter(
                                      levelData.commission,
                                      conversionFactor
                                    )}`
                                  : `${t("level")} ${levelData.level} : ${
                                      levelData.commission
                                    } %`}
                              </li>
                            )
                          )}
                        </ul>
                      </li>
                    )}
                    {!!item?.rankCommission?.status && (
                      <li>
                        {t("rank")} : {item?.rankCommission?.value?.name}
                      </li>
                    )}
                  </ul>
                  <p className="plan_btn">
                    <button className="plan-choose">{t("oldPlan")}</button>
                  </p>
                </div>
              )}
              {item?.upgradable === 1 && (
                <div className="plan current_plan" key={index}>
                  <div className="plan-header">
                    <h3 className="plan-heading">{item?.pack?.name}</h3>
                  </div>
                  <div className="upgrade_desc">
                    <p className="amnt_view_plan">
                      {currency?.symbolLeft}
                      <span className="plan-amount">
                        {CurrencyConverter(item?.pack?.price, conversionFactor)}
                      </span>
                      <span className="pvValuewPlan">{`${t("pv")} : ${
                        item?.pack?.pairValue
                      }`}</span>
                    </p>
                  </div>
                  <ul className="plan-feature">
                    <li>
                      {t("upgradeValidity")} : {item?.pack?.validity}
                    </li>
                    {!!item?.binaryCommission?.status &&
                      (item?.binaryCommission?.type === "flat" ? (
                        <li>
                          {t("leg")} :{" "}
                          {`${currency?.symbolLeft} ${CurrencyConverter(
                            item?.binaryCommission?.value,
                            conversionFactor
                          )}`}
                        </li>
                      ) : (
                        <li>
                          {t("leg")} : {`${item?.binaryCommission?.value} %`}
                        </li>
                      ))}
                    {!!item?.referralCommission?.status &&
                      (item?.referralCommission?.type === "flat" ? (
                        <li>
                          {t("referral")} :{" "}
                          {`${currency?.symbolLeft} ${CurrencyConverter(
                            item?.referralCommission?.value,
                            conversionFactor
                          )}`}
                        </li>
                      ) : (
                        <li>
                          {t("referral")} :{" "}
                          {`${item?.referralCommission?.value} %`}
                        </li>
                      ))}
                    {!!item?.levelCommission?.status && (
                      <li>
                        {t("level_commission")}:
                        <ul style={{ listStyleType: "disc" }}>
                          {item.levelCommission.value.map(
                            (levelData, levelIndex) => (
                              <li key={levelIndex}>
                                {item?.levelCommission?.type === "flat"
                                  ? `${t("level")} ${levelData.level}: ${
                                      currency?.symbolLeft
                                    } ${CurrencyConverter(
                                      levelData.commission,
                                      conversionFactor
                                    )}`
                                  : `${t("level")} ${levelData.level} : ${
                                      levelData.commission
                                    } %`}
                              </li>
                            )
                          )}
                        </ul>
                      </li>
                    )}
                    {!!item?.rankCommission?.status && (
                      <li>
                        {t("rank")} : {item?.rankCommission?.value?.name}
                      </li>
                    )}
                  </ul>
                  <p className="plan_btn">
                    <button className="plan-choose">{t("current")}</button>
                  </p>
                </div>
              )}
              {item?.upgradable === 2 && (
                <div className="plan" key={index}>
                  <div className="plan-header">
                    <h3 className="plan-heading">{item?.pack?.name}</h3>
                  </div>
                  <div className="upgrade_desc">
                    <p className="amnt_view_plan">
                      {currency?.symbolLeft}
                      <span className="plan-amount">
                        {CurrencyConverter(item?.pack?.price, conversionFactor)}
                      </span>
                      <span className="pvValuewPlan">{`${t("pv")} : ${
                        item?.pack?.pairValue
                      }`}</span>
                    </p>
                  </div>
                  <ul className="plan-feature">
                    <li>
                      {t("upgradeValidity")} : {item?.pack?.validity}
                    </li>
                    {!!item?.binaryCommission?.status &&
                      (item?.binaryCommission?.type === "flat" ? (
                        <li>
                          {t("leg")} :{" "}
                          {`${currency?.symbolLeft} ${CurrencyConverter(
                            item?.binaryCommission?.value,
                            conversionFactor
                          )}`}
                        </li>
                      ) : (
                        <li>
                          {t("leg")} : {`${item?.binaryCommission?.value} %`}
                        </li>
                      ))}
                    {!!item?.referralCommission?.status &&
                      (item?.referralCommission?.type === "flat" ? (
                        <li>
                          {t("referral")} :{" "}
                          {`${currency?.symbolLeft} ${CurrencyConverter(
                            item?.referralCommission?.value,
                            conversionFactor
                          )}`}
                        </li>
                      ) : (
                        <li>
                          {t("referral")} :{" "}
                          {`${item?.referralCommission?.value} %`}
                        </li>
                      ))}
                    {!!item?.levelCommission?.status && (
                      <li>
                        {t("level_commission")}:
                        <ul style={{ listStyleType: "disc" }}>
                          {item.levelCommission.value.map(
                            (levelData, levelIndex) => (
                              <li key={levelIndex}>
                                {item?.levelCommission?.type === "flat"
                                  ? `${t("level")} ${levelData.level}: ${
                                      currency?.symbolLeft
                                    } ${CurrencyConverter(
                                      levelData.commission,
                                      conversionFactor
                                    )}`
                                  : `${t("level")} ${levelData.level} : ${
                                      levelData.commission
                                    } %`}
                              </li>
                            )
                          )}
                        </ul>
                      </li>
                    )}
                    {!!item?.rankCommission?.status && (
                      <li>
                        {t("rank")} : {item?.rankCommission?.value?.name}
                      </li>
                    )}
                  </ul>
                  <p className="plan_btn">
                    <SubmitButton
                      className="plan-choose"
                      text="upgrade"
                      isSubmitting={!item?.upgradable}
                      click={() => handleUpgradeClick(item?.pack)}
                    />
                  </p>
                </div>
              )}
            </div>
          ))
        )}
      </div>
    </div>
  );
};

export default UpgradeComponents;
