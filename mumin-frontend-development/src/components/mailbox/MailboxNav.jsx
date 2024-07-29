import { useTranslation } from "react-i18next";

const MailboxNav = () => {
  const { t } = useTranslation();

  return <div className="page_head_top mail_box_top_hx">{t("mailBox")}</div>;
};

export default MailboxNav;
