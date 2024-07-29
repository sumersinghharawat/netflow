<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CommissionReport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $reportData;

    public function headings(): array
    {
        return [
            'MemberName',
            'Type',
            'Amount',
            'Tax',
            'Service Charge',
            'Amount Payable',
            'Date',
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
        foreach ($reportData as $commissionDetails) {
            $data['name'] = $commissionDetails->user->userDetails->name . '' . $commissionDetails->user->userDetails->second_name;
            $data['type'] = str_replace('_', ' ', ucfirst($commissionDetails->amount_type ?? 'Na'));
            $data['amount'] = formatCurrency($commissionDetails->total_amount) ?? 0;
            $data['tds'] = formatCurrency($commissionDetails->tds) ?? 0;
            $data['service_charge'] = formatCurrency($commissionDetails->service_charge) ?? 0;
            $data['amount_payable'] = formatCurrency($commissionDetails->amount_payable) ?? 0;
            $data['date'] = $commissionDetails->date_of_submission;
            $row->push($data);
        }
        return $row;
    }
}
