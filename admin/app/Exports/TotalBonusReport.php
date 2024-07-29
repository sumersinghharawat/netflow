<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TotalBonusReport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $reportData;

    public function headings(): array
    {
        return [
            'MemberName',
            'Total Amount',
            'Tds',
            'Service Charge',
            'Amount Payable',
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
        foreach ($reportData as $user) {
            $data['name'] = $user->userDetails->name . '' . $user->userDetails->second_name . '' . '(' . $user->username . ')' ?? 'NA';

            $data['total_amount'] = $user->total_amount ?? '0';
            $data['tds'] = $user->tds ?? '0';
            $data['service_charge'] = $user->service_charge ?? '0';
            $data['amount_payable'] = $user->amount_payable ?? '0';
            $row->push($data);
        }

        return $row;
    }
}
