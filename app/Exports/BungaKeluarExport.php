<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class BungaKeluarExport implements FromView
{
    protected $list;
    protected $periode;

    public function __construct($list, $periode)
    {
        $this->list = $list;
        $this->periode = $periode;
    }

    public function view(): View
    {
        return view('laporan.bunga_keluar_excel', [
            'list' => $this->list,
            'periode' => $this->periode
        ]);
    }
}
