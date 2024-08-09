<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StokIndenExport implements FromView
{
    protected $data;
    protected $produk;
    protected $total;
    protected $totalSisaBunga;

    public function __construct($data, $produk, $total, $totalSisaBunga)
    {
        $this->data = $data;
        $this->produk = $produk;
        $this->total = $total;
        $this->totalSisaBunga = $totalSisaBunga;
    }

    public function view(): View
    {
        return view('laporan.stok_inden_excel', [
            'data' => $this->data,
            'produk' => $this->produk,
            'total' => $this->total,
            'totalSisaBunga' => $this->totalSisaBunga
        ]);
    }
}
