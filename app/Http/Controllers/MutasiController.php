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
use App\Models\Mutasi;
use App\Models\ProdukMutasi;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MutasiController extends Controller
{
    public function index_outlet()
    {
        $mutasis = Mutasi::where('no_mutasi', 'like', 'MGO%')->orderBy('created_at', 'desc')->get();
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
            $Invoice = Mutasi::latest()->first();
            // dd($bankpens);
            if ($Invoice != null) {
                $substring = substr($Invoice->no_mutasi, 11);
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

    public function store_outlet(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'pengirim' => 'required',
            'penerima' => 'required',
            'no_mutasi' => 'required',
            'tanggal_kirim' =>'required',
            'tanggal_diterima' => 'required',
            'status' => 'required',
            'pilih_pengiriman' => 'required',
            'biaya_pengiriman' =>'required',
            'total_biaya' => 'required'
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $req->except(['_token', '_method', 'bukti_file', 'bukti', 'status_bayar']);
        // dd($data);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_mutasi', $fileName, 'public');
            // dd($filePath);
            $data['bukti'] = $filePath;
        }
        
        $mutasi = Mutasi::create($data);

        if ($mutasi) {
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                // dd($getProdukJual);
                $produk_terjual = ProdukMutasi::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_mutasi' => $mutasi->no_mutasi,
                    'jumlah_dikirim' => $data['jumlah_dikirim'][$i],
                ]);

                if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                foreach ($getProdukJual->komponen as $komponen) {
                    $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                        'produk_terjual_id' => $produk_terjual->id,
                        'kode_produk' => $komponen->kode_produk,
                        'nama_produk' => $komponen->nama_produk,
                        'tipe_produk' => $komponen->tipe_produk,
                        'kondisi' => $komponen->kondisi,
                        'deskripsi' => $komponen->deskripsi,
                        'jumlah' => $komponen->jumlah,
                        'harga_satuan' => $komponen->harga_satuan,
                        'harga_total' => $komponen->harga_total
                    ]);
                    if (!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }
            }
            return redirect(route('mutasigalery.index'))->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }
    public function show_outlet($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = ProdukMutasi::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Jual::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        // dd($mutasis);
        return view('mutasigalery.show', compact('produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function update_outlet(Request $req, $mutasi)
    {
        $data = $req->except(['_token', '_method']);
        // dd($data);
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            // $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();

            $produkmutasi = ProdukMutasi::where('no_mutasi', $req->no_mutasi)
                                        ->where('id', $data['nama_produk'][$i])
                                        ->first();

            if ($produkmutasi) {
                $produkmutasi->update([
                    'jumlah_diterima' => $data['jumlah_diterima'][$i],
                ]);
            } else {
                return redirect()->back()->withInput()->with('fail', 'Produk Mutasi tidak ditemukan');
            }
        }
        
        return redirect(route('mutasigalery.index'))->with('success', 'Data Berhasil Disimpan');
    }

    public function index_outletgalery()
    {
        $mutasis = Mutasi::where('no_mutasi', 'like', 'MOG%')->orderBy('created_at', 'desc')->get();
        return view('mutasioutlet.index', compact('mutasis'));
    }


}
