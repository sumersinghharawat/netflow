import CurrencyConverter from "../Currency/CurrencyConverter";


export const getEpins = (data, conversionFactor, Currency) => {
    let epinArray = []
    if (data === undefined || data === null) {
        return null;
    }
    Object.entries(data).map(([key, value]) => {
        epinArray.push({ 'label': `${value.numbers} ${Currency.symbolLeft}${CurrencyConverter(value.balanceAmount, conversionFactor)}`, 'value': value.numbers, 'amount': value.balanceAmount })
    });
    return epinArray
}