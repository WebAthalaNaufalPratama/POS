<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PergantianExport implements FromView
{
    public function view(): View
    {
        return view('kontrak.excelPergantian', [
            'users' => User::all()
        ]);
    }
}
