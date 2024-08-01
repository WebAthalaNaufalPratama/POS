<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function kontrak_index()
    {
        return view('laporan.kontrak');
    }

    public function kontrak_pdf()
    {
        dd('pdf');
    }
    
    public function kontrak_excel()
    {
        dd('excel');
    }
}
