<?php

namespace App\Exports;

use App\Models\Kontrak;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PergantianExport implements FromView
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function view(): View
    {
        $kontrak = Kontrak::with('customer', 'data_sales', 'do_sewa', 'kembali_sewa', 'do_sewa.produk.produk', 'do_sewa.produk.komponen')->find($this->id);
        // dd($kontrak);

        if (!$kontrak) {
            abort(404, "Data tidak ditemukan.");
        }

        return view('kontrak.excelPergantian', [
            'data' => $kontrak
        ]);
    }
}
