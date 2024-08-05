<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PembayaranExport implements FromView
{
    protected $pembayaran;
    protected $duit;

    public function __construct($pembayaran, $duit)
    {
        $this->pembayaran = $pembayaran;
        $this->duit = $duit; 
    }

    public function view(): View
    {
        return view('laporan.pembayaran_excel', [
            'pembayaran' => $this->pembayaran,
            'duit' => $this->duit 
        ]);
    }
}
