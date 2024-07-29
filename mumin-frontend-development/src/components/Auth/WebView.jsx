const WebView = () => {
  const urlParams = new URLSearchParams(window.location.search);
  window.location.href = urlParams.get("type");
};

export default WebView;
