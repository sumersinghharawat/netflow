<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProfileDateReport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $reportData;

    public function headings(): array
    {
        return [
            'Member Name',
            'Sponsor',
            'E-mail',
            'Phone',
            'Country',
            'Zip',
            'Enrollment Date',
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
        foreach ($reportData as $profileDetails) {
            $data['Member Name'] = $profileDetails->userDetails->name . '' . $profileDetails->userDetails->second_name;
            $data['Sponsor'] = $profileDetails->sponsor->username ?? 'NA';
            // $data['email'] = $profileDetails->userDetails->email ?? 'NA';
            $data['email'] = $profileDetails->email ?? 'NA';
            $data['phone'] = $profileDetails->userDetails->mobile ?? 'NA';
            $data['Country'] = $profileDetails->userDetails->country->name ?? 'NA';
            $data['Zip'] = $profileDetails->userDetails->pin ?? 'NA';
            $data['date'] = $profileDetails->date_of_joining;
            $row->push($data);
        }
        return $row;
    }
}
