require('dotenv/config')
const Common = require("../../utils/web/common");
const jwt = require("jsonwebtoken");
const { errorMessage } = require("../../utils/web/response");
const verifyToken = async (req, res, next) => {
  try {
    const token = req.headers["access-token"];
    const api_key = req.headers["api-key"];
    if (!token || !api_key) {
        return res.status(403).json("A token is required for authentication");
    }
    if(process.env.DEMO_STATUS == 'yes') {
      var prefix = await Common.getPrefix(api_key);
    }else{
      var prefix = process.env.PREFIX
    }
    const decoded = jwt.verify(token, process.env.TOKEN_KEY);
    const tokenFromDB = await Common.getAccessTokenUnapproved(decoded.id, prefix);
    const apiKeyFromDB = await Common.getApiKey(prefix)
    if (tokenFromDB != token || tokenFromDB == false) {
        return res.status(401).json({ status: false });
    }
    if (apiKeyFromDB != api_key || apiKeyFromDB == false) {
      let response = await errorMessage({code:1001})
      return res.status(401).json(response);
    }
    // req.user = decoded;
    req.prefix = prefix
    return next();
  } catch (err) {
      console.log(err)
    return res.status(401).json({ status: false });
  }
};

module.exports = verifyToken;
