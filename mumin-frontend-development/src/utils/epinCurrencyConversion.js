import CurrencyConverter from "../Currency/CurrencyConverter";

export const epinCurrencyConverter = (epinAmounts, conversionFactor) => {
  const convertedEpinAmounts = [];
  if (epinAmounts) {
    for (const epin of epinAmounts) {
      const convertedValue = CurrencyConverter(epin?.label, conversionFactor);
      convertedEpinAmounts.push({
        ...epin,
        label: convertedValue
      });
    }
  }
  return convertedEpinAmounts;
};
