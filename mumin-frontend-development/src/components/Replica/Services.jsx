import React from "react";

const Services = ({ data }) => {
  return (
    <div className="services__one section-padding" id="services">
      <div className="shape-slide">
        <div className="sliders scroll">
          <img src="/img/shape/services-1.png" alt="service-shape" />
        </div>
        <div className="sliders scroll">
          <img src="/img/shape/services-1.png" alt="service-shape" />
        </div>
      </div>
      <div className="container">
        <div className="row mb-30 align-items-end">
          <div className="col-xl-9 col-lg-9 lg-mb-30">
            <div className="services__one-title lg-t-center">
              <h2>Our Plan</h2>
            </div>
          </div>
        </div>
        {data && <div dangerouslySetInnerHTML={{ __html: data }}></div>}
      </div>
    </div>
  );
};

export default Services;
