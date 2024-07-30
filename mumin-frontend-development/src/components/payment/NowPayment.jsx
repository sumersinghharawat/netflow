import { useEffect, useState } from "react";
import axios from "axios";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import Alert from "@mui/material/Alert";
import { Button } from "@mui/material";
import { BASE_URL } from "../../config/config";

const NowPayment = ({
  currency,
  product,
  price,
  totalAmount,
  action,
  nowpaymentKey
}) => {

  localStorage.setItem("nowpaymentKey",nowpaymentKey);  
  const [paymentInvoiceStatus, setPaymentInvoiceStatus] = useState(false);
  const [paynowButton, setPaynowButton] = useState(false);
  const [paymentId, setPaymentId] = useState(null);

  useEffect(()=>{
    setPaymentInvoiceStatus(false);
    setPaynowButton(false);
  },[]);

  const handleInvoiceCreate = () => {
    
    let nowpaymentKey = localStorage.getItem("nowpaymentKey");
    let headers = {
      'x-api-key': nowpaymentKey,
      'Content-Type': 'application/json'
    }
    axios.post('https://api.nowpayments.io/v1/invoice',
    {
        "price_amount": totalAmount,
        "price_currency": currency,
        "order_id": "NETFLOW",
        "order_description": product.name,
        "ipn_callback_url": "https://nowpayments.io",
        "success_url": "https://nowpayments.io",
        "cancel_url": "https://nowpayments.io"
    },
    {
      headers: headers
    }
    ).then((res)=>{
      console.log(res.data);
      setPaymentId(res.data.id);
      window.open(res.data.invoice_url);

      setPaynowButton(true);
      setPaymentInvoiceStatus(true);
    }).catch((err)=>{
      console.log(err);
    })

  }

  const confirmPaymentStatus = () => [
    
  ]


return (
  // {
  //   "price_amount": 1000,
  //   "price_currency": "usd",
  //   "order_id": "RGDBP-21314",
  //   "order_description": "Apple Macbook Pro 2019 x 1",
  //   "ipn_callback_url": "https://nowpayments.io",
  //   "success_url": "https://nowpayments.io",
  //   "cancel_url": "https://nowpayments.io"
  // }

  <>
    {/* {console.log(product)} */}
    <table className="w-100 mb-5 table-bordered">
      <tr>
        <th>order id</th>
        <td>{totalAmount}</td>
      </tr>
      <tr>
        <th>Product Name</th>
        <td>{product.name}</td>
      </tr>
      <tr>
        <th>Currency</th>
        <td>{currency}</td>
      </tr>
      <tr>
        <th>Price</th>
        <td>{price}</td>
      </tr>
    </table>
    {paynowButton?<div className="w-100 text-center p-4">
      Please first pay payment and then confirm.
    </div>:<></>}
    <div className="text-center d-flex gap-2 justify-content-center flex-wrap">
      <Button  variant="contained" disabled={paynowButton} className="btn text-white rounded-3 bg-color-info" onClick={e=>{handleInvoiceCreate()}}>Pay Now</Button>
      {paymentInvoiceStatus?<Button  variant="contained" className="btn text-white rounded-3 bg-color-info" onClick={e=>{handleInvoiceCreate()}}>Confirm</Button>:<></>}
    </div>
  </>)
};
export default NowPayment;
