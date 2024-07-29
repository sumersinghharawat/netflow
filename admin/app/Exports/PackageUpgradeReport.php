<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PackageUpgradeReport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $reportData;

    public function headings(): array
    {
        return [
            'MemberName',
            'Old Package',
            'Upgraded Package',
            'Amount',
            'Payment Method',
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
        foreach ($reportData as $upgrade) {
            $data['name'] = $upgrade->user->userDetails->name . '' . $upgrade->user->userDetails->second_name . '' . '(' . $upgrade->user->username . ')';

            $data['current_package'] = $upgrade->currentPackage->name;
            $data['upgrade_package'] = $upgrade->upgradePackage->name;
            $data['payment_amount'] = $upgrade->payment_amount;
            if ($upgrade->payment_type == 'free_upgrade' && $upgrade->payment_amount == 0) {
                $data['payment_type'] = 'Manualy by admin';
            } elseif ($upgrade->payment_type == 'free_upgrade' && $upgrade->payment_amount != 0) {
                $data['payment_type'] = 'Free upgrade';
            } else {
                $data['payment_type'] = $upgrade->payment_type;
            }

            $data['date'] = $upgrade->created_at;
            $row->push($data);
        }

        return $row;
    }
}
