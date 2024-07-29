import React from "react";
import { ApiHook } from "../../hooks/apiHook";
import { useNavigate, useParams } from "react-router";
import { useState } from "react";
import { useTranslation } from "react-i18next";

const News = (newsId) => {
  const { t } = useTranslation();
  const navigate = useNavigate();
  const params = useParams();
  const [articleId, setArticleId] = useState(params?.newsId);

  const article = ApiHook.CallGetNewsById(articleId);
  const news = ApiHook.CallGetNews();
  const handleNewsExpand = (newsId) => {
    setArticleId(newsId);
    navigate(`/news/${newsId}`);
  };

  const hanldeBack = () => {
    navigate(`/news`);
  };
  return (
    <>
      <div className="page_head_top">News</div>
      <div className="newsMainBg">
        {news?.data?.length > 0 ? (
          <>
            {params?.newsId ? (
              <>
                <button
                  type="button"
                  className="btn btn-dark text-white float-end rounded-3"
                  onClick={hanldeBack}
                >
                  {t("back")}
                </button>
                <div className="newsSubBg">
                  <div className="row">
                    <div className="col-md-4">
                      <img src={article?.data?.image} alt="" />
                    </div>
                    <div className="col-md-8">
                      <h4>{article?.data?.title}</h4>
                      <p>{article?.data?.description}</p>
                    </div>
                  </div>
                </div>
              </>
            ) : (
              <div className="row">
                {news?.data?.map((item) => (
                  <div className="col-md-4" key={item.id}>
                    <div
                      className="newsSubBg"
                      onClick={() => handleNewsExpand(item?.id)}
                    >
                      <div className="row">
                        <div className="col-md-4">
                          <img src={item?.image} alt="" />
                        </div>
                        <div className="col-md-8">
                          <h4>{item?.title}</h4>
                          <p>{item?.description}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                ))}
              </div>
            )}
          </>
        ) : (
          <>
            <img src="images/news-no-data.png" alt=""/>
            <div>Sorry no data found</div>
          </>
        )}
      </div>
    </>
  );
};

export default News;
