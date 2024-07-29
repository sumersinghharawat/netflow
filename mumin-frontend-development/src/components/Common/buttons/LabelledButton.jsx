import React from "react";

const LabelledButton = ({ text, className, click }) => {
  return (
    <>
      <button
        type="button"
        className="btn btn-labeled btn-primary"
        onClick={click}
      >
        <span className="btn-label">
          <i className={className}></i>
        </span>
        {text}
      </button>
    </>
  );
};

export default LabelledButton;
