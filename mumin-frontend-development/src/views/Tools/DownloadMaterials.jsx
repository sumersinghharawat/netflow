import DownloadMaterialsComponent from "../../components/Tools/DownloadMaterialsComponent";
import { ApiHook } from "../../hooks/apiHook";


const DownloadMaterials = () => {
    const DownloadMaterials = ApiHook.CallGetDownloadMaterials()
    return (
        <>
            <div className="page_head_top">Download Materials</div>
            <DownloadMaterialsComponent materials={DownloadMaterials?.data}/>
        </>
    )
};
export default DownloadMaterials;