import React from "react";

const NavButton = ({ disabled, click, text, className }) => {
  return (
    <>
      <button className={className} onClick={click} disabled={disabled}>
        {text}
      </button>
    </>
  );
};

export default NavButton;
