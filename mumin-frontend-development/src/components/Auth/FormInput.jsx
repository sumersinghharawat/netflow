import React from "react";


const FormInput = ({ type, id, name, placeholder, onChange, value }) => (
  <div className={`${name}Input`}>
    <input type={type} id={id} name={name} placeholder={placeholder} onChange={onChange} value={value} required/>
  </div>
);

export default FormInput
