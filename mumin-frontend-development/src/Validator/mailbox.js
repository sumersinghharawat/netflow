


export const validateEmailFields = (recipients, setErrors) => {
    const emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}/;
    const newErrors = {};

    if (!recipients.to) {
        newErrors.to = "this_field_is_required";
    } else if (!recipients.to.match(emailRegex)) {
        newErrors.to = "invalid_email_format";
    } else {
        newErrors.to = "";
    }

    setErrors(newErrors);

    // Check if there are no errors
    return Object.values(newErrors).every((error) => !error);
};