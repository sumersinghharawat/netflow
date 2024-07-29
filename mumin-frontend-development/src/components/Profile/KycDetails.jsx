import React, { useEffect, useRef, useState } from "react";
import { useTranslation } from "react-i18next";
import { ApiHook } from "../../hooks/apiHook";
import { toast } from "react-toastify";
import { useQueryClient } from "@tanstack/react-query";

const KycDetails = () => {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const [selectedCategory, setSelectedCategory] = useState("");
  const [documentFront, setDocumentFront] = useState([]);
  const fileInputRef = useRef(null);

  // ------------------------------------- API -------------------------------------
  const details = ApiHook.CallKycDetails();
  const kycMutation = ApiHook.CallKycUploads();
  const kycFileDeleteMutation = ApiHook.CallDeleteKycFiles();

  // ------------------------------- Functionalities -------------------------------

  const handleCategoryChange = (event) => {
    setSelectedCategory(event.target.value);
  };
  const [userKyc, setUserKyc] = useState(details?.data?.userKyc);

  useEffect(() => {
    setUserKyc(details?.data?.userKyc);
  }, [details]);

  const handleDocumentFrontChange = (event) => {
    const selectedFiles = event.target.files;
    setDocumentFront(selectedFiles);
  };

  const handleUploadClick = () => {
    const files = {
      files: documentFront,
      category: selectedCategory,
      type: "kyc",
    };
    kycMutation.mutate(files, {
      onSuccess: (res) => {
        if (res?.status) {
          queryClient.invalidateQueries({ queryKey: ["kyc-details"] });
          toast.success(t(res?.data));
          if (fileInputRef.current) {
            setSelectedCategory("");
            fileInputRef.current.value = "";
          }
        } else {
          if (res.data.code === 1124) {
            toast.error(t("kycVerified"));
          } else if (res.data.code) {
            toast.error(t(res?.data?.description))
          } else {
            toast.error(res?.message);
          }
          if (fileInputRef.current) {
            fileInputRef.current.value = "";
            setSelectedCategory("");
          }
        }
      },
    });
  };

  const deleteRejectedFile = (file) => {
    const files = [file];
    kycFileDeleteMutation.mutate(files);
  };

  const pendingFiles = (files) => {
    const filesId = [];
    files.forEach((image) => {
      if (image.status === 2) {
        filesId.push(image.id);
      }
    });
    return filesId;
  };

  const deletePendingFiles = (files) => {
    const filesId = pendingFiles(files);
    kycFileDeleteMutation.mutate(filesId);
  };

  return (
    <>
      <div className="page_head_top">{t("kyc_details")}</div>
      <div className="kyc_bg">
        <div className="row">
          <div className="col-md-3">
            <label htmlFor="category">{t("select_category")}</label>
            <select
              className="form-control"
              name="category"
              id="category"
              value={selectedCategory}
              onChange={handleCategoryChange}
            >
              <option key={"first"}>{t("choose_the_category")}</option>
              {details?.data?.kycCategory.map((item, key) => (
                <option key={key} value={item.id}>
                  {item?.category}
                </option>
              ))}
            </select>
          </div>
          <div className="col-md-2">
            <label htmlFor="documentFront">{t("document_both_side")}</label>
            <input
              ref={fileInputRef}
              className="form-control"
              type="file"
              name="documentFront"
              id="documentFront"
              accept="image/jpeg, image/png, image/jpg"
              multiple
              onChange={handleDocumentFrontChange}
            />
            <p className="info_txt">({t('allowedTypes')})</p>
          </div>
          <div className="col-md-3 mt-3 pt-2">
            <button
              className="btn btn-primary"
              onClick={handleUploadClick}
              disabled={kycMutation.isLoading}
            >
              {t("upload")}
            </button>
          </div>
        </div>
      </div>
      <div className="kyc_bg mt-2">
        <div className="table-responsive min-hieght-table">
          <table className="striped table_scroll">
            <thead>
              <tr>
                <th>{t("slno")}</th>
                <th>{t("document_name")}</th>
                <th>{t("status")}</th>
                <th>{t("document_files")}</th>
                <th>{t("action")}</th>
              </tr>
            </thead>
            <tbody>
              {userKyc?.length > 0 ? (
                userKyc?.map((item, index) => (
                  <tr key={index}>
                    <td>{index + 1}</td>
                    <td>{item.category}</td>
                    <td>
                      {item.status === "approved" && (
                        <span style={{ color: "green" }}>{t(item.status)}</span>
                      )}
                      {item.status === "pending" && (
                        <span className="bg-warning">{t(item.status)}</span>
                      )}
                      {item.status === "rejected" && (
                        <span style={{ color: "red" }}>{t(item.status)}</span>
                      )}
                    </td>
                    <td>
                      <div className="image_view_sec">
                        {item?.files.map((image, i) => (
                          <div className="image_view" key={i}>
                            <img src={image?.file} alt="" />
                            {image?.status === 1 ? <></> : null}
                          </div>
                        ))}
                        {item?.rejectedFiles.map((image, i) => (
                          <div className="image_view" key={i}>
                            <a data-bs-toggle="modal" href="#kycimageview">
                              <img src={image?.file} alt="" />
                            </a>
                            <div
                              className="upload_error"
                              onClick={() => deleteRejectedFile(image?.id)}
                            >
                              <i className="fa fa-close"></i>
                            </div>
                          </div>
                        ))}
                      </div>
                    </td>
                    <td>
                      {item.status === "pending" && (
                        <a
                          href="#"
                          className="action_btn"
                          onClick={() => deletePendingFiles(item?.files)}
                        >
                          <i className="fa fa-trash"></i>
                        </a>
                      )}
                    </td>
                  </tr>
                ))
              ) : (
                <tr>
                  <td colSpan="12">
                    <div className="nodata-table-view">
                      <div className="nodata-table-view-box">
                        <div className="nodata-table-view-box-img">
                          <img src="/images/no-data-image1.jpg" alt="" />
                        </div>
                        <div className="nodata-table-view-box-txt">
                          {t("sorry_no_data_found")}
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
};

export default KycDetails;
