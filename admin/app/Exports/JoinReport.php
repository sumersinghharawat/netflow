<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JoinReport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $reportData;
    protected $regFeeStatus;

    public function headings(): array
    {
        $header = [
            'MemberName',
            'Sponsor',
            'Package',
            'Payment Method',
            'Enrollment Date',
        ];
        if ($this->regFeeStatus) {
            array_splice($header, 3, 0, 'Registration Fee');
        }
        return $header;
    }

    public function __construct($reportData, $regFeeStatus)
    {
        $this->reportData = $reportData;
        $this->regFeeStatus = $regFeeStatus;
    }

    public function collection()
    {
        $reportData = $this->reportData;

        $row = collect([]);
        foreach ($reportData as $joinDetails) {
            $data['name'] = $joinDetails->userDetails->name . '' . $joinDetails->userDetails->second_name;
            $data['sponsor_name'] = $joinDetails->user->sponsor->username ?? 'Na';
            $data['package'] = $joinDetails->package->name ?? $joinDetails->package->model . '(' . $joinDetails->package->price . ')' ?? 'Na';
            if ($this->regFeeStatus) {
                $data['package_value'] = formatCurrency($joinDetails->reg_amount) ?? 'Na';
            }
            $data['register'] = str_replace('_', ' ', ucfirst($joinDetails->paymentGateway->name ?? 'NA'));
            $data['date'] = $joinDetails->user->date_of_joining;
            $row->push($data);
        }
        // die();
        return $row;
    }
}
