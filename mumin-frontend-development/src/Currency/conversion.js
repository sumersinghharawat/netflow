import { useSelector } from "react-redux";

const CurrencyConverter = (amount, targetCurrency) => {
  const loginResponse = useSelector((state) => state.user?.loginResponse);
  const currencies = useSelector((state) => state.dashboard?.appLayout?.currencies);
  const selectedCurrency = JSON.parse(loginResponse?.user).currency?.code;
  const defaultCurrency = JSON.parse(loginResponse?.defaultCurrency).code;
  let sourceRate = null;
  let targetRate = null;

  // Find the exchange rates for the source and target currencies
  for (const currency of currencies) {
    if (currency.code === defaultCurrency) {
      sourceRate = currency.value;
    }
    if (currency.code === selectedCurrency) {
      targetRate = currency.value;
    }
  }

  // Check if both source and target exchange rates were found
  if (sourceRate === null || targetRate === null) {
    return "Unable to convert"; // Unable to convert
  }

  // Perform the conversion
  const convertedAmount = (amount / sourceRate) * targetRate;
  return convertedAmount.toFixed(2);

}
export default CurrencyConverter;
