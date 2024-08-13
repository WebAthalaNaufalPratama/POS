<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StokPusatExport implements FromView
{
    protected $data;
    protected $listDate;
    protected $lokasi;

    public function __construct($data, $listDate, $lokasi)
    {
        $this->data = $data;
        $this->listDate = $listDate;
        $this->lokasi = $lokasi;
    }

    public function view(): View
    {
        return view('laporan.stok_pusat_excel', [
            'data' => $this->data,
            'listDate' => $this->listDate,
            'lokasi' => $this->lokasi
        ]);
    }
}
