const CurrencyConverter = (amount, conversionFactor, rev = 0) => {
  const currencies = conversionFactor && conversionFactor.currencies;
  const selectedCurrency =
    conversionFactor && conversionFactor.selectedCurrency;
  const defaultCurrency = conversionFactor && conversionFactor.defaultCurrency;
  let convertedAmount = null;
  let sourceRate = null;
  let targetRate = null;

  const numberDisplay = (num) => {
    if (isNaN(num)) return "0";
    if (num === 0) return "0";

    let number = Math.abs(num);
    if (number < 1e3) return number.toFixed(2);
    if (number < 1e6) return (number / 1e3).toFixed(2) + "k";
    if (number < 1e9) return (number / 1e6).toFixed(2) + "m";
    if (number >= 1e9) return (number / 1e9).toFixed(2) + "B";
  };

  if (currencies) {
    for (const currency of currencies) {
      if (currency.code === defaultCurrency.code) {
        if (rev) {
          targetRate = currency?.value;
        } else {
          sourceRate = currency?.value;
        }
      }
      if (currency?.code === selectedCurrency?.code) {
        if (rev) {
          sourceRate = currency?.value;
        } else {
          targetRate = currency?.value;
        }
      }
    }

    if (sourceRate === null || targetRate === null) {
      return "0";
    }
    if (amount === "NA") {
      return "NA";
    }
    convertedAmount = (Number(amount) / sourceRate) * targetRate;
    return numberDisplay(convertedAmount);
  } else {
    return numberDisplay(Number(amount))
  }
};

export default CurrencyConverter;
