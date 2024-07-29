<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Invoice Number',
            'Member Name',
            'Total Amount',
            'Payment Method',
            'Purchase Date'
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

    public function collection()
    {
        $reportData = $this->reportData;
        $row = collect([]);
        $currency = currencySymbol();
        foreach ($reportData as $details) {
            $data['Invoice Number'] = $details->invoice_no;
            $data['Member Name'] = $details->user->userDetails->name . '' . $details->user->userDetails->second_name . '' . '(' . $details->user->username . ')';

            $data['Total Amount'] = $currency . " " . formatCurrency($details->total_amount ?? 0) ?? 0;
            $data['Payment Method'] = ($details->paymentMethod->slug == 'free-joining' ? 'Free Purchase' : $details->paymentMethod->name);
            $data['Purchase Date'] = $details->order_date;
            $row->push($data);
        }


        return $row;
    }
}
