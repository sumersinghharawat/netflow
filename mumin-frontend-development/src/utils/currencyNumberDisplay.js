

export const reverseNumberDisplay = (str) => {
    if (str === "NaN") return NaN;
    if (str === "0") return 0;
    const lastChar = str.charAt(str.length - 1);
    const numericValue = parseFloat(str);

    if (lastChar === "k") return numericValue * 1e3;
    if (lastChar === "m") return numericValue * 1e6;
    if (lastChar === "B") return numericValue * 1e9;

    return numericValue;
};

export const numberDisplay = (num) => {
    if (isNaN(num)) return "NaN";
    if (num === 0) return "0";

    let number = Math.abs(num);
    if (number < 1e3) return number.toFixed(2);
    if (number < 1e6) return (number / 1e3).toFixed(2) + "k";
    if (number < 1e9) return (number / 1e6).toFixed(2) + "m";
    if (number >= 1e9) return (number / 1e9).toFixed(2) + "B";
};

