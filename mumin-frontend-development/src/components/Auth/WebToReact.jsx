import webToReact from "../../services/auth/webToReact";

const WebToReact = () => {
  const urlParams = new URLSearchParams(window.location.search);
  let string = urlParams.get("string");
  let code = urlParams.get("db_prefix");
  let type = urlParams.get("type");
  webToReact.accessToken(string, code)
  .then((res)=>{
      localStorage.setItem('access-token',res.data?.accessToken)
      localStorage.setItem('api-key',res.data?.apiKey)
      localStorage.setItem('user',JSON.stringify(res.data?.user))
      localStorage.setItem('defaultCurrency',JSON.stringify(res.data?.defaultCurrency))
      localStorage.setItem('defaultLanguage',JSON.stringify(res.data?.defaultLanguage))
      window.location.href = `${type}`
  })
};

export default WebToReact;
