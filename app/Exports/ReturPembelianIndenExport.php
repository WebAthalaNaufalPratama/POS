<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReturPembelianIndenExport implements FromView
{
    protected $returs;

    public function __construct($returs)
    {
        $this->returs = $returs;
    }

    public function view(): View
    {
        return view('laporan.retur_pembelian_inden_excel', [
            'returs' => $this->returs
        ]);
    }
}
