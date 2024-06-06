<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use App\Models\Mutasiindens;
use App\Models\ProdukMutasiInden;
use Illuminate\Http\Request;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\InventoryInden;
use App\Models\Pembayaran;
use App\Models\Supplier;

class MutasiindensController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function generatemutasiNumber() {
        // Ambil tanggal hari ini dalam format 'Y-m-d'
        $tgl_today = date('Y-m-d');
        
        // Cari urutan terakhir nomor PO pada hari ini
        $last = Mutasiindens::whereDate('created_at', $tgl_today)->orderBy('id', 'desc')->first();
    
        // Jika tidak ada nomor PO pada hari ini, urutan diinisialisasi dengan 1
        $urutan = 1;
        if ($last) {
            // Jika ada nomor PO pada hari ini, ambil urutan berikutnya
            $urutan = intval(substr($last->no_mutasi, -3)) + 1;
        }
    
        // Format nomor PO dengan pola 'PO_tgl_urutanpo'
        $nomor_mutasi = 'MI_' . date('Ymd') . '_' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
        
        return $nomor_mutasi;
    }

    public function index_indengh(Request $req)
    {
        $query = Mutasiindens::orderBy('created_at', 'desc');

        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }
        $mutasis = $query->get();

        return view('mutasiindengh.index', compact('mutasis'));
    } 

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_indengh()
    {
    
        $produks = InventoryInden::get();
        $no_mutasi = $this->generatemutasiNumber();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        $lokasi = Lokasi::where('tipe_lokasi', 3)->get();
        $kondisis = Kondisi::all();
        // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')

        // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
        return view('mutasiindengh.create',compact('suppliers','lokasi','produks','kondisis','no_mutasi'));
    }

    public function getBulanInden($supplier_id)
    {
        // Ambil data bulan inden berdasarkan supplier_id
        $bulanInden = InventoryInden::where('supplier_id', $supplier_id)->pluck('bulan_inden')->unique();

        return response()->json($bulanInden);
    }
    public function getkodeInden($bulan_inden, $supplier_id)
    {
        // Ambil data bulan inden berdasarkan supplier_id
        $kodeInden = InventoryInden::where('supplier_id', $supplier_id)
        ->where('bulan_inden', $bulan_inden)
        ->pluck('kode_produk_inden')->unique();

        return response()->json($kodeInden);
    }
    public function getkategoriInden($kode_inden, $bulan_inden, $supplier_id)
    {
        // Ambil data kategori berdasarkan supplier_id, bulan_inden, dan kode_produk_inden
        $kategori = InventoryInden::where('supplier_id', $supplier_id)
            ->where('bulan_inden', $bulan_inden)
            ->where('kode_produk_inden', $kode_inden)
            ->with('produk') // Load relasi dengan produk
            ->first()->produk->nama; // Ambil kategori dari relasi dengan produk
       

        return response()->json($kategori);
        

    }
        /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_indengh(Request $request)
    {
       // Validasi input
    $validator = Validator::make($request->all(), [
        'supplier_id' => 'required',
        'lokasi_id' => 'required',
        // 'tgl_dikirim' => 'required|date',
        // Tambahkan validasi sesuai kebutuhan lainnya
    ]);

    if ($validator->fails()) {
        $errors = $validator->errors()->all();
        return redirect()->back()->withInput()->with('fail', $errors);
    }
    // Simpan data ke tabel mutasiindens
    $mutasiinden = new Mutasiindens();
    $no_mutasi = $mutasiinden->no_mutasi = $request->no_mutasi;
    $mutasiinden->supplier_id = $request->supplier_id;
    $mutasiinden->lokasi_id = $request->lokasi_id;
    $mutasiinden->tgl_dikirim = $request->tgl_kirim;
    $mutasiinden->subtotal = $request->sub_total ?? '';
    $mutasiinden->biaya_perawatan = $request->biaya_rwt ?? '';
    $mutasiinden->biaya_pengiriman = $request->biaya_ongkir ?? '';
    $mutasiinden->total_biaya = $request->total_tagihan;
    $mutasiinden->pembuat_id = $request->pembuat;
    $mutasiinden->status_dibuat = $request->status_dibuat;
    $mutasiinden->tgl_dibuat = $request->tgl_dibuat;

    if ($request->hasFile('bukti')) {
        $file = $request->file('bukti');
        $fileName = $request->no_mutasi . date('YmdHis') . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('bukti_mutasi_inden', $fileName, 'public');
        $mutasiinden->bukti = $filePath; // Simpan path file ke dalam model jika ada
    }
    // Tambahkan atribut lainnya sesuai kebutuhan
    $mutasiinden->save();

    // Simpan detail barang ke tabel produk_mutasi_indens
    foreach ($request->bulan_inden as $key => $bulanInden) {
        // Cari inventoryinden_id berdasarkan kode_inden, bulan_inden, dan supplier_id
        $inventoryInden = InventoryInden::where('kode_produk_inden', $request->kode_inden[$key])
            ->where('bulan_inden', $bulanInden)
            ->where('supplier_id', $request->supplier_id)
            ->first();

        if ($inventoryInden) {
            $produkMutasiInden = new ProdukMutasiInden();
            $produkMutasiInden->mutasiinden_id = $mutasiinden->id;
            $produkMutasiInden->inventoryinden_id = $inventoryInden->id;
            $produkMutasiInden->jml_dikirim = $request->qtykrm[$key];
            $produkMutasiInden->jml_diterima = $request->qtytrm[$key] ?? '';
            $produkMutasiInden->kondisi_id = $request->kondisi[$key] ?? '';
            $produkMutasiInden->biaya_rawat = $request->rawat[$key] ?? '';
            $produkMutasiInden->totalharga = $request->jumlah[$key] ?? '';
            // Tambahkan atribut lainnya sesuai kebutuhan
            $produkMutasiInden->save();
        } else {
            // Handle jika tidak ditemukan record di InventoryInden
            return redirect()->back()->withInput()->with('fail', 'tidak ditemukan record di InventoryInden');
        }

        return redirect(route('mutasiindengh.index'))->with('success', 'Data Mutasi berhasil disimpan. Nomor Mutasi: ' . $no_mutasi);

    }
 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mutasiindens  $mutasiindens
     * @return \Illuminate\Http\Response
     */
    public function show(Mutasiindens $mutasiindens)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mutasiindens  $mutasiindens
     * @return \Illuminate\Http\Response
     */
    public function edit(Mutasiindens $mutasiindens)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mutasiindens  $mutasiindens
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mutasiindens $mutasiindens)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mutasiindens  $mutasiindens
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mutasiindens $mutasiindens)
    {
        //
    }
}
