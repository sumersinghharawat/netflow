import React, { useState } from "react";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";
import SubmitButton from "../../components/Common/buttons/SubmitButton";
import { useQueryClient } from "@tanstack/react-query";
import { toast } from "react-toastify";
import { Slide } from "react-slideshow-image";
import "react-slideshow-image/dist/styles.css";

const ReplicaSite = () => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const replicaFlag = ApiHook.CallGetReplicaBanner();
  const Upload = ApiHook.CallUploadReplicaBanner(File);
  const deleteBannerMutation = ApiHook.CallDeleteReplicaBanner();
  const [fileResponse, setFileResponse] = useState({
    success: null,
    error: null,
  });
  const [file, setFile] = useState([]);
  const [sliderKey, setSliderKey] = useState(0);
  const handleFileChange = (event) => {
    setFileResponse({
      success: null,
      error: null,
    });
    const selectedFile = event.target.files;
    if (selectedFile.length > 2) {
      toast.error(t("maxFileLimitExceeded"));
    } else {
      setFile(selectedFile);
    }
  };
  const handleUpload = () => {
    if (file) {
      Upload.mutate(file, {
        onSuccess: (res) => {
          if (res.status) {
            toast.success(res?.data?.message);
            setFile([]);
            queryClient.invalidateQueries({ queryKey: ["get-replica-banner"] });
            setSliderKey((prevKey) => prevKey + 1);
            document.getElementById("fileUpload").value = "";
          } else {
            toast.error(t(res?.data?.description));
          }
        },
      });
    }
  };
  const deleteBanner = (banner) => {
    deleteBannerMutation.mutateAsync(
      { id: banner.id },
      {
        onSuccess: (res) => {
          if (res?.data?.status) {
            toast.success(res?.data?.data?.message);
            queryClient.invalidateQueries({ queryKey: ["get-replica-banner"] });
            setSliderKey((prevKey) => prevKey + 1);
          }
        },
      }
    );
  };
  const images = replicaFlag?.data?.map((item) => item.image) || [];
  return (
    <>
      <div className="page_head_top">{t("replicaSite")}</div>
      <div className="uploadMainBg">
        <div className="container-banner">
          <h3>{t("currentTopBanner")}</h3>
          <div className="uploadSubBg">
            {images.length > 0 ? (
              <Slide key={sliderKey}>
                {images.map((item, key) => (
                  <>
                    <button
                      className="checkout_address_btn"
                      disabled = {deleteBannerMutation.status === "loading"}
                      onClick={() => deleteBanner(replicaFlag?.data[key])}
                    >
                      <i className="fa fa-trash"></i>
                    </button>
                    <div className="each-slide-effect" key={key}>
                      <img
                        style={{ height: "280px", width: "390px" }}
                        src={item}
                        alt={`Slide ${key + 1}`}
                      />
                    </div>
                  </>
                ))}
              </Slide>
            ) : (
              <p>{t("noImagesAvailable")}</p>
            )}
          </div>
        </div>
        <div className="container-banner">
          <h3>{t("uploadTopBanner")}</h3>
          <div className="uploadSubBg">
            <input
              id="fileUpload"
              type="file"
              placeholder=""
              className="form-control"
              name="fileUpload"
              onChange={handleFileChange}
              multiple
            />
            {fileResponse?.success && (
              <div style={{ color: "green" }}>{t(fileResponse?.success)}</div>
            )}
            {fileResponse?.error && (
              <div style={{ color: "red" }}>{t(fileResponse?.error)}</div>
            )}
            <img src="images/upload.png" alt="" />
            <h6>{t("choose_an_image_file_or_drag_it_here")}</h6>
            <p>{t("please_choose_a_Png/Jpeg/Jpg_file")}</p>
            <p>{t("max_size_2MB")}</p>
            <p>{t("resolution_recommeded_1920x1080")}</p>
            <div style={{ textAlign: "center" }}>
              <SubmitButton
                className="upload_btn"
                click={handleUpload}
                text={Upload?.isLoading ? "Uploading..." : t("upload")}
                isLoading={Upload.status === "loading"}
              />
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default ReplicaSite;
