<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActiveDeactiveExcel implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $reportData;

    public function headings(): array
    {
        return [
            'Invoice Number',
            'MemberName',
            'Status',
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
        foreach ($reportData as $item) {
            $data['Invoice Number'] = $item->salesOrder->invoice_no ?? $item->ocOrder->invoice_prefix. '' .$item->ocOrder->order_id;
            $data['Member Name'] = $item->userDetails->name . '' . $item->userDetails->second_name;
            $data['Status'] = ($item->active ? 'Active' : 'Inactive');
            $data['Date'] = $item->date_of_joining;
            $row->push($data);
        }

        return $row;
    }
}
