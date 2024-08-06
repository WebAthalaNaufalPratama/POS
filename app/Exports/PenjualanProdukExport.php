<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PenjualanProdukExport implements FromView
{
    protected $produkterjual;
    protected $pojuList;

    public function __construct($produkterjual, $pojuList)
    {
        $this->produkterjual = $produkterjual;
        $this->pojuList = $pojuList; 
    }

    public function view(): View
    {
        return view('laporan.penjualanproduk_excel', [
            'produkterjual' => $this->produkterjual,
            'pojuList' => $this->pojuList 
        ]);
    }
}
