import React, { useState, useEffect, useRef } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { updateConversionFactors } from "../../store/reducers/userReducer";
import { useDispatch, useSelector } from "react-redux";
import { ApiHook } from "../../hooks/apiHook";
import { useTranslation } from "react-i18next";
import { formatDate } from "../../utils/formateDate";
import Skeleton from "react-loading-skeleton";

const HeaderSection = ({
  count,
  handleLeftMenuToggle,
  userName,
  appLayout,
  toggleMobileRef,
}) => {
  const userSelectedCurrency = useSelector(
    (state) => state.user?.selectedCurrency
  );
  const userSelectedLanguage = useSelector(
    (state) => state.user?.selectedLanguage
  );
  const [notificationCheck, setNotificationCheck] = useState(false);
  const moduleStatus = appLayout?.moduleStatus;
  const [dropdownOpen, setDropdownOpen] = useState({
    currency: false,
    country: false,
    notification: false,
    user: false,
  });
  const [selectedCurrency, setSelectedCurrency] = useState({
    id: null,
    symbolLeft: null,
    code: null,
    value: null,
  });
  const [selectedLanguage, setSelectedLanguage] = useState({
    id: null,
    flagImage: null,
    code: null,
    name: null,
  });
  const logoutMutation = ApiHook.CallLogout();
  const updateCurrencyMutation = ApiHook.CallCurrencyUpdation({
    selectedCurrency,
  });
  const updateLanguageMutation = ApiHook.CallLanguageUpdation({
    selectedLanguage,
  });
  const notificationData = ApiHook.CallNotificationData(
    notificationCheck,
    setNotificationCheck
  );
  const readAllNotification = ApiHook.CallReadAllNotification();

  const dropdownCurrencyRef = useRef(null);
  const dropdownCountryRef = useRef(null);
  const dropdownNotificationRef = useRef(null);
  const dropdownUserRef = useRef(null);
  const navigate = useNavigate();
  const dispatch = useDispatch();
  const location = useLocation();
  const { t, i18n } = useTranslation();

  const toggleDropdown = (dropdown) => {
    if (dropdown === "notification") {
      setNotificationCheck(true);
    }
    setDropdownOpen((prevState) => ({
      currency: dropdown === "currency" ? !prevState.currency : false,
      country: dropdown === "country" ? !prevState.country : false,
      notification:
        dropdown === "notification" ? !prevState.notification : false,
      user: dropdown === "user" ? !prevState.user : false,
    }));
  };
  useEffect(() => {
    if (location.pathname) {
      setDropdownOpen(false);
    }
    const handleOutsideClick = (event) => {
      const dropdownRefs = [
        dropdownCurrencyRef,
        dropdownCountryRef,
        dropdownNotificationRef,
        dropdownUserRef,
      ];
      const isClickInsideDropdown = dropdownRefs.some(
        (ref) => ref.current && ref.current.contains(event.target)
      );
      if (!isClickInsideDropdown) {
        setDropdownOpen({
          currency: false,
          country: false,
          notification: false,
          user: false,
        });
      }
    };

    document.addEventListener("click", handleOutsideClick);

    return () => {
      document.removeEventListener("click", handleOutsideClick);
    };
  }, [location.pathname]);

  const dropdownCurrencies = appLayout?.currencies;

  const dropdownCountries = appLayout?.languages;

  const handleLogout = async () => {
    logoutMutation.mutate();
  };

  const changeCurrency = (currency) => {
    const newCurrency = {
      currencyId: currency?.id.toString(),
    };
    setSelectedCurrency({
      id: currency?.id,
      symbolLeft: currency?.symbolLeft,
      value: currency?.value,
      code: currency?.code,
    });
    updateCurrencyMutation.mutateAsync(newCurrency);

    // update conversionFactor
    dispatch(updateConversionFactors(currency));
    setDropdownOpen({ currency: false });
  };
  const changeLanguage = (language) => {
    const newLanguage = { langId: language?.id.toString() };
    setSelectedLanguage({
      id: language?.id,
      flagImage: language?.flagImage,
      code: language?.code,
      name: language?.name,
    });
    updateLanguageMutation.mutateAsync(newLanguage);
    setDropdownOpen({ country: false });
    i18n.changeLanguage(language?.code);
  };
  const handleReadAll = () => {
    readAllNotification.mutateAsync();
  };

  return (
    <header className="header_section">
      <div className="row">
        <div className="col-md-4 col-6">
          <div className="leftLogo_section">
            <div
              ref={toggleMobileRef}
              className="left_mn_toggle_btn left_mn_toogle_btn"
              onClick={handleLeftMenuToggle}
            >
              <i className="fa-solid fa-bars"></i>
            </div>
            <Link to={"/dashboard"}>
              <img
                // src={appLayout?.companyProfile?.logo ?? "/images/logo_user.png"}
                src={"/images/logo_user.png"}
                onClick={() => navigate("/dashboard")}
                alt=""
              />
            </Link>
          </div>
        </div>
        <div className="col-md-8 col-6">
          <div className="right_notiifcation_mail_ico_sec">
            {moduleStatus?.multi_currency_status === 1 && (
              <div
                className={`right_notiifcation_mail_ico top_dropdown currency_dropdown ${
                  dropdownOpen.currency ? "show" : ""
                }`}
                ref={dropdownCurrencyRef}
              >
                <a
                  href="#"
                  className="dropdown-toggle"
                  onClick={() => toggleDropdown("currency")}
                  data-bs-toggle="dropdown"
                  aria-expanded={dropdownOpen.currency}
                >
                  <span className="currency_top_slctd">
                    {userSelectedCurrency?.symbolLeft}
                  </span>
                </a>
                <div
                  className={`dropdown-menu usr_prfl right-0 animation slideDownIn ${
                    dropdownOpen.currency ? "show" : ""
                  }`}
                >
                  <div className="usr_prfl_setting">{t("currency")}</div>
                  <ul className="">
                    {dropdownCurrencies?.map((item, index) => (
                      <li key={index}>
                        <a
                          className="dropdown-item"
                          onClick={() => changeCurrency(item)}
                        >
                          <span>{item.symbolLeft}</span> {item.title}
                        </a>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            )}
            {moduleStatus?.multilang_status === 1 && (
              <div
                className={`right_notiifcation_mail_ico top_dropdown country_dropdown ${
                  dropdownOpen.country ? "show" : ""
                }`}
                ref={dropdownCountryRef}
              >
                <a
                  href="#"
                  className="dropdown-toggle"
                  onClick={() => toggleDropdown("country")}
                  data-bs-toggle="dropdown"
                  aria-expanded={dropdownOpen.country}
                >
                  <img src={`/${userSelectedLanguage?.flagImage}`} alt="" />
                </a>
                <div
                  className={`dropdown-menu usr_prfl right-0 animation slideDownIn ${
                    dropdownOpen.country ? "show" : ""
                  }`}
                >
                  <div className="usr_prfl_setting">{t("country")}</div>
                  <ul className="">
                    {dropdownCountries?.map((item, index) => (
                      <li key={index}>
                        <a
                          className="dropdown-item"
                          onClick={() => changeLanguage(item)}
                        >
                          <img src={`/${item?.flagImage}`} alt="" /> {item.name}
                        </a>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            )}
            <div className="right_notiifcation_mail_ico">
              <Link to="/mailbox">
                <img src="/images/mail_ico.svg" alt="" />
              </Link>
              {appLayout && appLayout?.mailCount !== 0 && (
                <div className="notification_count">{appLayout?.mailCount}</div>
              )}
            </div>
            <div
              className={`right_notiifcation_mail_ico ${
                dropdownOpen.notification ? "show" : ""
              }`}
              ref={dropdownNotificationRef}
            >
              <a
                className="dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-expanded={dropdownOpen.notification}
                href="#"
                onClick={() => toggleDropdown("notification")}
              >
                <img src="/images/notification_ico.svg" alt="" />
              </a>
              {count != 0 && (
                <div className="notification_count">{count ?? 0}</div>
              )}
              <div
                className={`dropdown-menu notification_list right-0 animation slideDownIn ${
                  dropdownOpen.notification ? "show" : ""
                }`}
              >
                <div className="notification_list_head">
                  {t("notifications")}
                  <i
                    className="fa-solid fa-check-double"
                    onClick={handleReadAll}
                  ></i>
                </div>
                <ul className="notification_list_box">
                  {!notificationData?.data?.data ? (
                    <div className="teammbrs_cnt_row">
                      <div className="teammbrs_cnt_img">
                        <Skeleton
                          circle
                          width="45px"
                          height="45px"
                          containerClassName="avatar-skeleton"
                          count={2}
                        />
                      </div>
                      <div className="teammbrs_cnt_name_dtl">
                        <div className="teammbrs_cnt_name">
                          <Skeleton count={4} />
                        </div>
                      </div>
                    </div>
                  ) : notificationData?.data?.data?.length === 0 ? (
                    <li className="no-data-div">
                      <div className="no-data-div-image">
                        <img src="/images/nodata-image.png" alt="" />
                      </div>
                      <p>{t("noDataFound")}</p>
                    </li>
                  ) : (
                    notificationData?.data?.data.map((notification) => (
                      <li key={notification.request_id}>
                        <a className="dropdown-item" href="#">
                          <span className="notifc_module">
                            {notification?.image}
                          </span>
                          {notification?.title}
                          <span>{formatDate(notification?.date)}</span>
                        </a>
                      </li>
                    ))
                  )}
                </ul>
              </div>
            </div>
            <div
              className={`right_notiifcation_mail_ico user_avatar ${
                dropdownOpen.user ? "show" : ""
              }`}
              ref={dropdownUserRef}
            >
              <a
                href="#"
                className="dropdown-toggle"
                data-bs-toggle="dropdown"
                aria-expanded={dropdownOpen.user}
                onClick={() => toggleDropdown("user")}
              >
                <img
                  src={
                    appLayout?.user?.image
                      ? appLayout?.user?.image
                      : "/images/user-profile.png"
                  }
                  alt=""
                />
              </a>
              <div
                className={`dropdown-menu usr_prfl right-0 animation slideDownIn ${
                  dropdownOpen.user ? "show" : ""
                }`}
              >
                <div className="usr_prfl_setting">{userName}</div>
                <ul className="">
                  <li key="profile">
                    <Link to={"/profile"} className="dropdown-item">
                      {t("profile")}
                    </Link>
                  </li>
                  <li key="logout">
                    <a className="dropdown-item" onClick={handleLogout}>
                      {t("logout")}
                    </a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>
  );
};

export default HeaderSection;
