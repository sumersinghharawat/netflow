<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SubscriptionReportExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'Member Name',
            'Package',
            'Subscription Amount',
            'Payment Method',
            'Subscription Date'
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
            $data['Member Name'] = $details->user->userDetails->name . '' . $details->user->userDetails->second_name . '' . '(' . $details->user->username . ')';
            $data['package'] =  $details->package->name ?? $details->package->model ?? 'NA' . '(' . $currency .  formatCurrency($details->price ?? 0) . ')';
            $data['Total Amount'] = $currency . " " . formatCurrency($details->total_amount ?? 0) ?? 0;
            $data['Payment Method'] = ($details->payment_type == 'free joining' ? 'Free Subscription' : $details->payment_type);
            $data['Subscription Date'] = $details->created_at;
            $row->push($data);
        }


        return $row;
    }
}
