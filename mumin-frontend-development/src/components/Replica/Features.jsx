import React, { useEffect } from "react";

const Features = ({ data }) => {
  useEffect(() => {
    // Function to handle mouse enter
    const handleMouseEnter = (event) => {
      const elements = document.querySelectorAll(".features-area-item");

      elements.forEach((element) => {
        element.classList.remove("features-area-item-hover");
      });

      event.currentTarget.classList.add("features-area-item-hover");
    };

    // Add event listeners to all .features-area-item elements
    const items = document.querySelectorAll(".features-area-item");
    items.forEach((item) => {
      item.addEventListener("mouseenter", handleMouseEnter);
    });

    // Cleanup: Remove event listeners when the component unmounts
    return () => {
      items.forEach((item) => {
        item.removeEventListener("mouseenter", handleMouseEnter);
      });
    };
  }, []);

  return (
    <div className="features">
      <div className="container">
        {data && <div dangerouslySetInnerHTML={{ __html: data }}></div>}
      </div>
    </div>
  );
};

export default Features;
