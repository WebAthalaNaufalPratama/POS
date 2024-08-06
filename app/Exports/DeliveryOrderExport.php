<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class DeliveryOrderExport implements FromView
{
    protected $combinedData;

    public function __construct($combinedData)
    {
        $this->combinedData = $combinedData;
    }

    public function view(): View
    {
        return view('laporan.dopenjualan_excel', [
            'combinedData' => $this->combinedData
        ]);
    }
}
