import { format } from "date-fns";


export const formatDate = (dateString) => {
  if (!dateString) {
    return ""; // Handle empty or null values gracefully.
  }
  const formattedDate = format(new Date(dateString), 'dd MMM yyyy HH:mm:ss');
  return formattedDate;
};

export const formatDateWithoutTime = (dateString) => {
  if (!dateString) {
    return ""; // Handle empty or null values gracefully.
  }
  const formattedDate = format(new Date(dateString), 'dd MMM yyyy');
  return formattedDate;
}

export const ticketFormatDate = (dateString) => {
  if (!dateString) {
    return ""; // Handle empty or null values gracefully.
  }
  const formattedDate = format(new Date(dateString), 'dd/MMM/yyyy  HH:mm');
  return formattedDate;
};

export const crmFormateDate = (dateString) => {
  if (!dateString) {
    return ""; // Handle empty or null values gracefully.
  }
  const formattedDate = format(new Date(dateString), 'MM-dd-yyyy');
  return formattedDate;
}