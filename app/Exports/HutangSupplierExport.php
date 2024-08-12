<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class HutangSupplierExport implements FromView
{
    protected $data;
    protected $totalTagihan;

    public function __construct($data, $totalTagihan)
    {
        $this->data = $data;
        $this->totalTagihan = $totalTagihan;
    }

    public function view(): View
    {
        return view('laporan.hutang_supplier_excel', [
            'data' => $this->data,
            'totalTagihan' => $this->totalTagihan
        ]);
    }
}
