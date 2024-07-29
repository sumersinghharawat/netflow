<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PayoutPendingReport implements FromCollection, WithHeadings, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $reportData;

    public function headings(): array
    {
        return [
            'MemberName',
            'Amount',
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
        foreach ($reportData['amountPaids'] as $task) {
            $data['name'] = $task->user->userDetails->name . '' . $task->user->userDetails->second_name . '' . '(' . $task->user->username . ')';
            $data['amount'] = $task->amount;
            $data['date'] = $task->date;
            $row->push($data);
        }

        return $row;
    }
}
