import multer from "multer";
import fs from "fs/promises";
import { resolve } from "path";
import { consoleLog } from "../helper/index.js";

const storage = multer.diskStorage({
	destination: async (req, file, callback) => {
		const destinationFolder = `uploads/${req.folderName}`;
		const fullPath = resolve(destinationFolder);
		try {
			await fs.mkdir(fullPath, { recursive: true });
			callback(null, destinationFolder);
		} catch (error) {
			callback(error);
		}
	},
	filename: (req, file, callback) => {
		callback(null, Date.now() + "-" + file.originalname);
	},
});

const fileFilter = (req, file, callback) => {
	const allowedMimes = ["image/jpeg", "image/png", "image/gif"];
	if (allowedMimes.includes(file.mimetype)) {
		callback(null, true); // Accept the file
	} else {
		callback(
			new Error(
				"1017",
			),
			false
		); // Reject the file
	}
};
const fileSize = 2 * 1024 * 1024;

const upload = multer({ storage, fileFilter, limits: { fileSize } });

const uploadFile = (req, res) => {
	return new Promise((resolve, reject) => {
		try {
			req.folderName = req.query.type;
			upload.array("file", 2)(req, res, (err) => {
				if (err instanceof multer.MulterError) {
					if (err.code === 'LIMIT_FILE_SIZE') {
						reject({ message: 'file_size_limit_exceeded', error: '1018' });
					  } else if( err.code === 'LIMIT_UNEXPECTED_FILE') {
						reject({ message: 'file_upload_failed', error: "1115" });
					  } else {
						reject({ message: 'file_upload_failed', error: "1017" });
					  }
				} else if (err) {
					reject({ message: 'file_upload_failed', error: err.message });
				} else if (!req.files || req.files.length === 0) {
					reject({ message: 'no_file_selected', error: "1032" });
				} else {
					const filesData = req.files.map((file) => ({
						filename: file.filename,
						path: file.path,
						mimetype: file.mimetype,
					  }));
					let data = {};
					// if(req.query && req.query.type === 'register') {
						data.username 	= req.body.username;
						data.type 		= req.query.type
					// }
					resolve({ message: 'file_uploaded_successfully', file: filesData, data });
				}
			});
		} catch (error) {
			reject({ message: 'file_upload_failed', error: '1100' });
		}
	});
};

export { uploadFile };
