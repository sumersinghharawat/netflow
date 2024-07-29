import { MailServices } from "../../services/mailbox/mailbox";

export const Inboxes = async (page, limit) => {
  try {
    const response = await MailServices.getInboxes(page, limit);
    return response;
  } catch (error) {
    console.log(error.message);
  }
};

export const SingleMail = async (id, type) => {
  try {
    const response = await MailServices.getSingleMail(id, type);
    return response;
  } catch (error) {
    console.log(error.message);
  }
};

export const ReplyMail = async (replyMail) => {
  try {
    const response = await MailServices.replyMail(replyMail);
    return response.data;
  } catch (error) {
    console.log(error.message);
  }
};

export const AdminInboxes = async (page, limit) => {
  try {
    const response = await MailServices.getInboxFromAdmin(page, limit);
    return response;
  } catch (error) {
    console.log(error.message);
  }
};

export const SendInternalMail = async (mailContent) => {
  try {
    const response = await MailServices.sendInternalMail(mailContent);
    return response.data;
  } catch (error) {
    console.log(error.message);
  }
};

export const DeleteMail = async (mailId) => {
  try {
    const response = await MailServices.deleteMail(mailId);
    return response.data;
  } catch (error) {
    console.log(error.message);
  }
};

export const SentMail = async (page, limit) => {
  try {
    const response = await MailServices.sentMail(page, limit);
    return response;
  } catch (error) {
    console.log(error.message);
  }
};

export const replicaInbox = async (page, limit) => {
  try {
    const response = await MailServices.contacts(page, limit);
    return response;
  } catch (error) {
    console.log(error.message);
  }
};
