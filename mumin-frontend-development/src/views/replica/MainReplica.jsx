import React from "react";
import AboutUs from "../../components/Replica/AboutUs";
import ReplicaBanners from "../../components/Replica/Banner";
import ChooseUs from "../../components/Replica/ChooseUs";
import ContactUs from "../../components/Replica/ContactUs";
import CustomCount from "../../components/Replica/CustomCounts";
import Enquiry from "../../components/Replica/Enquiry";
import Features from "../../components/Replica/Features";
import Services from "../../components/Replica/Services";
import { ApiHook } from "../../hooks/apiHook";

const MainReplica = () => {
  const replica = ApiHook.CallReplicaHome();
  if (replica.isLoading) {
    return (
      <div className="theme-loader">
        <div className="spinner">
          <div className="spinner-bounce one"></div>
          <div className="spinner-bounce two"></div>
          <div className="spinner-bounce three"></div>
        </div>
      </div>
    );
  }
  return (
    <>
      <ReplicaBanners data={replica?.data?.data?.replicaHome?.replicaBanners} />
      <Features data={replica?.data?.data?.replicaHome?.features} />
      <AboutUs data={replica?.data?.data?.replicaHome?.aboutUs} />
      <Services data={replica?.data?.data?.replicaHome?.services} />
      <CustomCount />
      <ChooseUs data={replica?.data?.data?.replicaHome?.chooseUs} />
      <Enquiry />
      <ContactUs companyDetails={replica?.data?.data?.companyDetails} />
    </>
  );
};

export default MainReplica;
