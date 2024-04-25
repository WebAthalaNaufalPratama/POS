<?php

namespace App\Http\Controllers;

use App\Models\Komponen_Produk_Jual;
use App\Models\Kondisi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use App\Models\Komponen_Produk_Terjual;
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
use App\Models\Penjualan;
use App\Models\Pembayaran;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PenjualanController extends Controller
{

    public function index()
    {
        $penjualans = Penjualan::with('karyawan')->orderBy('created_at', 'desc')->get();

        $payments = Pembayaran::with('penjualan')->get();

        $latestPayments = [];

        foreach ($payments as $payment) {
            $penjualanId = $payment->invoice_penjualan_id;

            if (!isset($latestPayments[$penjualanId])) {
                $latestPayments[$penjualanId] = $payment;
            } else {
                if ($payment->id > $latestPayments[$penjualanId]->id) {
                    $latestPayments[$penjualanId] = $payment;
                }
            }
        }

        // dd($latestPayments);

        return view('penjualan.index', compact('penjualans', 'latestPayments'));
    }

    public function create()
    {
        $roles = Auth::user()->roles()->value('name');
        if ($roles == 'admin' || $roles == 'kasir') {
            $user = Auth::user()->value('id');
            $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
            // dd($karyawans);
            $customers = Customer::where('lokasi_id', $lokasi)->get();
            $lokasis = Lokasi::where('id', $lokasi)->get();
            $rekenings = Rekening::get();
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
            if ($Invoice != null) {
                $cekInvoice = substr($Invoice->no_invoice, -1);
            } else {
                $cekInvoice = 0;
            }
            // $komponen = Kondisi::with('komponen')->get();
            // dd($komponen);
            $kondisis = Kondisi::all();
            $invoices = Penjualan::get();
        }

        return view('penjualan.create', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices'));
    }


    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id_customer' => 'required',
            'point_dipakai' => 'required',
            'lokasi_id' => 'required',
            'distribusi' => 'required',
            'no_invoice' => 'required',
            'tanggal_invoice' => 'required',
            'jatuh_tempo' => 'required',
            'employee_id' => 'required',
            'status' => 'required',
            'bukti_file' => 'required|image|mimes:jpeg,png|max:2048',
            'notes' => 'required',
            'cara_bayar' => 'required',
            'pilih_pengiriman' => 'required',
            'biaya_ongkir' => 'required',
            'sub_total' => 'required',
            'promo_id' => 'required',
            'jenis_ppn' => 'required',
            'jumlah_ppn' => 'required',
            'dp' => 'required',
            'total_tagihan' => 'required',
            'sisa_bayar' => 'required'
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $req->except(['_token', '_method', 'bukti_file']);
        if ($req->hasFile('bukti_file')) {
            $file = $req->file('bukti_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_invoice_penjualan', $fileName, 'public');
            // dd($filePath);
            $data['bukti_file'] = $filePath;
        }
        // dd($data);
        $penjualan = Penjualan::create($data);

        if ($penjualan) {
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                // dd($getProdukJual);
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_invoice' => $penjualan->no_invoice,
                    'harga' => $data['harga_satuan'][$i],
                    'jumlah' => $data['jumlah'][$i],
                    'jenis_diskon' => $data['jenis_diskon'][$i],
                    'diskon' => $data['diskon'][$i],
                    'harga_jual' => $data['harga_total'][$i]
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
            return redirect(route('penjualan.index'))->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function destroy($penjualan)
    {
        $data = Penjualan::find($penjualan);
        // dd($data);
        if (!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $getProduks = Produk_Terjual::where('no_invoice', $data->no_invoice)->get();
        // dd($getProduks);
        $check = $data->delete();
        if (!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        if ($getProduks) {
            $getProduks->each->delete();
        }
        foreach ($getProduks as $item) {
            $getKomponenProduks = Komponen_Produk_Terjual::where('produk_terjual_id', $item->id)->get();
            if ($getKomponenProduks) {
                $getKomponenProduks->each->delete();
            }
        }
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function payment(Request $req, $penjualan)
    {
        $produkjuals = Produk_Jual::all();
        // dd($produkjuals);
        $penjualans = Penjualan::find($penjualan);
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        $karyawans = Karyawan::where('id', $penjualans->employee_id)->get();
        $promos = Promo::where('id', $penjualans->promo_id)->get();
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $Invoice = Pembayaran::latest()->first();
        if ($Invoice != null) {
            $cekInvoice = substr($Invoice->no_invoice_bayar, -1);
        } else {
            $cekInvoice = 0;
        }
        $pembayarans = Pembayaran::with('rekening')->where('invoice_penjualan_id', $penjualan)->orderBy('created_at', 'desc')->get();
        // dd($produks);
        // dd($promos);
        // $getProdukJual = Produk_Jual::find($penjualan);
        // $getKomponen = Komponen_Produk_Jual::where('produk_jual_id', $getProdukJual->id)->get();
        $roles = Auth::user()->roles()->value('name');
        $user = Auth::user()->value('id');
        $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
        $lokasis = Lokasi::where('id', $lokasi)->get();
        $rekenings = Rekening::get();
        $ongkirs = Ongkir::get();
        $bankpens = Rekening::get();
        $Invoice = Penjualan::latest()->first();
        $kondisis = Kondisi::all();
        $invoices = Penjualan::get();
        // return view('penjualan.create', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis','invoices'));
        return view('penjualan.payment', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'kondisis', 'invoices', 'penjualans', 'produkjuals', 'perangkai', 'cekInvoice', 'pembayarans'));
    }

    public function show(Request $req, $penjualan)
    {
        $produkjuals = Produk_Jual::all();
        // dd($produkjuals);
        $penjualans = Penjualan::find($penjualan);
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        $karyawans = Karyawan::where('id', $penjualans->employee_id)->get();
        $promos = Promo::where('id', $penjualans->promo_id)->get();
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        // dd($produks);
        // dd($promos);
        // $getProdukJual = Produk_Jual::find($penjualan);
        // $getKomponen = Komponen_Produk_Jual::where('produk_jual_id', $getProdukJual->id)->get();
        $roles = Auth::user()->roles()->value('name');
        $user = Auth::user()->value('id');
        $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
        $lokasis = Lokasi::where('id', $lokasi)->get();
        $rekenings = Rekening::get();
        $ongkirs = Ongkir::get();
        $bankpens = Rekening::get();
        $Invoice = Penjualan::latest()->first();
        $kondisis = Kondisi::all();
        $invoices = Penjualan::get();
        // return view('penjualan.create', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis','invoices'));
        return view('penjualan.show', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'kondisis', 'invoices', 'penjualans', 'produkjuals', 'perangkai'));
    }
}
