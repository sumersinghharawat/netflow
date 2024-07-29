import * as yup from "yup";

export const profileDetailsSchema = yup.object().shape({
  name: yup
    .string()
    .required("First name is required")
    .matches(/^\S+$/, "First name cannot contain spaces")
});

export const contactDetailsSchema = yup.object().shape({
  address: yup.string().required("Address line1 is required"),
  address2: yup.string().required("Address line2 is required"),
  country: yup.string().required("Country is required"),
  state: yup.string().required("State is required"),
  city: yup.string().required("City is required"),
  zipCode: yup
    .number()
    .typeError("Zip code must be a number")
    .required("Zip code is required"),
  mobile: yup.number().required("Mobile number is required"),
})

export const bankDetailsSchema = yup.object().shape({
  bankName: yup.string().required("Bank name is required"),
  branchName: yup.string().required("Branch name is required"),
  accountHolder: yup.string().required("Account holder is required"),
  accountNumber: yup.string().required("Account number is required"),
  ifsc: yup.string().required("IFSC code is required"),
  pan: yup.string().required("PAN number is required"),
})
