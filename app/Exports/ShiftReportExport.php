<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class ShiftReportExport implements FromView, WithTitle
{
    protected $shifts;
    protected $startDate;
    protected $endDate;

    public function __construct($shifts, $startDate, $endDate)
    {
        $this->shifts = $shifts;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('reports.shifts-excel', [
            'shifts' => $this->shifts,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function title(): string
    {
        return 'Laporan Shift Kasir';
    }
}
