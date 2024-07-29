import React from "react";
import TicketHeader from "./TicketHeader";
import TicketForm from "./TicketForm";
import { ApiHook } from "../../hooks/apiHook";

const CreateTicket = () => {

  const partials = ApiHook.CallTicketPartials()
  const trackId = ApiHook.CallTrackId();


  return (
    <>
      <TicketHeader />
      <TicketForm partials={partials.data} trackId={trackId.data}/>
    </>
  );
};

export default CreateTicket
