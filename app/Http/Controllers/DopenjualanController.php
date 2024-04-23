<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Komponen_Produk_Jual;
use App\Models\Kondisi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Tipe_Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use App\Models\Customer;
use App\Models\Lokasi;
use App\Models\Karyawan;
use App\Models\Rekening;
use App\Models\Promo;
use App\Models\Ongkir;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DopenjualanController extends Controller
{
    public function index()
    {
        return view('dopenjualan.index');
    }

    public function create()
    {
        $roles = Auth::user()->roles()->value('name');
        if($roles == 'admin' || $roles == 'kasir')
        {
            $user = Auth::user()->value('id');
            $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
            // dd($karyawans);
            $customers = Customer::where('lokasi_id', $lokasi)->get();
            $lokasis = Lokasi::where('id', $lokasi)->get();
            $rekenings = Rekening::get();
            $ongkirs = Ongkir::get();
            $karyawans = Karyawan::where('lokasi_id', $lokasi)->get();        
            $promos = Promo::where(function($query) use ($lokasi) {
                $query->where('lokasi_id', $lokasi)
                      ->orWhere('lokasi_id', 'Semua');
            })->get();
            $produks = Produk_Jual::get();
            $bankpens = Rekening::get();
        }

        return view('dopenjualan.create', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens'));
    }
}
