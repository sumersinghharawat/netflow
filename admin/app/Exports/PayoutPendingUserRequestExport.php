<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayoutPendingUserRequestExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Member Name',
            'Total Amount',
            'Date'
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
        $row = collect([]);
        $currency = currencySymbol();
        foreach ($reportData as $details) {
            $data['Member Name'] = $details->user->userDetails->name . '' . $details->user->userDetails->second_name . '' . '(' . $details->user->username . ')';
            $data['Total Amount'] = $currency . " " . formatCurrency($details->balance_amount ?? 0) ?? 0;
            $data['Date'] = $details->created_at;
            $row->push($data);
        }

        return $row;
    }
}
