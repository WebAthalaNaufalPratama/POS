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
use App\Models\Penjualan;
use App\Models\Produk_Terjual;
use App\Models\DeliveryOrder;
use App\Models\Komponen_Produk_Terjual;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DopenjualanController extends Controller
{
    public function index()
    {
        $dopenjualans = DeliveryOrder::where('no_do', 'LIKE', 'DOP%')->orderBy('created_at', 'desc')->get();
        // dd($dopenjualans);
        return view('dopenjualan.index', compact('dopenjualans'));
    }

    public function create($penjualan)
    {
        $penjualans = Penjualan::with('produk')->find($penjualan);
        // dd($penjualans);
        $user = Auth::user();
        $lokasis = Lokasi::find($user);
        $karyawans = Karyawan::all();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $produkjuals = Produk_Jual::all();
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        // dd($produks);

        return view('dopenjualan.create', compact('penjualans', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals', 'cekInvoice'));
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'no_do' => 'required',
            'no_referensi' => 'required',
            'tanggal_kirim' => 'required',
            'driver' => 'required',
            'customer_id' => 'required',
            'penerima' => 'required',
            'handphone' => 'required',
            'alamat' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_do_penjualan', $fileName, 'public');
            // dd($filePath);
            $data['file'] = $filePath;
        }
        // dd($data['nama_produk']);
        $data['jenis_do'] = 'PENJUALAN';
        $data['status'] = 'DIKIRIM';
        // $data['tanggal_pembuat'] = now();
        $data['pembuat'] = Auth::user()->id;

        // save data do
        $check = DeliveryOrder::create($data);
        if (!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

        // save produk do
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->id,
                'no_do' => $check->no_do,
                'jumlah' => $data['jumlah'][$i],
                'satuan' => $data['satuan'][$i],
                'keterangan' => $data['keterangan'][$i]
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

        if (!empty($data['nama_produk2'])) {
            // Simpan data tambahan
            foreach ($data['nama_produk2'] as $index => $nama_produk) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $nama_produk)->first();
                if (!$getProdukJual) {
                    return redirect(route('penjualan.index'))->with('success', 'Data tersimpan');
                }
        
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_do' => $check->no_do,
                    'jumlah' => $data['jumlah2'][$index],
                    'satuan' => $data['satuan2'][$index],
                    'jenis' => 'TAMBAHAN',
                    'keterangan' => $data['keterangan2'][$index]
                ]);
        
                if (!$produk_terjual) {
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data produk terjual');
                }
        
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
                    if (!$komponen_produk_terjual) {
                        return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data komponen produk terjual');
                    }
                }
            }
        }
        

        return redirect(route('penjualan.index'))->with('success', 'Data tersimpan');
    }

    public function show($dopenjualan)
    {
        $dopenjualan = DeliveryOrder::find($dopenjualan);
        $produkjuals = Produk_Jual::all();
        $customers = Customer::all();
        $karyawans = Karyawan::all();
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        return view('dopenjualan.show', compact('dopenjualan', 'produkjuals', 'karyawans', 'customers', 'cekInvoice'));
    }

    public function update(Request $req, $dopenjualan)
    {
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = $req->no_do . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_do_sewa', $fileName, 'public');
            $data['file'] = $filePath;

            // update bukti DO
            $do = DeliveryOrder::find($dopenjualan);
            $do->file = $data['file'];
            $do->update();
            return redirect()->back()->with('success', 'File tersimpan');
        } else {
            return redirect()->back()->with('fail', 'Gagal menyimpan file');
        }
    }
}
