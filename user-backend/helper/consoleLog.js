export default (...messages) => {
    const greenColor = '\x1b[32m';
    const resetColor = '\x1b[0m';
  
    const formattedMessages = messages.map((message) => {
      if (typeof message === 'object') {
        return JSON.stringify(message, null, 2);
      } else {
        return message;
      }
    });
  
    console.log(
      `${greenColor}[--------------LOG --------------- ]\n\n${formattedMessages.join(
        '\n\n'
      )}\n\n%c[--------------LOG END --------------- ]\n${resetColor}`
    );
}
  