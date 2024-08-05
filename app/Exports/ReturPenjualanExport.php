<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReturPenjualanExport implements FromView
{
    protected $combinedData;

    public function __construct($combinedData)
    {
        $this->combinedData = $combinedData;
    }

    public function view(): View
    {
        return view('laporan.returpenjualan_excel', [
            'combinedData' => $this->combinedData
        ]);
    }
}
