import React from "react";
import CrmTiles from "./crmTiles";
import { ApiHook } from "../../hooks/apiHook";

const CrmTilesSegment = ({tiles}) => {
  //----------------------------------------- API ---------------------------------------

  
  return (
    <div className="crm-con-sec">
      <div className="row">
        <CrmTiles
          text={"total_ongoing_leads"}
          count={tiles.data?.crmTiles?.ongoingLeads ?? 0}
        />
        <CrmTiles
          text={"total_accepted_leads"}
          count={tiles.data?.crmTiles?.acceptedLeads ?? 0}
        />
        <CrmTiles
          text={"total_rejected_leads"}
          count={tiles.data?.crmTiles?.rejectedLeads ?? 0}
        />
      </div>
    </div>
  );
};

export default CrmTilesSegment;
