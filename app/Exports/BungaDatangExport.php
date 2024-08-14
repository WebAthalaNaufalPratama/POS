<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BungaDatangExport implements FromView
{
    protected $groupedData;
    protected $listDate;
    protected $lokasi;

    public function __construct($groupedData, $listDate, $lokasi)
    {
        $this->groupedData = $groupedData;
        $this->listDate = $listDate;
        $this->lokasi = $lokasi;
    }

    public function view(): View
    {
        return view('laporan.bunga_datang_excel', [
            'groupedData' => $this->groupedData,
            'listDate' => $this->listDate,
            'lokasi' => $this->lokasi
        ]);
    }
}
