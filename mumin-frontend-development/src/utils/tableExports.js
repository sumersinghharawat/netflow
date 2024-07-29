import * as XLSX from "xlsx";
import Papa from "papaparse";

export const exportToExcel = async (data, headers, type) => {
    const wsData = data.map(obj => Object.values(obj))
    const ws = XLSX.utils.aoa_to_sheet([headers, ...wsData]);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "data");
    XLSX.writeFile(wb, `${type}.xlsx`);
};

export const exportToCSV = async (data, headers, type) => {
    const wsData = data.map(obj => Object.values(obj))
    const csvData = Papa.unparse([headers, ...wsData]);
    const blob = new Blob([csvData], { type: "text/csv" });
    const url = URL.createObjectURL(blob);
    const a = document.createElement("a");
    a.href = url;
    a.download = `${type}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
};