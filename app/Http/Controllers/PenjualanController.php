<?php

namespace App\Http\Controllers;

use App\Models\Komponen_Produk_Jual;
use App\Models\Kondisi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Tipe_Produk;
use Illuminate\Http\Request;
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
class PenjualanController extends Controller
{

    public function index()
    {
        
        
        return view('penjualan.index');
    }

    public function create()
    {
        $roles = Auth::user()->roles()->value('name');
        if($roles == 'admin' || $roles == 'kasir')
        {
            $lokasi = Auth::user()->value('lokasi_id');
            $customers = Customer::where('lokasi_id', $lokasi)->get();
            $lokasis = Lokasi::get();
            $karyawans = Karyawan::where('lokasi_id', $lokasi)->get();
            $rekenings = Rekening::get();
            $ongkirs = Ongkir::get();
            $promos = Promo::where(function($query) use ($lokasi) {
                $query->where('lokasi_id', $lokasi)
                      ->orWhere('lokasi_id', 'Semua');
            })->get();
            $produks = Produk_Jual::get();
        }

        return view('penjualan.create', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs'));
    }
}
