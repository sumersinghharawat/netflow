import { Router } from "express";
import auth from "../../middleware/web/auth.middleware.js";
import { validateRequest } from "../../middleware/validateRequest.js";
import { getReplicaBanner, getReplicaHome, 
    uploadReplicaBanner, uploadReplicaContact, uploadReplicaPaymentReceipt,
    getReplicaRegister, checkEmailUsername, replicaRegister, deleteReplicaBanner, deleteReplicaPaymentReceipt ,createPaymentIntent
} from "../../controllers/web/replicaController.js";
import { replicaContactSchema, replicaRegisterSchema } from "../../validators/replicaValidator.js";


const router = Router();
router.get("/get-replica-banner", auth, getReplicaBanner);
router.post("/upload-replica-banner", auth, uploadReplicaBanner);
router.delete("/delete-replica-banner", auth, deleteReplicaBanner);

router.get("/replica-home", getReplicaHome);
router.post("/replica-contact-upload", validateRequest(replicaContactSchema), uploadReplicaContact);
router.post("/replica-payment-receipt-upload", uploadReplicaPaymentReceipt);
router.post("/replica-payment-receipt-delete", deleteReplicaPaymentReceipt);
router.get("/replica-register-get", getReplicaRegister);
router.get('/replica-checkUsernameEmail', checkEmailUsername);
router.post("/replica-register-post", replicaRegister);
router.post("/create-payment-intent", createPaymentIntent);


export default router;