<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PelangganExport implements FromView
{
    protected $penjualan;

    public function __construct($penjualan)
    {
        $this->penjualan = $penjualan;
    }

    public function view(): View
    {
        return view('laporan.pelanggan_excel', [
            'penjualan' => $this->penjualan
        ]);
    }
}
