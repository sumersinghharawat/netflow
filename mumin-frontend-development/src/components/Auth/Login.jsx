import React, { useState, useRef } from "react";
import Input from "./FormInput.jsx";
import logo_user from "../../assests/images/logo_user.png";
import welcomeImg from "../../assests/images/welcomeImg.svg";
import SubmitButton from "../Common/buttons/SubmitButton.jsx";
import IconButton from "@mui/material/IconButton";
import Visibility from "@mui/icons-material/Visibility";
import InputAdornment from "@mui/material/InputAdornment";
import VisibilityOff from "@mui/icons-material/VisibilityOff";
import { ApiHook } from "../../hooks/apiHook.js";
import { toast } from "react-toastify";

const LoginForm = ({ params }) => {
  const [credentials, setCredentials] = useState({
    username: params.username ?? "",
    password: params.username ? "12345678" : "",
  });
  const [errorMessage, setErrorMessage] = useState({
    username: null,
    password: null,
    userCredentials: null,
  });
  const [showPassword, setShowPassword] = useState(false);
  const [selectedPage, setSelectedPage] = useState("login");
  const [username, setUsername] = useState("");
  const isSubmittingRef = useRef(false);

  const loginMutation = ApiHook.CallLoginUser();
  const forgotPasswordMutation = ApiHook.CallForgotPassword();

  const handleShowPassword = () => {
    setShowPassword(!showPassword);
  };
  const handleForgotUsername = (event) => {
    const { value } = event.target;
    setUsername(value);
  };
  const handleChange = (event) => {
    const { name, value } = event.target;
    setCredentials((prevCredentials) => ({
      ...prevCredentials,
      [name]: value,
    }));
    setErrorMessage((prev) => ({
      ...prev,
      [name]: null,
    }));

    setErrorMessage((prev) => ({
      ...prev,
      userCredentials: null,
    }));

    if (value === null || value === "") {
      setErrorMessage((prev) => ({
        ...prev,
        [name]: "*Required",
      }));
    }
  };

  const isFormValid = () => {
    return (
      credentials?.password.trim() !== "" && credentials?.username.trim() !== ""
    );
  };

  const handleForgotPassword = () => {
    forgotPasswordMutation.mutate(
      { username: credentials.username },
      {
        onSuccess: (res) => {
          if (res.status) {
            toast.success(res?.data);
          } else {
            toast.error(res?.description);
            setErrorMessage((prev) => ({
              ...prev,
              username: res?.description,
            }));
          }
        },
      }
    );
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    if (!isSubmittingRef.current) {
      isSubmittingRef.current = true;
      await loginMutation.mutateAsync(credentials, {
        onSuccess: (res) => {
          if (res?.code === 1003) {
            setErrorMessage({
              userCredentials: res?.data,
            });
          }
        },
      });
      isSubmittingRef.current = false;
    }
  };

  const resetPassword = async (event) => {
    event.preventDefault();
    if (!isSubmittingRef.current) {
      isSubmittingRef.current = true;
      const data = {
        username: username,
      };
      await forgotPasswordMutation.mutateAsync(data, {
        onSuccess: (res) => {
          if (res?.status) {
            toast.success(res?.data);
          } else {
            toast.error(res?.description);
          }
        },
      });
    }
  };

  return (
    <>
      <div className="col-md-6 logincredDetail">
        <div className="loginFormSec login_left_section">
          <div className="loginLogo">
            <img src={logo_user} alt="" />
          </div>
          <p>Welcome Back to Netflowpro</p>
          {selectedPage === "login" ? (
            <>
              <form onSubmit={handleSubmit}>
                {errorMessage?.userCredentials && (
                  <div style={{ color: "red", textAlign: "center" }}>
                    {errorMessage?.userCredentials}
                  </div>
                )}
                <Input
                  type="text"
                  id="fname"
                  name="username"
                  placeholder="Username"
                  value={credentials.username}
                  onChange={handleChange}
                />
                {errorMessage?.username && (
                  <div style={{ color: "red" }}>{errorMessage?.username}</div>
                )}
                <div className="LoginPasswordField">
                  <Input
                    type={showPassword ? "text" : "password"}
                    id="password"
                    name="password"
                    placeholder="Password"
                    value={credentials.password}
                    onChange={handleChange}
                  />
                  <InputAdornment
                    position="end"
                    style={{ position: "absolute", right: 0, top: 10 }}
                  >
                    <IconButton
                      onClick={handleShowPassword}
                      onMouseDown={(e) => e.preventDefault()}
                    >
                      {showPassword ? <Visibility /> : <VisibilityOff />}
                    </IconButton>
                  </InputAdornment>
                </div>
                {errorMessage?.password && (
                  <div style={{ color: "red" }}>{errorMessage?.password}</div>
                )}
                <a
                  className="forgetPassword"
                  onClick={() => setSelectedPage("resetPassword")}
                >
                  Forgot Password?
                </a>
                <div className="loginBtn">
                  <SubmitButton
                    isSubmitting={!isFormValid()}
                    click={handleSubmit}
                    text={loginMutation.isLoading ? "Submitting..." : "Login"}
                    className={"btn"}
                  />
                </div>
                {/* <p>
                  Don't have an account?{" "}
                  <a
                    href="https://infinitemlmsoftware.com/register.php"
                    style={{
                      fontSize: "16px",
                      textDecoration: "underline",
                      color: "rgb(61 66 195)",
                    }}
                  >
                    Signup now
                  </a>
                </p> */}
              </form>
            </>
          ) : (
            <form onSubmit={resetPassword}>
              <Input
                type="text"
                id="fname"
                name="username"
                placeholder="Username"
                value={username}
                onChange={handleForgotUsername}
              />
              {errorMessage?.username && (
                <div style={{ color: "red" }}>{errorMessage?.username}</div>
              )}
              <div className="loginBtn">
                <SubmitButton
                  // isSubmitting={!isFormValid()}
                  click={resetPassword}
                  text={
                    loginMutation.isLoading
                      ? "Sending mail..."
                      : "Change Password"
                  }
                  className={"btn"}
                />
              </div>
              <p>
                Don't have an account?{" "}
                <a
                  href="https://infinitemlmsoftware.com/register.php"
                  target="_blank"
                  style={{
                    fontSize: "16px",
                    textDecoration: "underline",
                    color: "rgb(61 66 195)",
                  }}
                >
                  Signup now
                </a>
              </p>
            </form>
          )}
        </div>
      </div>
      <div className="col-md-6">
        <div className="welcomeImgSec">
          <div className="welcomHeadSec">
            <p>Hello,</p>
            <h2>Welcome</h2>
            <p>Enter your credentials and login</p>
          </div>
          <div className="welcomeImg">
            <img src={welcomeImg} alt="" />
          </div>
        </div>
      </div>
    </>
  );
};

export default LoginForm;