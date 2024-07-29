import React, { useState } from "react";

const CustomCount = () => {
  const [counts, setCounts] = useState([0, 0, 0, 0]);
  const targetValues = [150, 259, 180, 193];

  const animateCounters = () => {
    const animationDuration = 2500; // Animation duration in milliseconds

    // Calculate increments for each counter
    const increments = targetValues.map((target) =>
      Math.ceil(target / (animationDuration / 10))
    );

    const updateCounts = () => {
      // Use the previous state to update counts
      setCounts((prevCounts) =>
        prevCounts.map((currentCounter, index) => {
          const newCount =
            currentCounter < targetValues[index]
              ? currentCounter + increments[index]
              : targetValues[index];

          return newCount;
        })
      );

      // Check if any counter has not reached its target value
      if (!counts.every((counter, index) => counter === targetValues[index])) {
        requestAnimationFrame(updateCounts);
      }
    };

    updateCounts();

    // Clean up the animation on component unmount
    return () => cancelAnimationFrame(updateCounts);
  };

  return (
    <div className="custom__container" onMouseEnter={animateCounters}>
      <div className="row">
        <div className="col-xl-12">
          <div className="counter__area">
            <div className="shape-slide">
              <div className="sliders scrolls">
                <img src={"/img/shape/counter-bg.png"} alt="counter-shape" />
              </div>
              <div className="sliders scrolls">
                <img src={"/img/shape/counter-bg.png"} alt="counter-shape" />
              </div>
            </div>
            <div className="counter__area-item">
              <div className="counter__area-item-icon">
                <i className="flaticon-review"></i>
              </div>
              <div className="counter__area-item-info">
                <h2>
                  <span className="counter">{counts[0]}</span>K
                </h2>
                <h6>Happy Customer</h6>
              </div>
            </div>
            <div className="counter__area-item">
              <div className="counter__area-item-icon">
                <i className="flaticon-meeting"></i>
              </div>
              <div className="counter__area-item-info">
                <h2>
                  <span className="counter">{counts[1]}</span>+
                </h2>
                <h6>Professional Agent</h6>
              </div>
            </div>
            <div className="counter__area-item">
              <div className="counter__area-item-icon">
                <i className="flaticon-success"></i>
              </div>
              <div className="counter__area-item-info">
                <h2>
                  <span className="counter">{counts[2]}</span>+
                </h2>
                <h6>National Award</h6>
              </div>
            </div>
            <div className="counter__area-item">
              <div className="counter__area-item-icon">
                <i className="flaticon-globe"></i>
              </div>
              <div className="counter__area-item-info">
                <h2>
                  <span className="counter">{counts[3]}</span>+
                </h2>
                <h6>Country Connected</h6>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default CustomCount;
