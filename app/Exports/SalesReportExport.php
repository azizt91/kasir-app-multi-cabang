<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportExport implements FromView, WithTitle
{
    protected $transactions;
    protected $expenses;
    protected $purchases;
    protected $summary;
    protected $startDate;
    protected $endDate;

    public function __construct($transactions, $expenses, $purchases, $summary, $startDate, $endDate)
    {
        $this->transactions = $transactions;
        $this->expenses = $expenses;
        $this->purchases = $purchases;
        $this->summary = $summary;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function view(): View
    {
        return view('reports.sales-excel', [
            'transactions' => $this->transactions,
            'expenses' => $this->expenses,
            'purchases' => $this->purchases,
            'summary' => $this->summary,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
        ]);
    }

    public function title(): string
    {
        return 'Laporan Penjualan';
    }
}
