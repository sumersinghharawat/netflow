import { useEffect, useState } from "react";
import axios from "axios";
import CurrencyConverter from "../../Currency/CurrencyConverter";
import Alert from "@mui/material/Alert";
import { Button, FormHelperText, MenuItem, Select } from "@mui/material";
import { BASE_URL, NOWPAYMENT_EMAIL, NOWPAYMENT_PASSWORD } from "../../config/config";
import { ToastContainer, toast } from 'react-toastify';
// import 'react-toastify/dist/ReactToastify.css';

const NowPayment = ({
  currency,
  product,
  price,
  totalAmount,
  action,
  nowpaymentKey,
  handleSubmitFinish,
  email,
  paymentMethodId
}) => {

  localStorage.setItem("nowpaymentKey",nowpaymentKey);  
  const [paymentInvoiceStatus, setPaymentInvoiceStatus] = useState(false);
  const [paymentLinkStatus, setPaymentLinkStatus] = useState(false);
  const [paynowButton, setPaynowButton] = useState(false);
  const [paymentLink, setPaymentLink] = useState(null);
  const [paymentId, setPaymentId] = useState(null);
  const [currencyList, setCurrencyList] = useState([]);
  const [selectedCoin, setSelectedCoin] = useState("Select Coin");
  const [invoiceId, setInvoiceId] = useState("");
  const [statusOfPayment, setStatusOfPayment] = useState("");
  const [helperText, setHelperText] = useState('Please choose a coin');
  const [error, setError] = useState(false);
  

  useEffect(()=>{
    setPaymentInvoiceStatus(false);
    setPaynowButton(false);
    setCurrencyList([]);
    setPaymentId("");
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
      setInvoiceId(res.data.id);
      setInvoiceId(res.data.id);

      // createPaymentByInvoice();
      getAvailableCurrencyList();
      // window.open(res.data.invoice_url);

      // setPaynowButton(true);
      setPaymentInvoiceStatus(true);
    }).catch((err)=>{
      console.log(err);
    })
  }
  const getAvailableCurrencyList = () => {
    
    let nowpaymentKey = localStorage.getItem("nowpaymentKey");
    let headers = {
      'x-api-key': nowpaymentKey,
      'Content-Type': 'application/json'
    }
    axios.get('https://api.nowpayments.io/v1/merchant/coins',
    {
      headers: headers
    }
    ).then((res)=>{
      setCurrencyList(res.data.selectedCurrencies);
    }).catch((err)=>{
      console.log(err);
    })
  }
  const handlePaymentByInvoiceCreate = () => {

    if(selectedCoin == "Select Coin"){
        setHelperText('Sorry, Please select a coin!');
        setError(true);
    }

    let nowpaymentKey = localStorage.getItem("nowpaymentKey");

    let headers = {
      'x-api-key': nowpaymentKey,
      'Content-Type': 'application/json'
    }
    axios.post('https://api.nowpayments.io/v1/invoice-payment',
    {
      "iid": invoiceId,
      "pay_currency": selectedCoin,
      "order_description": product.name,
      "customer_email": email,
    },
    {
      headers: headers
    }
    ).then((res)=>{
      console.log(res.data.payment_id);
      localStorage.setItem("payment_id",res.data.payment_id);
      setPaymentId(res.data.payment_id);

      console.log("payment id", paymentId);
      
      setPaymentLink("https://nowpayments.io/payment?iid="+invoiceId+"&paymentId="+res.data.payment_id);
      setPaymentInvoiceStatus(true);
      setPaymentLinkStatus(true);

    }).catch((err)=>{
      console.log(err);
    })
  }

  const confirmPaymentStatus = async () => {
    try {
      const nowpaymentKey = localStorage.getItem("nowpaymentKey");

      const headers = {
        'x-api-key': nowpaymentKey,
        'Content-Type': 'application/json'
      };

      let payment_id = localStorage.getItem("payment_id");
      if(payment_id){
        const response = await axios.get(`https://api.nowpayments.io/v1/payment/${payment_id}`, { headers });
        setStatusOfPayment(response.data.payment_status);

        if(response.data.payment_status != "waiting"){
          handleSubmitFinish(paymentMethodId);
        }
        setTimeout(()=>{
          setStatusOfPayment("");
        }, 3000)
      }else{
        console.log("response",  payment_id);
      }
      
    } catch (error) {
      console.error("Error confirming payment status:", error.response ? error.response.data : error.message);
    }
  };

return (
  <>
    {/* {console.log(product)} */}
    <table className="w-100 mb-5 table-bordered">
      <tr>
        <th>order id</th>
        <td>{"NETFLOW"}</td>
      </tr>
      <tr>
        <th>Product Name</th>
        <td>{product.name}</td>
      </tr>
      <tr>
        <th>Currency</th>
        <td>{currency}{console.log(currency)}</td>
      </tr>
      <tr>
        <th>Price</th>
        <td>{price}</td>
      </tr>
    </table>
    {!paymentLinkStatus?<>{paynowButton?<div className="w-100 text-center p-4">
      Please first pay payment and then confirm.

    </div>:<></>}
    <div className="mb-2">
    {currencyList.length!=0?<div>
        <Select value={selectedCoin} onChange={e=>{setSelectedCoin(e.target.value)}} fullWidth={true}>
        <MenuItem value={"Select Coin"} key={0}>Select Coin</MenuItem>
          {Object.keys(currencyList).map((coin) => (
            <MenuItem key={coin+1} value={currencyList[coin]}>
              {currencyList[coin]}
            </MenuItem>
          ))}
        </Select>
        {error?<FormHelperText>{helperText}</FormHelperText>:<></>}
      </div>:<></>}
    </div>
    <div className="text-center d-flex gap-2 justify-content-center flex-wrap">
      {!paynowButton?<Button  variant="contained" disabled={paynowButton} className="btn text-white rounded-3 bg-color-info" onClick={e=>{handleInvoiceCreate()}}>Pay Now</Button>:<></>}
      {paymentInvoiceStatus?<Button  variant="contained" className="btn text-white rounded-3 bg-color-info" onClick={e=>{handlePaymentByInvoiceCreate()}}>Generate Payment Link</Button>:<></>}
    </div></>:<><div className="mb-4">{statusOfPayment=="waiting"?<Alert severity="warning">{statusOfPayment}</Alert>:(statusOfPayment?<Alert severity="success">{statusOfPayment}</Alert>:<></>)}</div><div className="text-center d-flex gap-2 justify-content-center flex-wrap">
      <Button variant="contained" target="_blank" href={paymentLink} className="btn text-white rounded-3 bg-color-info">Go to Pay Page</Button>
      <Button variant="contained" className="btn text-white rounded-3 bg-color-info" onClick={(e)=>confirmPaymentStatus()}>Confirm</Button>
      </div></>}
  </>)
};
export default NowPayment;
