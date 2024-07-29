import { BrowserRouter, Routes, Route, Navigate } from "react-router-dom";
import { privateRoutes, publicRoutes, webRoutes } from "./Routes/router";
import MainLayout from "./Layouts/MainLayout";
import { useDispatch, useSelector } from "react-redux";
import {
  setIsAuthenticated,
  setLoginResponse,
} from "./store/reducers/userReducer";
import { useEffect } from "react";
import { setLoginResponseCheck } from "./utils/checkStorage";

function App() {
  const dispatch = useDispatch();
  const isLogin = useSelector((state) => state.user?.isAuthenticated);
  const token = !!localStorage.getItem("access-token");
  const isAuthenticated = isLogin || token;

  useEffect(() => {
    if (token) {
      const data = setLoginResponseCheck();
      dispatch(setLoginResponse(data));
      dispatch(setIsAuthenticated(true));
    }
  }, [dispatch, token]);

  const renderPrivateRoutes = () => {
    return privateRoutes.map((route, index) => (
      <Route key={index} path={route.path} element={route.element} />
    ));
  };

  const renderWebRoutes = () => {
    return webRoutes.map((route, index) => (
      <Route key={index} path={route.path} element={route.element} />
    ));
  };

  const renderPublicRoutes = () => {
    return publicRoutes.map((route, index) => (
      <Route key={index} path={route.path} element={route.element} />
    ));
  };
  return (
    <BrowserRouter>
      <Routes>
        {isAuthenticated ? (
          <>
            <Route path="/" element={<Navigate to="/dashboard" replace />} />
            <Route
              path="/login"
              element={<Navigate to="/dashboard" replace />}
            />
            <Route path="/" element={<MainLayout />}>
              {renderPrivateRoutes()}
            </Route>
            {renderWebRoutes()}
            <Route path="*" element={<Navigate to="/dashboard" replace />} />
          </>
        ) : (
          <>
            {renderPublicRoutes()}
            <Route path="*" element={<Navigate to="/login" replace />} />
          </>
        )}
      </Routes>
    </BrowserRouter>
  );
}

export default App;
