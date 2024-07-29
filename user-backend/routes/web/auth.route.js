import { Router } from "express";
import { backToBackOffice, forgotPassword, getAccessToken, logout, verifyForgotPassword, updatePassword, getCompanyLogo } from '../../controllers/web/authController.js';
import { validateRequest } from '../../middleware/validateRequest.js';
import { loginSchema } from "../../validators/loginValidator.js";
import auth from "../../middleware/web/auth.middleware.js";
import { passwordUpdateSchema } from "../../validators/passwordUpdateValidator.js";
import { paypalWebhookEvent } from "../../controllers/web/subscriptionController.js";
const router = Router();
// router.use('/auth', router);

router.get("/auth/get-company-logo", getCompanyLogo);
router.post("/auth/access", validateRequest(loginSchema), getAccessToken);
router.post("/auth/logout", auth, logout);
router.post("/auth/back-to-backoffice", backToBackOffice);
router.post("/auth/forgot-password", forgotPassword);
router.post("/auth/verify-forgot-password", verifyForgotPassword);
router.post("/auth/update-password", validateRequest(passwordUpdateSchema), updatePassword);
router.post("/auth/paypal-webhook",paypalWebhookEvent);

export default router;