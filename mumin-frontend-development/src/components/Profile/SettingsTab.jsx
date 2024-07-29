import React  from "react";
import { useTranslation } from "react-i18next";


const SettingsTab = ({ isEditModeEnabled, toggleEditMode }) => {
    const { t } = useTranslation();
    const settingsDetails = [
        { id: 3, type: 'select', label: 'Language', options: [{ label: 'English' }, { label: 'Spanish' }, { label: 'German' }] },
        { id: 3, type: 'select', label: 'Currency', options: [{ label: 'Rupee' }, { label: 'Dollar' }, { label: 'Euro' }] },
        { id: 3, type: 'select', label: 'Binary position Lock', options: [{ label: 'Left' }, { label: 'Right' }, { label: 'Both' }] },
    ]
    return (
        <div id="fifthTab" className="tabcontent">
            <div className={`editSec ${isEditModeEnabled ? "disabled" : ""}`}>
                <div className="editBg">
                    <span style={{ textDecoration: "none", cursor: "pointer" }} onClick={toggleEditMode}>
                        <i className="fa-solid fa-pen-to-square" style={{ color: "#32009c" }}></i>
                    </span>
                </div>
            </div>            <h3>Settings</h3>
            <div className="tabcontent_form_section">
                {settingsDetails.map((input, index) => (
                    <div className="mb-3 row tabBlockClass" key={index}>
                        <label htmlFor={input.id} className="col-sm-3 col-form-label labelWidthClass">{input.label}:</label>
                        <div className="col-md-9 col-sm-12 col-12">
                            <select className="form-select" id={input.id} disabled={!isEditModeEnabled} >
                                {input.options.map((option, optionIndex) => (
                                    <option key={optionIndex}>{option.label}</option>
                                ))}
                            </select>
                        </div>
                    </div>
                ))}
                <div className={`paymenytLinkBtn ${isEditModeEnabled ? "disabled" : ""}`}>
                    <button type="button" className="btn" disabled={!isEditModeEnabled} >{t('update')}</button>
                </div>
            </div>
        </div>
    )
}

export default SettingsTab