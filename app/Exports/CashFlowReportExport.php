<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class CashFlowReportExport implements FromView, WithTitle
{
    protected $results;
    protected $openingBalance;
    protected $startDate;
    protected $endDate;

    public function __construct($results, $openingBalance, $startDate, $endDate)
    {
        $this->results = $results;
        $this->openingBalance = $openingBalance;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('reports.cash-flow-excel', [
            'results' => $this->results,
            'openingBalance' => $this->openingBalance,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function title(): string
    {
        return 'Buku Kas Umum';
    }
}
