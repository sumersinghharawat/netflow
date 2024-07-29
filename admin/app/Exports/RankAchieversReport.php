<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RankAchieversReport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $reportData;

    public function headings(): array
    {
        return [
            'MemberName',
            'New Rank',
            'Rank achieved date',
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
        foreach ($reportData as $rank) {
            $data['name'] = $rank->user->userDetails->name . '' . $rank->user->userDetails->second_name . '' . '(' . $rank->user->username . ')';

            $data['rank_name'] = $rank->rank->name;
            $data['date'] = $rank->created_at;
            $row->push($data);
        }
        return $row;
    }
}
