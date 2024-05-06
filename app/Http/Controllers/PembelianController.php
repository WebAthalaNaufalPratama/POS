<?php

namespace App\Http\Controllers;

use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Pembelian;
use App\Models\Produk;
use App\Models\Produkbeli;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembelianController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $datapos = Pembelian::get();
        return view('purchase.index',compact('datapos'));

    }
    public function invoice()
    {
        return view('purchase.invoice');

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function generatePONumber() {
        // Ambil tanggal hari ini dalam format 'Y-m-d'
        $tgl_today = date('Y-m-d');
        
        // Cari urutan terakhir nomor PO pada hari ini
        $lastPO = Pembelian::whereDate('created_at', $tgl_today)->orderBy('id', 'desc')->first();
    
        // Jika tidak ada nomor PO pada hari ini, urutan diinisialisasi dengan 1
        $urutan = 1;
        if ($lastPO) {
            // Jika ada nomor PO pada hari ini, ambil urutan berikutnya
            $urutan = $lastPO->id + 1;
        }
    
        // Format nomor PO dengan pola 'PO_tgl_urutanpo'
        $nomor_po = 'PO_' . date('Ymd') . '_' . $urutan;
    
        return $nomor_po;
    }
    
    public function create() {
        // Generate nomor PO
        $nomor_po = $this->generatePONumber();
    
        // Ambil data yang diperlukan
        $produks = Produk::get();
        $suppliers = Supplier::where('tipe_supplier','tradisional')->get();
        $lokasis = Lokasi::get();
        $kondisis = Kondisi::get();
    
        return view('purchase.create', compact('produks', 'suppliers', 'lokasis', 'kondisis', 'nomor_po'));
    }
    

    public function createinden()
    {
        $produks = Produk::get();
        $suppliers = Supplier::get();
        return view('purchase.createinden', compact('produks','suppliers'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_po(Request $request)
    {
            // Validasi input
            $request->validate([
                'nopo' => 'required',
                'id_supplier' => 'required',
                'id_lokasi' => 'required',
                'tgl_kirim' => 'required|date',
                'no_do' => 'required',
                'status_dibuat' => 'required',

                // Tambahkan validasi lainnya sesuai kebutuhan
            ]);
        
            // Simpan data pembelian
            $pembelian = new Pembelian();
            $pembelian->no_po = $request->nopo;
            $pembelian->supplier_id = $request->id_supplier;
            $pembelian->lokasi_id = $request->id_lokasi;
            $pembelian->tgl_kirim = $request->tgl_kirim;
            $pembelian->no_do_suplier = $request->no_do;
            $pembelian->pembuat = Auth::id(); // ID pengguna yang membuat pembelian
            $pembelian->pemeriksa = Auth::id(); // ID pengguna yang membuat pembelian
            $pembelian->penerima = Auth::id(); // ID pengguna yang membuat pembelian
            $pembelian->tgl_dibuat = now(); // Tanggal pembuatan saat ini
            $pembelian->tgl_diterima = now(); // Tanggal pembuatan saat ini
            $pembelian->tgl_diperiksa = now(); // Tanggal pembuatan saat ini
            $pembelian->status_dibuat = $request->status_dibuat; // Status pembuatan
            $pembelian->status_diterima = $request->status_diterima; // Status pembuatan
            $pembelian->status_diperiksa = $request->status_diperiksa; // Status pembuatan
            $pembelian->save();
        
            // Ambil nomor PO yang baru dibuat
            $no_po = $pembelian->no_po;
        
            // Simpan data produk beli
            $produkIds = $request->produk;
            $qtyKirim = $request->qtykrm;
            $qtyTerima = $request->qtytrm;
            $kondisiIds = $request->kondisi;
        
            // Loop untuk setiap produk yang ditambahkan
            foreach ($produkIds as $index => $produkId) {
                $produkBeli = new Produkbeli();
                $produkBeli->pembelian_id = $pembelian->id;
                $produkBeli->produk_id = $produkId;
                $produkBeli->jml_dikirim = $qtyKirim[$index];
                $produkBeli->jml_diterima = $qtyTerima[$index];
                $produkBeli->kondisi_id = $kondisiIds[$index];
                // Tambahkan atribut lainnya sesuai kebutuhan
                $produkBeli->save();
            }
        
            // Redirect ke halaman yang sesuai atau tampilkan pesan sukses
            return redirect()->back()->with('success', 'Data pembelian berhasil disimpan. Nomor PO: ' . $no_po);
        }
        

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function show($datapo)
    {
        $beli = Pembelian::find($datapo);
        $produkbelis = Produkbeli::where('pembelian_id', $datapo)->get();
        return view('purchase.showpo',compact('beli','produkbelis'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function edit(Pembelian $pembelian)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pembelian $pembelian)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pembelian  $pembelian
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pembelian $pembelian)
    {
        //
    }
}
