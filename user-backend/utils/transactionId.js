import Transaction from "../models/transaction.js";

export default async (length = 16) => {
    const charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    const charactersLength = charset.length;
    let randomString = '';
  
    for (let i = 0; i < length; i++) {
      randomString += charset.charAt(Math.floor(Math.random() * charactersLength));
    }
  
    const transactionExists = await Transaction.findOne({ transaction_id: randomString });
  
    // if (transactionExists) {
    //   return await(length);
    // }
  
    return randomString;
};