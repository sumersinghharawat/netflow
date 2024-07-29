import React from "react";

const Enquiry = () => {
  return (
    <div
      className="request__quote"
      style={{ backgroundImage: "url(/img/pages/request-quote.jpg)" }}
    >
      <div className="container">
        <div className="row align-items-center">
          <div className="col-xl-9 col-lg-9 col-md-8 md-mb-30">
            <div className="request__quote-title">
              <h2>Enquire Now for more details</h2>
              <div className="request__quote-title-btn">
                <a className="btn-one" href="#contact">
                  Contact Us!
                </a>
                <img
                  className="left-right-animate"
                  src="/img/icon/arrow.png"
                  alt="quote-icon"
                />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Enquiry;
