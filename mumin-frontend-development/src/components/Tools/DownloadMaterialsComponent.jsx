import React from "react";

const DownloadMaterialsComponent = ({ materials }) => {
  const handleDownload = (material) => {
    // Create an anchor element
    const downloadLink = document.createElement("a");
    downloadLink.href = material?.fileName; // Replace with the actual file URL
    downloadLink.download = "file1name.extension"; // Replace with the desired file name and extension
    downloadLink.target = "_blank"; // Open in a new tab or window

    // Trigger a click event on the anchor link
    downloadLink.click();

    // Clean up the anchor element
    downloadLink.remove();
  };

  return (
    <div className="newsMainBg">
      {materials?.data?.length > 0 ? (
        <>
          <div className="row">
            {materials?.data?.map((item) => (
              <div className="col-md-4">
                <div className="newsSubBg" onClick={() => handleDownload(item)}>
                  <div className="row">
                    <div className="col-md-4">
                      {item?.catId === 1 && (
                        <img src="/images/pdf_icon.png" alt="" />
                      )}
                      {item?.catId === 2 && (
                        <img src="/images/image_icon.png" alt="" />
                      )}
                      {item?.catId === 3 && (
                        <img src="/images/video_icon.png" alt="" />
                      )}
                    </div>
                    <div className="col-md-8">
                      <h4>{item?.fileTitle}</h4>
                      <p>{item?.fileDescription}</p>
                      <i className="fa fa-download" />
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </>
      ) : (
        <>
          <img src="images/download-no-data.png" alt="" />
          <div>Sorry no data found</div>
        </>
      )}
    </div>
  );
};

export default DownloadMaterialsComponent;
