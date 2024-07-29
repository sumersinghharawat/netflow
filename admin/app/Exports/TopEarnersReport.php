<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TopEarnersReport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $reportData;

    public function headings(): array
    {
        return [
            'Name',
            'Total Earnings',
            'Ewallet balances',
        ];
    }

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function collection()
    {
        $reportData = $this->reportData;
        $row = collect([]);
        foreach ($reportData as $details) {
            $data['name'] = $details->userDetails->name . '' . $details->userDetails->second_name . '' . '(' . $details->username . ')';

            $data['total_earnigs'] =  formatCurrency($details->legamtDetails->sum('total_amount')) ?? 0;
            $data['ewallet_balance'] =  formatCurrency($details->userBalance->balance_amount) ?? 0;
            $row->push($data);
        }

        return $row;
    }
}
