<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class KasPusatExport implements FromView
{
    protected $data;
    protected $thisMonth;
    protected $thisYear;
    protected $saldo;
    protected $totalSaldo;
    protected $saldoRekening;
    protected $saldoCash;
    protected $id_galleries;

    public function __construct($data, $thisMonth, $thisYear, $saldo, $totalSaldo, $saldoRekening, $saldoCash, $id_galleries)
    {
        $this->data = $data;
        $this->thisMonth = $thisMonth;
        $this->thisYear = $thisYear;
        $this->saldo = $saldo;
        $this->totalSaldo = $totalSaldo;
        $this->saldoRekening = $saldoRekening;
        $this->saldoCash = $saldoCash;
        $this->id_galleries = $id_galleries;
    }

    public function view(): View
    {
        return view('laporan.kas_pusat_excel', [
            'data' => $this->data,
            'thisMonth' => $this->thisMonth,
            'thisYear' => $this->thisYear,
            'saldo' => $this->saldo,
            'totalSaldo' => $this->totalSaldo,
            'saldoRekening' => $this->saldoRekening,
            'saldoCash' => $this->saldoCash,
            'id_galleries' => $this->id_galleries,
        ]);
    }
}
