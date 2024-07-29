<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EpinTransferExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'From User',
            'To User',
            'E-Pin',
            'Transfer Date',
        ];
    }
    protected $reportData;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $reportData = $this->reportData;
        // dd($reportData);
        $row = collect([]);
        $currency = currencySymbol();
        foreach ($reportData as $details) {
            $data['From User'] = $details->fromUser->userDetails->name . '' . $details->fromUser->userDetails->second_name . '' . '(' . $details->fromUser->username . ')';
             $data['To User'] = $details->toUser->userDetails->name . '' . $details->toUser->userDetails->second_name . '' . '(' . $details->toUser->username . ')';
            $data['E-Pin'] = $details->epin->numbers;
            $data['Date'] = $details->updated_at;
            // $data['Status'] = ($details->type == 'released' ? 'Paid' : $details->type);
            $row->push($data);
        }
        return $row;
    }
}
