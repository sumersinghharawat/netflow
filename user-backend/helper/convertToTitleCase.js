export default (phrase) => {
    // eg. "upgrade_level_commission" -> "Upgrade Level Commission"
    return (phrase).replace(/_/g, ' ').replace(/\b\w/g, (match) => match.toUpperCase());
}