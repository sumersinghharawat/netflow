import { DEFAULT_KEY } from "../../config/config";
import backToOffice from "../../services/auth/backToOffice";

const BackToOffice = () => {
  const urlParams = new URLSearchParams(window.location.search);
  let string = urlParams.get("string");
  let code = DEFAULT_KEY;
  backToOffice.accessToken(string, code).then((res) => {
    localStorage.setItem("access-token", res.data?.accessToken);
    localStorage.setItem("api-key", res.data?.apiKey);
    localStorage.setItem("user", JSON.stringify(res.data?.user));
    localStorage.setItem(
      "defaultCurrency",
      JSON.stringify(res.data?.defaultCurrency)
    );
    localStorage.setItem(
      "defaultLanguage",
      JSON.stringify(res.data?.defaultLanguage)
    );
    window.location.href = "/dashboard";
  });
};

export default BackToOffice;
