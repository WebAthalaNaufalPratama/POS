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
use App\Models\Karyawan;
use App\Models\Pembayaran;
use App\Models\Supplier;
use ProdukMutasiIndens;

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
        $lokasi = Lokasi::whereIn('tipe_lokasi', [1, 3])->get();
        $kondisis = Kondisi::all();
        // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')

        // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
        return view('mutasiindengh.create',compact('suppliers','lokasi','produks','kondisis','no_mutasi'));
    }

    public function getBulanInden($supplier_id)
    {
            // Ambil data bulan inden berdasarkan supplier_id
        $bulanInden = InventoryInden::where('supplier_id', $supplier_id)->pluck('bulan_inden')->unique()->values()->all();

        return response()->json($bulanInden);
    }

    public function getkodeInden($bulan_inden, $supplier_id)
    {
        // Ambil data bulan inden berdasarkan supplier_id
        $kodeInden = InventoryInden::where('supplier_id', $supplier_id)
        ->where('bulan_inden', $bulan_inden)
        ->pluck('kode_produk_inden')->all();

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
        $mutasiinden->subtotal = $request->sub_total ?? null;
        $mutasiinden->biaya_perawatan = $request->biaya_rwt ?? null;
        $mutasiinden->biaya_pengiriman = $request->biaya_ongkir ?? null;
        $mutasiinden->total_biaya = $request->total_tagihan ?? null;
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
                $produkMutasiInden->jml_diterima = $request->qtytrm[$key] ?? null;
                $produkMutasiInden->kondisi_id = $request->kondisi[$key] ?? null;
                $produkMutasiInden->biaya_rawat = $request->rawat[$key] ?? null;
                $produkMutasiInden->totalharga = $request->jumlah[$key] ?? null;
                // Tambahkan atribut lainnya sesuai kebutuhan
                $produkMutasiInden->save();
            } else {
                // Handle jika tidak ditemukan record di InventoryInden
                return redirect()->back()->withInput()->with('fail', 'tidak ditemukan record di InventoryInden');
            }
   
        }
        return redirect(route('mutasiindengh.index'))->with('success', 'Data Mutasi berhasil disimpan. Nomor Mutasi: ' . $no_mutasi);
 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mutasiindens  $mutasiindens
     * @return \Illuminate\Http\Response
     */
    public function show_indengh($mutasiIG)
    {
        
            $data = Mutasiindens::where('id', $mutasiIG)->first();
            $pembuat = Karyawan::where('user_id',$data->pembuat_id)->value('nama');
            $jabatanbuat = Karyawan::where('user_id',$data->pembuat_id)->value('jabatan');
            $penerima = Karyawan::where('user_id',$data->penerima_id)->value('nama');
            $jabatanterima = Karyawan::where('user_id',$data->penerima_id)->value('jabatan');
            $pembuku = Karyawan::where('user_id',$data->pembuku_id)->value('nama');
            $jabatanbuku = Karyawan::where('user_id',$data->pembuku_id)->value('jabatan');
            $pemeriksa = Karyawan::where('user_id',$data->pemeriksa_id)->value('nama');
            $jabatanperiksa = Karyawan::where('user_id',$data->pemeriksa_id)->value('jabatan');

            $barangmutasi = ProdukMutasiInden::with('kondisi')->where('mutasiinden_id',$data->id)->get();
            // return $barangmutasi;
            $produks = InventoryInden::get();
            // $no_mutasi = $this->generatemutasiNumber();
            $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
            $lokasi = Lokasi::all();
            $kondisis = Kondisi::all();
            // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')
            // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
            return view('mutasiindengh.show',compact('data','suppliers','lokasi','produks','kondisis','barangmutasi','pembuat','penerima','pembuku','pemeriksa','jabatanbuat','jabatanterima','jabatanbuku','jabatanperiksa'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mutasiindens  $mutasiindens
     * @return \Illuminate\Http\Response
     */
    public function edit_indengh($mutasiIG)
    {
        $data = Mutasiindens::where('id', $mutasiIG)->first();
        $pembuat = Karyawan::where('user_id',$data->pembuat_id)->value('nama');
        // return $pembuat;
        $jabatan = Karyawan::where('user_id',$data->pembuat_id)->value('jabatan');
        $barangmutasi = ProdukMutasiInden::where('mutasiinden_id',$data->id)->get();
        // return $barangmutasi;
        $produks = InventoryInden::get();
        // $no_mutasi = $this->generatemutasiNumber();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        $lokasi = Lokasi::all();
        $kondisis = Kondisi::all();
        // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')
        // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
        return view('mutasiindengh.edit',compact('data','suppliers','lokasi','produks','kondisis','barangmutasi','pembuat','jabatan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mutasiindens  $mutasiindens
     * @return \Illuminate\Http\Response
     */
    public function update_indengh(Request $request, $mutasiIG)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'tgl_diterima' => 'required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }
        // Simpan data ke tabel mutasiindens

        $mutasiinden = Mutasiindens::find($mutasiIG);
        if (!$mutasiinden) {
            return redirect()->back()->with('fail', 'Mutasi tidak ditemukan');
        }

    
        $mutasiinden->tgl_diterima = $request->tgl_diterima;     
        $mutasiinden->subtotal = $request->sub_total ?? null;
        $mutasiinden->biaya_perawatan = $request->biaya_rwt ?? null;
        $mutasiinden->biaya_pengiriman = $request->biaya_ongkir ?? null;
        $mutasiinden->total_biaya = $request->total_tagihan ?? null;
        $mutasiinden->penerima_id = $request->penerima;
        $mutasiinden->status_diterima = $request->status_diterima;
        $mutasiinden->tgl_diterima_ttd = $request->tgl_diterima_ttd;
        $mutasiinden->pembuku_id = $request->pembuku ?? null;
        $mutasiinden->status_dibukukan = $request->status_dibukukan ?? null;
        $mutasiinden->tgl_dibukukan = $request->tgl_dibukukan ?? null;
        $mutasiinden->pemeriksa_id = $request->pemeriksa ?? null;
        $mutasiinden->status_diperiksa = $request->status_diperiksa ?? null;
        $mutasiinden->tgl_diperiksa = $request->tgl_diperiksa ?? null;

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $fileName = $request->no_mutasi . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_mutasi_inden', $fileName, 'public');
            $mutasiinden->bukti = $filePath; // Simpan path file ke dalam model jika ada
        }
        // Tambahkan atribut lainnya sesuai kebutuhan
        $check1 = $mutasiinden->save();

        
        
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
