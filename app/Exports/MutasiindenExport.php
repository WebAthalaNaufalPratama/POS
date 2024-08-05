<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MutasiindenExport implements FromView
{
    protected $produkterjual;
    protected $mutasiindenRecords;

    public function __construct($produkterjual, $mutasiindenRecords)
    {
        $this->produkterjual = $produkterjual;
        $this->mutasiindenRecords = $mutasiindenRecords; 
    }

    public function view(): View
    {
        return view('laporan.mutasiinden_excel', [
            'produkterjual' => $this->produkterjual,
            'mutasiindenRecords' => $this->mutasiindenRecords 
        ]);
    }
}
