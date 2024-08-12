<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StokGalleryExport implements FromView
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
        return view('laporan.stok_gallery_excel', [
            'data' => $this->data,
            'listDate' => $this->listDate,
            'lokasi' => $this->lokasi
        ]);
    }
}
