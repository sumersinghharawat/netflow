import nodemailer from "nodemailer";
import hbs from "nodemailer-express-handlebars";
import path from "path";
import { consoleLog, logger } from "../helper/index.js";
import CommonMailSetting from "../models/commonMailSetting.js";
import MailSetting from "../models/mailSetting.js";
import dotenv from 'dotenv';
dotenv.config();

export const mailConfig = async (mailSettings) => {
    try {
        const transporter = nodemailer.createTransport({
            host: mailSettings.smtpHost,
            port: mailSettings.smtpPort,
            auth: {
                user: mailSettings.smtpUsername,
                pass: mailSettings.smtpPassword
            },
            secure: !("0" == mailSettings.smtpAuthentication),
            tls: {
                ciphers: "SSLv3"
            },
            logger: (mailSettings.NODE_ENV === 'development') ? true : false,
            debug: (mailSettings.NODE_ENV === 'development') ? true : false,
        })

        const __dirname     = path.dirname(new URL(import.meta.url).pathname);
        const handlebarOptions = {
            viewEngine: {
                partialsDir: `${__dirname}/../views/`,
                defaultLayout: false,
            },
            viewPath: `${__dirname}/../views/`,
        };

        const mail = transporter.use("compile", hbs(handlebarOptions));
        return mail;
    } catch (error) {
        logger.error("ERROR FROM mailConfig",error);
        // throw error;
    }
}

export const mailConfigDemo = async () => {
    try {
        const transporter = nodemailer.createTransport({
            host: process.env.MAIL_HOST,
            port: process.env.MAIL_PORT,
            auth: {
                user: process.env.MAIL_USERNAME,
                pass: process.env.MAIL_PASSWORD
            },
            secure  : false,
            tls: {
                ciphers: "SSLv3"
            },
            logger: true,
            debug: true,
        });

        const __dirname     = path.dirname(new URL(import.meta.url).pathname);
        console.log("__dirname",__dirname);
        const handlebarOptions = {
            viewEngine: {
                partialsDir: `${__dirname}/../views/`,
                defaultLayout: false,
            },
            viewPath: `${__dirname}/../views/`,
        };

        const mail = transporter.use("compile", hbs(handlebarOptions));

        return mail;
    } catch (error) {
        logger.error("ERROR FROM mailConfigDemo",error);
        throw error;
    }
};

export const sendMailDemo = async (Mail, email, mailArr) => {
    try {
        const fromAddress = "support@infinitemlmsoftware.com";
        const commonMailOptions = {
            from: fromAddress,
            to: email,
            subject: mailArr.subject,
        };

        let mailOptions;

        if (mailArr.type === "support") {
            mailOptions = {
                ...commonMailOptions,
                template: "email",
                context: {
                    subject: mailArr.subject,
                    content: mailArr.content,
                    logo: "https://ci4.googleusercontent.com/proxy/dYAFqYTLmZvNORKMpiIzwT9kitfv8p6BIzCaDZidLA4a1B3KF1rEW7UW-mdgpI66TQl293EjyDCtOXdSkUIDLCEOsTfrfXKWpSHmhzdWq2k6L5yJWsUm2g=s0-d-e1-ft#https://infinitemlmsoftware.com/wp-content/uploads/2019/08/logo-1.png",
                    footer: "Infinite Open Source Solutions , LLP KSITIL Special Economic Zone Sahya building Unit No 03 Nellikode (PO Govt Cyber Park, Kozhikode, Kerala 673016"
                }
            };
        } else {
            mailOptions = {
                ...commonMailOptions,
                template: "otp",
                context: {
                    fullname: mailArr.fullname,
                    otp: mailArr.otp,
                }
            };
        }

        const result = await Mail.sendMail(mailOptions);
        return result;
    } catch (error) {
        logger.error("ERROR FROM sendMailDemo",error);
        return false;
    }
};



