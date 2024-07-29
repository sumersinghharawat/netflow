export default (num) => {
    /* for frontend display purposes. convert a number to a string with two decimal 
    places. displays larger numbers in k, m, or B format. */ 
    if (isNaN(num)) return "NaN";

    let number = Math.abs(num);
    if (number<1e3) return number.toFixed(0);
    if (number<1e6) return (number/1e3).toFixed(0) + "k";
    if (number<1e9) return (number/1e6).toFixed(0) + "m";
    if (number>=1e9) return (number/1e9).toFixed(0) + "B";

};