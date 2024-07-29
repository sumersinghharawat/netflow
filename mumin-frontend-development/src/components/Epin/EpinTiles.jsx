import React from "react";
import { useTranslation } from "react-i18next";
import CurrencyConverter from "../../Currency/CurrencyConverter";

const EpinTiles = ({data, conversionFactor, currency}) => {
    const { t } = useTranslation()

    return (
        <div className="ewallet_top_cnt_sction">
            <div className="row">
                <div className="col-md-3 pe-0">
                    <div className="ewallet_top_qc_balance_box">
                        <div className="row align-items-center">
                            <div className="ewallet_top_qc_balance_box_cnt">
                                <div className="ewallet_top_qc_balance_box_cnt_ico">
                                    <img src={'/images/ewallet-ico1.png'} alt="" />
                                </div>
                                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                                    <div className="ewallet_top_qc_balance_box_cnt_head">
                                        {t('activeEpinCount')}
                                    </div>
                                    <div className="box_amnt_dv">
                                        <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">
                                            {data?.epinCount}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-md-3 pe-0">
                    <div className="ewallet_top_qc_balance_box">
                        <div className="row align-items-center">
                            <div className="ewallet_top_qc_balance_box_cnt">
                                <div className="ewallet_top_qc_balance_box_cnt_ico">
                                    <img src={'/images/ewallet-ico2.png'} alt="" />
                                </div>
                                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                                    <div className="ewallet_top_qc_balance_box_cnt_head">
                                        {t('activeEpinBalance')}
                                    </div>
                                    <div className="box_amnt_dv">
                                        <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">
                                            {`${currency.symbolLeft} ${CurrencyConverter(data?.epinBalance,conversionFactor)}`}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-md-3 pe-0">
                    <div className="ewallet_top_qc_balance_box">
                        <div className="row align-items-center">
                            <div className="ewallet_top_qc_balance_box_cnt">
                                <div className="ewallet_top_qc_balance_box_cnt_ico">
                                    <img src={'/images/epin-pending-req.png'} alt="" />
                                </div>
                                <div className="ewallet_top_qc_balance_box_cnt_cnt">
                                    <div className="ewallet_top_qc_balance_box_cnt_head">
                                        {t('pendingEpinRequest')}
                                    </div>
                                    <div className="box_amnt_dv">
                                        <div className="ewallet_top_qc_balance_box_cnt_val ewallet_top_vl">
                                            {data?.pendingRequestCount}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
};

export default EpinTiles;
