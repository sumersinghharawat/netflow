import React from "react";

const ChooseUs = ({ data }) => {
  return (
    <div className="benefits__area section-padding">
      <div className="container">
        {data && <div dangerouslySetInnerHTML={{ __html: data }}></div>}
      </div>
    </div>
  );
};

export default ChooseUs;
