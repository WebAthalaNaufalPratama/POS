<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Komponen_Produk_Jual;
use App\Models\Kondisi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use App\Models\Komponen_Produk_Terjual;
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
use App\Models\Penjualan;
use App\Models\Pembayaran;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MutasiController extends Controller
{
    public function index_outlet()
    {
        $mutasis = Mutasi::orderBy('created_at', 'desc')->get();
        return view('mutasigalery.index', compact('mutasis'));
    }

    public function create_outlet()
    {
        $roles = Auth::user()->roles()->value('name');
        if ($roles == 'admin' || $roles == 'kasir') {
            $user = Auth::user()->value('id');
            $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
            // dd($karyawans);
            $customers = Customer::where('lokasi_id', $lokasi)->get();
            $lokasis = Lokasi::where('id', $lokasi)->get();
            $ongkirs = Ongkir::get();
            $karyawans = Karyawan::where('lokasi_id', $lokasi)->get();
            $promos = Promo::where(function ($query) use ($lokasi) {
                $query->where('lokasi_id', $lokasi)
                    ->orWhere('lokasi_id', 'Semua');
            })->get();
            $produks = Produk_Jual::with('komponen.kondisi')->get();
            // dd($produks);
            $bankpens = Rekening::get();
            $Invoice = Penjualan::latest()->first();
            // dd($bankpens);
            if ($Invoice != null) {
                $substring = substr($Invoice->no_invoice, 11);
                $cekInvoice = substr($substring, 0, 3);
                // dd($cekInvoice);
            } else {
                $cekInvoice = 0;
            }
            $InvoiceBayar = Pembayaran::latest()->first();
        // dd($Invoice);
        if ($InvoiceBayar != null) {
            $substringBayar = substr($InvoiceBayar->no_invoice_bayar, 11);
            $cekInvoiceBayar = substr($substringBayar, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoiceBayar = 0;
        }
            // $komponen = Kondisi::with('komponen')->get();
            // dd($komponen);
            $kondisis = Kondisi::all();
            $invoices = Penjualan::get();
        }

        return view('mutasigalery.create', compact('customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
    }
}
