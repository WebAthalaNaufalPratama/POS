<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class TagihanPelangganExport implements FromView
{
    protected $produkterjual;
    protected $penjualan;

    public function __construct($produkterjual, $penjualan)
    {
        $this->produkterjual = $produkterjual;
        $this->penjualan = $penjualan; 
    }

    public function view(): View
    {
        return view('laporan.tagihanpelanggan_excel', [
            'produkterjual' => $this->produkterjual,
            'penjualan' => $this->penjualan 
        ]);
    }
}
