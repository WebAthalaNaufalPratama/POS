<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PergantianExport implements FromView
{
    protected $result;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function view(): View
    {
        return view('kontrak.excelPergantian', [
            'data' => $this->result
        ]);
    }
}
