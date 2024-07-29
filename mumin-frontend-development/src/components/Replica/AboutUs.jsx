import React from "react";

const AboutUs = ({ data }) => {
  return (
    <div className="about__two section-padding" id="aboutus">
      <img
        className="about__two-shape-one"
        src={"/img/shape/about-1.png"}
        alt="about-shape"
      />
      <img
        className="about__two-shape-two dark-n"
        src={"/img/shape/about-2.png"}
        alt="about-shape"
      />
      {data && <div dangerouslySetInnerHTML={{ __html: data }}></div>}
    </div>
  );
};

export default AboutUs;
