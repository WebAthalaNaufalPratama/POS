<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use App\Models\Mutasiindens;
use App\Models\ProdukMutasiInden;
use Illuminate\Http\Request;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Karyawan;
use App\Models\Rekening;
use App\Models\Pembayaran;
use App\Models\Supplier;
use ProdukMutasiIndens;
use App\Models\Produk;
use App\Models\InventoryGallery;
use App\Models\InventoryGreenHouse;
use App\Models\InventoryGudang;
use App\Models\InventoryInden;
use App\Models\Produkreturinden;
use App\Models\Returinden;

class MutasiindensController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

     public function generateRefundIndenNumber() {
        $date = date('Ymd');  // Tanggal hari ini dalam format YYYYMMDD
        $prefix = 'RefundInden_' . $date . '_';
        $lastPayment = Pembayaran::where('no_invoice_bayar', 'like', $prefix . '%')
                        ->orderBy('no_invoice_bayar', 'desc')
                        ->first();
    
        if (!$lastPayment) {
            return $prefix . '001';
        }
    
        $lastNumber = intval(substr($lastPayment->no_invoice_bayar, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    
        return $prefix . $newNumber; 
    }

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

    public function generateReturNumber() {
        // Ambil tanggal hari ini dalam format 'Y-m-d'
        $tgl_today = date('Y-m-d');
        
        // Cari urutan terakhir nomor PO pada hari ini
        $last = Returinden::whereDate('created_at', $tgl_today)->orderBy('id', 'desc')->first();
    
        // Jika tidak ada nomor PO pada hari ini, urutan diinisialisasi dengan 1
        $urutan = 1;
        if ($last) {
            // Jika ada nomor PO pada hari ini, ambil urutan berikutnya
            $urutan = intval(substr($last->no_retur, -3)) + 1;
        }
    
        // Format nomor PO dengan pola 'PO_tgl_urutanpo'
        $no_retur = 'RMI_' . date('Ymd') . '_' . str_pad($urutan, 3, '0', STR_PAD_LEFT);
        
        return $no_retur;
    }

    public function generatebayarmutasiNumber() {
        $date = date('Ymd');  // Tanggal hari ini dalam format YYYYMMDD
        $prefix = 'BYMI_' . $date . '_';
        $lastPayment = Pembayaran::where('no_invoice_bayar', 'like', $prefix . '%')
                        ->orderBy('no_invoice_bayar', 'desc')
                        ->first();
    
        if (!$lastPayment) {
            return $prefix . '001';
        }
    
        $lastNumber = intval(substr($lastPayment->no_invoice_bayar, -3));
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
    
        return $prefix . $newNumber;
    }

    public function index_indengh(Request $req)
    {
        $query = Mutasiindens::query();

  
        $query->when(Auth::user()->hasRole('AdminGallery'), function($q){
            $q->where('status_dibuat', 'DIKONFIRMASI')
            ->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        });

        $query->when(Auth::user()->hasRole('Finance'), function($q){
            $q->where('status_diperiksa', 'DIKONFIRMASI');
            // ->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        });
    
    
        $query->when(Auth::user()->hasRole('Auditor'), function($q){
            $q->where('status_dibuat', 'DIKONFIRMASI')
            ->where(function($query) {
                $query->where('status_diterima', 'DIKONFIRMASI')
                ->orWhere('status_diterima', '-')
                        ->orWhere(function($subQuery) {
                            $subQuery->whereNull('status_diterima')
                                    ->whereHas('lokasi.tipe', function($lokasiQuery) {
                                        $lokasiQuery->whereIn('tipe_lokasi', [3, 4]);
                                    });
                        });
            });
        });
    
        $query->orderBy('created_at', 'desc');
  
        // if(Auth::user()->hasRole(['Auditor', 'AdminGallery'])){
        //     $query->where('status_dibuat', 'DIKONFIRMASI');
        // }

        if ($req->dateStart) {
            $query->where('tgl_dikirim', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_dikirim', '<=', $req->input('dateEnd'));
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
        $lokasi = Lokasi::whereIn('tipe_lokasi', [1, 3, 4])->get();
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

    public function getkategoriIndenEdit($kode_inden, $bulan_inden, $supplier_id)
    {
        // Ambil data kategori berdasarkan supplier_id, bulan_inden, dan kode_produk_inden
        $kategori = InventoryInden::where('supplier_id', $supplier_id)
            ->where('bulan_inden', $bulan_inden)
            ->where('kode_produk_inden', $kode_inden)
            ->with('produk') // Load relasi dengan produk
            ->first()->produk->nama; // Ambil kategori dari relasi dengan produk
       

        return response()->json($kategori);
        

    }
    public function getKategoriInden($kode_inden, $bulan_inden, $supplier_id)
    {
        // Ambil data kategori dan jumlah berdasarkan supplier_id, bulan_inden, dan kode_produk_inden
        $inden = InventoryInden::where('supplier_id', $supplier_id)
            ->where('bulan_inden', $bulan_inden)
            ->where('kode_produk_inden', $kode_inden)
            ->with('produk') // Load relasi dengan produk
            ->first();
        
        if ($inden) {
            $kategori = $inden->produk->nama; // Ambil kategori dari relasi dengan produk
            $jumlah = $inden->jumlah; // Ambil jumlah dari tabel InventoryInden
            $idinven = $inden->id; // Ambil jumlah dari tabel InventoryInden
            
            return response()->json([
                'kategori' => $kategori,
                'jumlah' => $jumlah,
                'idinven' => $idinven
            ]);
        }

        return response()->json(['error' => 'Data tidak ditemukan'], 404);
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
            'tgl_dikirim' => 'required|date',
            'sub_total' => 'required',
            'total_tagihan' => 'required',
            'pembuat' => 'required',
            'tgl_dibuat' => 'required',
            'status_dibuat' => 'required',
            // Tambahkan validasi sesuai kebutuhan lainnya
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }
        
         // Pengecekan stok sebelum menyimpan data ke tabel Mutasiindens
        foreach ($request->bulan_inden as $key => $bulanInden) {
            $inventoryInden = InventoryInden::where('kode_produk_inden', $request->kode_inden[$key])
                ->where('bulan_inden', $bulanInden)
                ->where('supplier_id', $request->supplier_id)
                ->first();

            if (!$inventoryInden) {
                return redirect()->back()->withInput()->with('fail', 'Tidak ditemukan record di InventoryInden');
            }

            if ($request->qtykrm[$key] > $inventoryInden->jumlah) {
                return redirect()->back()->withInput()->with('fail', 'stok di inden kurang');
            }
        }

        // Simpan data ke tabel mutasiindens
        $mutasiinden = new Mutasiindens();
        $no_mutasi = $mutasiinden->no_mutasi = $request->no_mutasi;
        $mutasiinden->supplier_id = $request->supplier_id;
        $mutasiinden->lokasi_id = $request->lokasi_id;
        $mutasiinden->tgl_dikirim = $request->tgl_dikirim;
        $mutasiinden->subtotal = $request->sub_total ?? null;
        $mutasiinden->biaya_perawatan = $request->biaya_rwt ?? null;
        $mutasiinden->biaya_pengiriman = $request->biaya_ongkir ?? null;
        $mutasiinden->total_biaya = $request->total_tagihan ?? null;
        $mutasiinden->sisa_bayar = $request->total_tagihan ?? null;
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
            $inventoryInden = InventoryInden::where('id', $request->idinven[$key])
                ->where('kode_produk_inden', $request->kode_inden[$key])
                ->where('bulan_inden', $bulanInden)
                ->where('supplier_id', $request->supplier_id)
                ->first();
            
            if(!$inventoryInden){
                return redirect()->back()->withInput()->with('fail', 'tidak ditemukan record di InventoryInden');
            }
            // $inventoryInden = InventoryInden::where('id', $request->idinven[$key])->first();

            if ($inventoryInden) {
                $produkMutasiInden = new ProdukMutasiInden();
                $produkMutasiInden->mutasiinden_id = $mutasiinden->id;
                $produkMutasiInden->inventoryinden_id = $request->idinven[$key];
                $produkMutasiInden->jml_dikirim = $request->qtykrm[$key];
                $produkMutasiInden->jml_diterima = $request->qtytrm[$key] ?? null;
                $produkMutasiInden->kondisi_id = $request->kondisi[$key] ?? null;
                $produkMutasiInden->biaya_rawat = $request->rawat[$key] ?? null;
                $produkMutasiInden->totalharga = $request->jumlah[$key] ?? null;
                // Tambahkan atribut lainnya sesuai kebutuhan
                $check1 = $produkMutasiInden->save();
                
                if($check1 && $request->status_dibuat == "DIKONFIRMASI"){ 
                    $inventoryInden->jumlah -= $request->qtykrm[$key];
                    $inventoryInden->update();
                }

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
            $no_bypo = $this->generatebayarmutasiNumber();
            // $no_mutasi = $this->generatemutasiNumber();
            $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
            $lokasi = Lokasi::all();
            $kondisis = Kondisi::all();
            $databayars = Pembayaran::where('mutasiinden_id', $data->id)->get()->sortByDesc('created_at');
            $rekenings = Rekening::all();

            //riwayat

            $riwayatPembelian = Activity::where('subject_type', Mutasiindens::class)->where('subject_id', $mutasiIG)->orderBy('id', 'desc')->get();
            $riwayatPembayaran = Activity::where('subject_type', Pembayaran::class)->orderBy('id', 'desc')->get();
            $produkIds = $data->pluck('id')->toArray();
            $filteredRiwayat = $riwayatPembayaran->filter(function (Activity $activity) use ($produkIds) {
                $properties = json_decode($activity->properties, true);
                return isset($properties['attributes']['mutasiinden_id']) && in_array($properties['attributes']['mutasiinden_id'], $produkIds);
            });
            
            $mergedriwayat = [
                'pembelian' => $riwayatPembelian,
                'pembayaran' => $filteredRiwayat
            ];
            
            $riwayat = collect($mergedriwayat)
                ->flatMap(function ($riwayatItem, $jenis) {
                    return $riwayatItem->map(function ($item) use ($jenis) {
                        $item->jenis = $jenis;
                        return $item;
                    });
                })
                ->sortByDesc('id')
                ->values()
                ->all();

            // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')
            // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
            return view('mutasiindengh.show',compact('riwayat','data','rekenings','no_bypo','databayars','suppliers','lokasi','produks','kondisis','barangmutasi','pembuat','penerima','pembuku','pemeriksa','jabatanbuat','jabatanterima','jabatanbuku','jabatanperiksa'));
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mutasiindens  $mutasiindens
     * @return \Illuminate\Http\Response
     */
    public function editpurchase_indengh($mutasiIG)
    {
        $data = Mutasiindens::with('produkmutasi')->where('id', $mutasiIG)->first();
        

        $pembuat = Karyawan::where('user_id',$data->pembuat_id)->value('nama');
        // return $pembuat;
        $jabatan = Karyawan::where('user_id',$data->pembuat_id)->value('jabatan');
        $barangmutasi = ProdukMutasiInden::where('mutasiinden_id',$data->id)->get();

        // return $barangmutasi;
        $produks = InventoryInden::get();
        // $no_mutasi = $this->generatemutasiNumber();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        $lokasi = Lokasi::whereIn('tipe_lokasi', [1, 3, 4])->get();
        $kondisis = Kondisi::all();
        $bulanInden = InventoryInden::where('supplier_id', $data->supplier_id)->pluck('bulan_inden')->unique()->values()->all();
        
        // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')
        // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
        return view('mutasiindengh.editpurchasemutasi',compact('data','suppliers','lokasi','produks','kondisis','barangmutasi','pembuat','jabatan', 'bulanInden'));
    }

    public function editfinance_indengh($mutasiIG)
    {
        $data = Mutasiindens::with('produkmutasi')->where('id', $mutasiIG)->first();
        

        $pembuat = Karyawan::where('user_id',$data->pembuat_id)->value('nama');
        $jabatanbuat = Karyawan::where('user_id',$data->pembuat_id)->value('jabatan');
        $penerima = Karyawan::where('user_id',$data->penerima_id)->value('nama');
        $jabatanterima = Karyawan::where('user_id',$data->penerima_id)->value('jabatan');
        $pembuku = Karyawan::where('user_id',$data->pembuku_id)->value('nama');
        $jabatanbuku = Karyawan::where('user_id',$data->pembuku_id)->value('jabatan');
        $pemeriksa = Karyawan::where('user_id',$data->pemeriksa_id)->value('nama');
        $jabatanperiksa = Karyawan::where('user_id',$data->pemeriksa_id)->value('jabatan');

        $barangmutasi = ProdukMutasiInden::where('mutasiinden_id',$data->id)->get();

        // return $barangmutasi;
        $produks = InventoryInden::get();
        // $no_mutasi = $this->generatemutasiNumber();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        $lokasi = Lokasi::whereIn('tipe_lokasi', [1, 3, 4])->get();
        $kondisis = Kondisi::all();
        $bulanInden = InventoryInden::where('supplier_id', $data->supplier_id)->pluck('bulan_inden')->unique()->values()->all();
        
        // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')
        // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
        return view('mutasiindengh.editfinance',compact('data','suppliers','lokasi','produks','kondisis','barangmutasi','pembuat','jabatanbuat','penerima','jabatanterima','pemeriksa','jabatanperiksa', 'bulanInden'));
    }

    public function edit_indengh($mutasiIG)
    {
        $data = Mutasiindens::where('id', $mutasiIG)->first();
        $pembuat = Karyawan::where('user_id',$data->pembuat_id)->value('nama');
        $penerima = Karyawan::where('user_id',$data->penerima_id)->value('nama');
        $pemeriksa = Karyawan::where('user_id',$data->pemeriksa_id)->value('nama');
        $pembuku = Karyawan::where('user_id',$data->pembuku_id)->value('nama');
        // return $pembuat;
        $jabatan = Karyawan::where('user_id',$data->pembuat_id)->value('jabatan');
        $jabatan_penerima = Karyawan::where('user_id',$data->penerima_id)->value('jabatan');
        $jabatan_pemeriksa = Karyawan::where('user_id',$data->pemeriksa_id)->value('jabatan');
        $jabatan_pembuku = Karyawan::where('user_id',$data->pembuku_id)->value('jabatan');
        $barangmutasi = ProdukMutasiInden::where('mutasiinden_id',$data->id)->get();
        // return $barangmutasi;
        $produks = InventoryInden::get();
        // $no_mutasi = $this->generatemutasiNumber();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        $lokasi = Lokasi::whereIn('tipe_lokasi', [1, 3, 4])->get();
        $kondisis = Kondisi::all();

        
        // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')
        // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
        return view('mutasiindengh.edit',compact('data','suppliers','lokasi','produks','kondisis','barangmutasi','pembuat','jabatan', 'penerima', 'jabatan_penerima', 'pemeriksa', 'jabatan_pemeriksa', 'pembuku', 'jabatan_pembuku'));
    }

    public function create_retur($mutasiIG)
    { 
        
        $data = Mutasiindens::where('id', $mutasiIG)->first();
        // $pembuat = Karyawan::where('user_id',$data->pembuat_id)->value('nama');
        // $jabatanbuat = Karyawan::where('user_id',$data->pembuat_id)->value('jabatan');
        // $penerima = Karyawan::where('user_id',$data->penerima_id)->value('nama');
        // $jabatanterima = Karyawan::where('user_id',$data->penerima_id)->value('jabatan');
        // $pembuku = Karyawan::where('user_id',$data->pembuku_id)->value('nama');
        // $jabatanbuku = Karyawan::where('user_id',$data->pembuku_id)->value('jabatan');
        // $pemeriksa = Karyawan::where('user_id',$data->pemeriksa_id)->value('nama');
        // $jabatanperiksa = Karyawan::where('user_id',$data->pemeriksa_id)->value('jabatan');

        $barangmutasi = ProdukMutasiInden::with('kondisi')->where('mutasiinden_id',$data->id)->get();
        // return $barangmutasi;
        $produks = InventoryInden::get();
        $no_bypo = $this->generatebayarmutasiNumber();
        $no_retur = $this->generateReturNumber();
        // $no_mutasi = $this->generatemutasiNumber();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        $lokasi = Lokasi::all();
        $kondisis = Kondisi::all();
        $databayars = Pembayaran::where('mutasiinden_id', $data->id)->get()->sortByDesc('created_at');
        $rekenings = Rekening::all();
        

        //riwayat

        $riwayatPembelian = Activity::where('subject_type', Mutasiindens::class)->where('subject_id', $mutasiIG)->orderBy('id', 'desc')->get();
        $riwayatPembayaran = Activity::where('subject_type', Pembayaran::class)->orderBy('id', 'desc')->get();
        $produkIds = $data->pluck('id')->toArray();
        $filteredRiwayat = $riwayatPembayaran->filter(function (Activity $activity) use ($produkIds) {
            $properties = json_decode($activity->properties, true);
            return isset($properties['attributes']['mutasiinden_id']) && in_array($properties['attributes']['mutasiinden_id'], $produkIds);
        });
        
        $mergedriwayat = [
            'pembelian' => $riwayatPembelian,
            'pembayaran' => $filteredRiwayat
        ];
        
        $riwayat = collect($mergedriwayat)
            ->flatMap(function ($riwayatItem, $jenis) {
                return $riwayatItem->map(function ($item) use ($jenis) {
                    $item->jenis = $jenis;
                    return $item;
                });
            })
            ->sortByDesc('id')
            ->values()
            ->all();

        // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')
        // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
        return view('mutasiindengh.retur',compact('riwayat','no_retur','data','rekenings','no_bypo','databayars','suppliers','lokasi','produks','kondisis','barangmutasi'));
    
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
        $validator = Validator::make($request->all(), []);

        // Validasi untuk peran Purchasing
        if (Auth::user() && Auth::user()->hasRole('Purchasing')) {
            $validator->validate([
                'tgl_dikirim' => 'required|date',
                'tgl_dibuat' => 'required|date',
                'status_dibuat' => 'required',
                'pembuat' => 'required',
                'supplier_id' => 'required',
                'lokasi_id' => 'required'
            ]);
        }

        // Validasi untuk peran AdminGallery
        if (Auth::user() && Auth::user()->hasRole('AdminGallery')) {
            $validator->validate([
                'tgl_diterima' => 'required|date',
                'tgl_diterima_ttd' => 'required|date',
                'status_diterima' => 'required',
                'penerima' => 'required'
            ]);
        }

        // Validasi untuk peran Auditor
        if (Auth::user() && Auth::user()->hasRole('Auditor')) {
            $validator->validate([
                'tgl_diperiksa' => 'required|date',
                'status_diperiksa' => 'required',
                'pemeriksa' => 'required'
            ]);
        }

        // // Validasi untuk peran Finance
        // if (Auth::user() && Auth::user()->hasRole('Finance')) {
        //     $validator->validate([
        //         'tgl_dibukukan' => 'required|date',
        //         'status_dibukukan' => 'required',
        //         'pembuku' => 'required'
        //     ]);
        // }

        // Cek apakah ada kesalahan validasi
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Lanjutkan dengan logika penyimpanan data lainnya
        // Simpan data ke tabel mutasiindens

        $mutasiinden = Mutasiindens::find($mutasiIG);
        if (!$mutasiinden) {
            return redirect()->back()->with('fail', 'Mutasi tidak ditemukan');
        }

        
    DB::beginTransaction();

    try {
    
        if(Auth::user()->hasRole('Auditor')){

            $mutasiinden->tgl_diterima = $request->tgl_diterima ?? null;  //pusat
            $mutasiinden->pemeriksa_id = $request->pemeriksa ?? null;
            $mutasiinden->status_diperiksa = $request->status_diperiksa ?? null;
            $mutasiinden->tgl_diperiksa = $request->tgl_diperiksa ?? null;
            
            if($mutasiinden->lokasi->tipe_lokasi == 3 || $mutasiinden->lokasi->tipe_lokasi == 4){
                $mutasiinden->penerima_id = $request->pemeriksa ?? null;
                $mutasiinden->status_diterima = $request->status_diperiksa ?? null;
                $mutasiinden->tgl_diterima_ttd = $request->tgl_diperiksa ?? null;
            }
        }
        if(Auth::user()->hasRole('AdminGallery')){
            $mutasiinden->tgl_diterima = $request->tgl_diterima ?? null;  
            $mutasiinden->penerima_id = $request->penerima ?? null;
            $mutasiinden->status_diterima = $request->status_diterima ?? null;
            $mutasiinden->tgl_diterima_ttd = $request->tgl_diterima_ttd ?? null;
        }
        if(Auth::user()->hasRole('Finance')){
            $mutasiinden->subtotal = $request->sub_total ?? null;
            $mutasiinden->biaya_pengiriman = $request->biaya_ongkir ?? null;
            $mutasiinden->biaya_perawatan = $request->biaya_rwt ?? null;
            $mutasiinden->total_biaya = $request->total_tagihan ?? null;
            $mutasiinden->sisa_bayar = $request->total_tagihan ?? null;
            $mutasiinden->pembuku_id = $request->pembuku ?? null;
            $mutasiinden->status_dibukukan = $request->status_dibukukan ?? null;
            $mutasiinden->tgl_dibukukan = $request->tgl_dibukukan ?? null;
        }
        if(Auth::user()->hasRole('Purchasing')){
            $mutasiinden->supplier_id = $request->supplier_id ?? null;     
            $mutasiinden->lokasi_id = $request->lokasi_id?? null;     
            $mutasiinden->tgl_dikirim = $request->tgl_dikirim ?? null;     
            $mutasiinden->subtotal = $request->sub_total ?? null;
            $mutasiinden->biaya_pengiriman = $request->biaya_ongkir ?? null;
            $mutasiinden->biaya_perawatan = $request->biaya_rwt ?? null;
            $mutasiinden->total_biaya = $request->total_tagihan ?? null;
            $mutasiinden->sisa_bayar = $request->total_tagihan ?? null;
            $mutasiinden->pembuat_id = $request->pembuat ?? null;
            $mutasiinden->status_dibuat = $request->status_dibuat ?? null;
            $mutasiinden->tgl_dibuat = $request->tgl_dibuat ?? null;
            
            if($mutasiinden->status_dibuat == "BATAL"){
                $mutasiinden->status_diterima = $request->status_dibuat;
                $mutasiinden->status_dibukukan = $request->status_dibuat;
                $mutasiinden->status_diperiksa = $request->status_dibuat;
            }
        }

        if ($request->hasFile('bukti')) {
            $file = $request->file('bukti');
            $fileName = $request->no_mutasi . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_mutasi_inden', $fileName, 'public');
            $mutasiinden->bukti = $filePath; // Simpan path file ke dalam model jika ada
        }
        // Tambahkan atribut lainnya sesuai kebutuhan
        $check1 = $mutasiinden->save();

        $produkIds = $request->id;
        $kode = $request->kode;
        $idinven = $request->idinven;
        $qty = $request->qtytrm; 
        $qty2 = $request->qtykrm;
        $kondisi = $request->kondisi;
        $rawat = $request->rawat;
        $jml = $request->jumlah;
        $bulan_inden = $request->bulan_inden;
        $kode_inden = $request->kode_inden;
    
        $check2 = true;
       
        
            foreach ($produkIds as $index => $produkId) {
                $produkmutasi = ProdukMutasiInden::find($produkId);

                if (!$produkmutasi) {
                    $check2 = false;
                    continue;
                }
                
                // if (Auth::user()->hasRole(['Auditor', 'AdminGallery'])) {
                //     // khusus yang menerima karena inventoryinden_id sudah ada di $produkmutasi
                //     $inveninden = InventoryInden::where('id', $produkmutasi->inventoryinden_id)->first();

                //     if ($inveninden && $inveninden->jumlah >= $qty[$index]) {
                //         $inveninden->jumlah += $qty2[$index];
                //         $inveninden->jumlah -= $qty[$index];
                //         $inveninden->save(); // Simpan perubahan jumlah ke database
                //     } elseif (!$inveninden || $inveninden->jumlah <= $qty[$index]) {
                //         throw new Exception('stok di inden kurang untuk produk: ' . $inveninden->kode_produk_inden .'/'. $inveninden->produk->nama);
                //     }
                // }

                //update inventory inden oleh purchasing ketika edit di purchase
                if (Auth::user()->hasRole(['Purchasing'])) {
                    // khusus purchasing ketika inventory_id berubah jadi sesuai requestnya $idinven
                    $id_inveninden = InventoryInden::where('id', $idinven[$index])->first();

                    if ($id_inveninden && $request->status_dibuat == "DIKONFIRMASI") {
                        if ($id_inveninden->jumlah >= $qty2[$index]) { // Periksa apakah jumlah cukup untuk dikurangi
                            $id_inveninden->jumlah -= $qty2[$index];
                            $id_inveninden->save(); // Simpan perubahan jumlah ke database
                        } else {
                            throw new Exception('stok di inden kurang untuk produk: ' . $id_inveninden->kode_produk_inden .'/'. $id_inveninden->produk->nama);
                        }
                    } 
                }

                

                $lokasi = Lokasi::find($mutasiinden->lokasi_id);
                $produk = Produk::where('kode', $request->kode[$index])->first(); // khusus penerima

                if ($lokasi && $produk) {
                    if (Auth::user()->hasRole(['AdminGallery'])) {
                        if ($lokasi->tipe_lokasi == 1) {
                            $checkInven = InventoryGallery::where('kode_produk', $produk->kode)
                                ->where('kondisi_id', $kondisi[$index])
                                ->where('lokasi_id', $lokasi->id)
                                ->first();
                            if ($checkInven) {
                                $checkInven->jumlah += $qty[$index];
                                $checkInven->update();
                            } else {
                                $createProduk = new InventoryGallery();
                                $createProduk->kode_produk = $produk->kode;
                                $createProduk->kondisi_id = $kondisi[$index];
                                $createProduk->jumlah = $qty[$index];
                                $createProduk->lokasi_id = $lokasi->id;
                                $createProduk->save();
                            }
                        }
                    } elseif (Auth::user()->hasRole(['Auditor'])) {

                        if ($lokasi->tipe_lokasi == 1) {
                            $checkInvenLama = InventoryGallery::where('kode_produk', $produk->kode)
                                ->where('kondisi_id', $produkmutasi->kondisi_id)
                                ->where('lokasi_id', $lokasi->id)
                                ->first();
                            if ($checkInvenLama) {
                                $checkInvenLama->jumlah -= $produkmutasi->jml_diterima;
                                $checkInvenLama->update();
                            }

                            $checkInvenBaru = InventoryGallery::where('kode_produk', $produk->kode)
                                ->where('kondisi_id', $kondisi[$index])
                                ->where('lokasi_id', $lokasi->id)
                                ->first();

                            if ($checkInvenBaru) {
                                $checkInvenBaru->jumlah += $qty[$index];
                                $checkInvenBaru->update();
                            } else {
                                $createProduk = new InventoryGallery();
                                $createProduk->kode_produk = $produk->kode;
                                $createProduk->kondisi_id = $kondisi[$index];
                                $createProduk->jumlah = $qty[$index];
                                $createProduk->lokasi_id = $lokasi->id;
                                $createProduk->save();
                            }
                    
                        } elseif ($lokasi->tipe_lokasi == 3) {
                            $checkInven = InventoryGreenHouse::where('kode_produk', $produk->kode)
                                ->where('kondisi_id', $kondisi[$index])
                                ->where('lokasi_id', $lokasi->id)
                                ->first();
                            if ($checkInven) {
                                $checkInven->jumlah += $qty[$index];
                                $checkInven->update();
                            } else {
                                $createProduk = new InventoryGreenHouse();
                                $createProduk->kode_produk = $produk->kode;
                                $createProduk->kondisi_id = $kondisi[$index];
                                $createProduk->jumlah = $qty[$index];
                                $createProduk->lokasi_id = $lokasi->id;
                                $createProduk->save();
                            }
                        } elseif ($lokasi->tipe_lokasi == 4) {
                            $checkInven = InventoryGudang::where('kode_produk', $produk->kode)
                                ->where('kondisi_id', $kondisi[$index])
                                ->where('lokasi_id', $lokasi->id)
                                ->first();
                            if ($checkInven) {
                                $checkInven->jumlah += $qty[$index];
                                $checkInven->update();
                            } else {
                                $createProduk = new InventoryGudang();
                                $createProduk->kode_produk = $produk->kode;
                                $createProduk->kondisi_id = $kondisi[$index];
                                $createProduk->jumlah = $qty[$index];
                                $createProduk->lokasi_id = $lokasi->id;
                                $createProduk->save();
                            }
                        }
                    }
                }

                if (Auth::user()->hasRole(['Purchasing','Finance'])) {
                    $produkmutasi->inventoryinden_id = $idinven[$index];
                    $produkmutasi->jml_dikirim = $qty2[$index];
                    $produkmutasi->biaya_rawat = $rawat[$index];
                    $produkmutasi->totalharga = $jml[$index];
                    $check2 = $produkmutasi->save();
                }
                if (Auth::user()->hasRole(['Auditor', 'AdminGallery'])) {
                    $produkmutasi->jml_diterima = $qty[$index];
                    $produkmutasi->kondisi_id = $kondisi[$index];
                    $check2 = $produkmutasi->save();
                }
            }
        
        //     DB::commit();

        // } catch (Exception $e) {
        //     DB::rollBack();
        //     return redirect()->back()->withInput()->with('fail', $e->getMessage());
        // }

    
        // if (!$check1 || !$check2) {
        //     return redirect()->back()->withInput()->with('fail', 'Gagal mengupdate data');
        // } else {
        //     return redirect(route('mutasiindengh.show',['mutasiIG' => $mutasiIG]))->with('success', 'Data berhasil diupdate');
        // }

        
        DB::commit();

        return redirect(route('mutasiindengh.show',['mutasiIG' => $mutasiIG]))->with('success', 'Data berhasil diupdate');
        
    } catch (Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->with('fail', 'Gagal update mutasi inden: ' . $e->getMessage());
    }

        
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

    public function updatePembuku(Request $request, $mutasiIG)
    {
    // Validasi input
    $request->validate([
        'pembuku' => 'required|exists:users,id',
        'status_dibukukan' => 'required|string',
        'tgl_dibukukan' => 'required|date'
    ]);

    // Temukan data mutasi berdasarkan ID
    $mutasiinden = Mutasiindens::find($mutasiIG);
    if (!$mutasiinden) {
        return redirect()->back()->with('fail', 'Mutasi tidak ditemukan');
    }
    

    // Mulai transaksi database
    DB::beginTransaction();
    
    try {
        // Simpan data
        $mutasiinden->pembuku_id = $request->pembuku; // ID pembuku
        $mutasiinden->status_dibukukan = $request->status_dibukukan; // Status dibukukan
        $mutasiinden->tgl_dibukukan = $request->tgl_dibukukan; // Tanggal dibukukan

        // Simpan perubahan
        $mutasiinden->save();
        
        // Commit transaksi
        DB::commit();

        return redirect(route('mutasiindengh.show', ['mutasiIG' => $mutasiIG]))->with('success', 'Data berhasil diperbarui');
        
    } catch (\Exception $e) {
        // Rollback jika terjadi kesalahan
        DB::rollBack();
        return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data: ' . $e->getMessage());
    }
    }

    public function updatePembukuRetur(Request $request, $idretur)
    {
    // Validasi input
    $request->validate([
        'pembuku' => 'required|exists:users,id',
        'status_dibukukan' => 'required|string',
        'tgl_dibukukan' => 'required|date'
    ]);

    // Temukan data mutasi berdasarkan ID
    $returinden = Returinden::find($idretur);
    if(!$returinden) {
        return redirect()->back()->with('fail', 'Mutasi tidak ditemukan');
    }
    

    // Mulai transaksi database
    DB::beginTransaction();
    
    try {
        // Simpan data
        $returinden->pembuku_id = $request->pembuku; // ID pembuku
        $returinden->status_dibukukan = $request->status_dibukukan; // Status dibukukan
        $returinden->tgl_dibukukan = $request->tgl_dibukukan; // Tanggal dibukukan

        // Simpan perubahan
        $returinden->save();
        
        // Commit transaksi
        DB::commit();

        return redirect(route('returinden.index'))->with('success', 'Data berhasil diperbarui');
        
    } catch (\Exception $e) {
        // Rollback jika terjadi kesalahan
        DB::rollBack();
        return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data: ' . $e->getMessage());
    }
    }

    

    public function store_retur(Request $request)
    {
        // dd($request);
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'no_retur' => 'required|string',
            'mutasiinden_id' => 'required|integer',
            'kode_inden_retur' => 'required',
            'kode_inden_retur.*' => 'required|string',
            'produk_mutasi_inden_id' => 'required|array',
            'produk_mutasi_inden_id.*' => 'required|integer',
            'alasan' => 'required|array',
            'alasan.*' => 'required|string',
            'jml_diretur' => 'required|array',
            'jml_diretur.*' => 'required|integer|min:1',
            'harga_satuan' => 'required|array',
            'harga_satuan.*' => 'required|integer|min:1',
            'totalharga' => 'required|array',
            'totalharga.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }

        // Check apakah mutasiinden_id sudah ada sebelumnya
        if (Returinden::where('mutasiinden_id', $request->mutasiinden_id)->exists()) {
            return redirect()->back()->withInput()->with('fail', 'Data sudah ada.');
        }

        $validated = $validator->validated();
        
   // Log struktur data yang divalidasi
//    dd($validated);
//    Log::info('Struktur data yang divalidasi:', $validated);
        // Mulai transaksi
        DB::beginTransaction();

        try {
            $mutasi = Mutasiindens::where('id', $request->mutasiinden_id)->first();
            $lokasi_id = $mutasi->lokasi_id;
            $lokasi = Lokasi::find($lokasi_id);

            // Buat entri baru di tabel Returinden
            $returinden = Returinden::create([
                'no_retur' => $request->no_retur,
                'tipe_komplain' => "Refund",
                'mutasiinden_id' => $request->mutasiinden_id,
                'refund' => $request->refund,
                'sisa_refund' => $request->refund,
                'total_akhir' => 0,
                'pembuat_id' => $request->pembuat_id,
                'status_dibuat' => $request->status_dibuat,
                'tgl_dibuat' => $request->tgl_dibuat,
            ]);

            if ($request->hasFile('file_retur')) {
                $file = $request->file('file_retur');
                $fileName = $request->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('bukti_retur_inden', $fileName, 'public');
                $returinden->foto = $filePath; // Simpan path file ke dalam model jika ada
                $returinden->save(); // Simpan perubahan ke dalam model setelah mengatur foto
            }

           if($mutasi->sisa_bayar == $mutasi->total_biaya){
                $returinden->total_akhir = $request->total_akhir;
                $returinden->tipe_komplain = "Diskon";
                $returinden->sisa_refund = 0;
                $returinden->save();
            }
            //update jumlah bayar khusus belum lunas dan dilakukan oleh finance 

            // if ($mutasi->sisa_bayar == $mutasi->total_biaya && $request->status_dibuat == "DIKONFIRMASI") {
            //     $mutasi->sisa_bayar = $returinden->total_akhir;
            //     $mutasi->update();  // Memanggil update() pada objek model Mutasiindens
            // }


            // Loop melalui data produk dan masukkan ke tabel Produkreturinden
            foreach ($validated['produk_mutasi_inden_id'] as $index => $produk_mutasi_inden_id) {

            // Log::info("Memasukkan produk returing: index={$index}, produk_mutasi_inden_id={$produk_mutasi_inden_id}");
                
            $check = Produkreturinden::create([
                    'returinden_id' => $returinden->id,
                    'produk_mutasi_inden_id' => $produk_mutasi_inden_id,
                    'alasan' => $validated['alasan'][$index],
                    'jml_diretur' => $validated['jml_diretur'][$index],
                    'harga_satuan' => $validated['harga_satuan'][$index],
                    'totalharga' => $validated['totalharga'][$index],
                ]);

                //pengurangan dilakukan setelah acc finance

            // if($request->status_dibuat == "DIKONFIRMASI"){
            //     $produkmutasi = ProdukMutasiInden::where('id', $produk_mutasi_inden_id)->first();
            //     $kondisi = $produkmutasi->kondisi_id;
            //     $kode_produk = $produkmutasi->produk->kode_produk;

            //     if ($lokasi && $kode_produk) {
            //         if ($lokasi->tipe_lokasi == 1) {
            //             $checkInven = InventoryGallery::where('kode_produk', $kode_produk)
            //                 ->where('kondisi_id', $kondisi)
            //                 ->where('lokasi_id', $lokasi->id)
            //                 ->first();
            //             if ($checkInven) {
            //                 $checkInven->jumlah -= $validated['jml_diretur'][$index];
            //                 $checkInven->update();
            //             }
            //         } elseif ($lokasi->tipe_lokasi == 3) {
            //             $checkInven = InventoryGreenHouse::where('kode_produk', $kode_produk)
            //                 ->where('kondisi_id', $kondisi)
            //                 ->where('lokasi_id', $lokasi->id)
            //                 ->first();
            //             if ($checkInven) {
            //                 $checkInven->jumlah -= $validated['jml_diretur'][$index];
            //                 $checkInven->update();
            //             }
            //         } elseif ($lokasi->tipe_lokasi == 4) {
            //             $checkInven = InventoryGudang::where('kode_produk', $kode_produk)
            //                 ->where('kondisi_id', $kondisi)
            //                 ->where('lokasi_id', $lokasi->id)
            //                 ->first();
            //             if ($checkInven) {
            //                 $checkInven->jumlah -= $validated['jml_diretur'][$index];
            //                 $checkInven->update();
            //             }
            //         }
            //         }
            // }

        }

            // Commit transaksi jika tidak ada error
            DB::commit();

            return redirect()->route('show.returinden', ['mutasiIG' => $returinden->mutasiinden_id])->with('success', 'Data retur berhasil disimpan.');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    
    public function index_returinden(Request $req)
    {
        $query = Returinden::query();
    

        $query->when(Auth::user()->hasRole('Finance'), function($q){
            $q->where('status_dibuat', 'DIKONFIRMASI');
            // ->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        });
    
    
        $query->orderBy('tgl_dibuat', 'desc');

        if ($req->dateStart) {
            $query->where('tgl_dibuat', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_dibuat', '<=', $req->input('dateEnd'));
        }
        $returs = $query->get();

        return view('mutasiindengh.returindex', compact('returs'));
    } 

    public function show_returinden($mutasiIG)
    { 
        
        $data = Mutasiindens::where('id', $mutasiIG)->first();
        $dataretur = Returinden::with('produkreturinden')->where('mutasiinden_id',$data->id)->first();

        $pembuat = Karyawan::where('user_id', $dataretur->pembuat_id)->value('nama');
        $jabatanbuat = Karyawan::where('user_id', $dataretur->pembuat_id)->value('jabatan');
       
        $pembuku = Karyawan::where('user_id',$dataretur->pembuku_id)->value('nama') ?? null;
        $jabatanbuku = Karyawan::where('user_id',$dataretur->pembuku_id)->value('jabatan') ?? null;
       

        $barangmutasi = ProdukMutasiInden::with('kondisi')->where('mutasiinden_id',$data->id)->get();
        $barangretur = Produkreturinden::with('produk')->where('returinden_id',$dataretur->id)->get();
        // return $barangmutasi;
        $produks = InventoryInden::get();
        $no_bypo = $this->generatebayarmutasiNumber();
        $no_retur = $this->generateReturNumber();
        $no_bayar = $this->generateRefundIndenNumber();
        // $no_mutasi = $this->generatemutasiNumber();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        $lokasi = Lokasi::all();
        $kondisis = Kondisi::all();
        $databayars = Pembayaran::where('mutasiinden_id', $data->id)->get()->sortByDesc('created_at');
        $rekenings = Rekening::all();
        

        //riwayat

        $riwayatPembelian = Activity::where('subject_type', Mutasiindens::class)->where('subject_id', $mutasiIG)->orderBy('id', 'desc')->get();
        $riwayatPembayaran = Activity::where('subject_type', Pembayaran::class)->orderBy('id', 'desc')->get();
        $produkIds = $data->pluck('id')->toArray();
        $filteredRiwayat = $riwayatPembayaran->filter(function (Activity $activity) use ($produkIds) {
            $properties = json_decode($activity->properties, true);
            return isset($properties['attributes']['mutasiinden_id']) && in_array($properties['attributes']['mutasiinden_id'], $produkIds);
        });
        
        $mergedriwayat = [
            'pembelian' => $riwayatPembelian,
            'pembayaran' => $filteredRiwayat
        ];
        
        $riwayat = collect($mergedriwayat)
            ->flatMap(function ($riwayatItem, $jenis) {
                return $riwayatItem->map(function ($item) use ($jenis) {
                    $item->jenis = $jenis;
                    return $item;
                });
            })
            ->sortByDesc('id')
            ->values()
            ->all();

        // $pembayarans = Pembayaran::where('no_invoice_bayar','LIKE','%','MUTIN')->where('mutasiinden_id','')
        // return view('mutasiindengh.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
        return view('mutasiindengh.showretur',compact('riwayat','no_bayar','barangretur','dataretur','no_retur','data','rekenings','no_bypo','databayars','suppliers','lokasi','produks','kondisis','barangmutasi','pembuat','pembuku','jabatanbuat','jabatanbuku'));
    
    }

    public function edit_retur($idretur){

        $dataretur = Returinden::with('produkreturinden')->where('id',$idretur)->first();
        $data = Mutasiindens::where('id', $dataretur->mutasiinden_id)->first();

        $pembuat = Karyawan::where('user_id', $dataretur->pembuat_id)->value('nama');
        $jabatanbuat = Karyawan::where('user_id', $dataretur->pembuat_id)->value('jabatan');
       
        $pembuku = Karyawan::where('user_id',$dataretur->pembuku_id)->value('nama') ?? null;
        $jabatanbuku = Karyawan::where('user_id',$dataretur->pembuku_id)->value('jabatan') ?? null;
       

        $barangmutasi = ProdukMutasiInden::with('kondisi')->where('mutasiinden_id',$data->id)->get();
        $barangretur = Produkreturinden::with('produk')->where('returinden_id',$dataretur->id)->get();
        // return $barangmutasi;
        $produks = InventoryInden::get();

        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        $lokasi = Lokasi::all();
        $kondisis = Kondisi::all();
        $databayars = Pembayaran::where('mutasiinden_id', $data->id)->get()->sortByDesc('created_at');
        $rekenings = Rekening::all();
        

        return view('mutasiindengh.editretur',compact('data','dataretur','pembuat','jabatanbuat','pembuku','jabatanbuku','barangmutasi','barangretur','produks','suppliers','lokasi','kondisis','databayars','rekenings'));

    }

    public function update_retur(Request $request, $idretur)
    {
        // dd($request);
        // Validasi data yang diterima
        $validator = Validator::make($request->all(), [
            'no_retur' => 'required|string',
            'mutasiinden_id' => 'required|integer',
            'kode_inden_retur' => 'required',
            'kode_inden_retur.*' => 'required|string',
            'produk_mutasi_inden_id' => 'required|array',
            'produk_mutasi_inden_id.*' => 'required|integer',
            'alasan' => 'required|array',
            'alasan.*' => 'required|string',
            'jml_diretur' => 'required|array',
            'jml_diretur.*' => 'required|integer|min:1',
            'harga_satuan' => 'required|array',
            'harga_satuan.*' => 'required|integer|min:1',
            'totalharga' => 'required|array',
            'totalharga.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }
              
        // Check apakah Returinden dengan id yang diberikan ada
        $retur = Returinden::find($idretur);
        if (!$retur) {
            return redirect()->back()->withInput()->with('fail', 'Data tidak ditemukan.');
        }


        $validated = $validator->validated();
        
   // Log struktur data yang divalidasi
//    dd($validated);
//    Log::info('Struktur data yang divalidasi:', $validated);
        // Mulai transaksi
        DB::beginTransaction();

        try {

            Produkreturinden::where('returinden_id', $retur->id)->delete();
           
            $mutasi = Mutasiindens::find($request->mutasiinden_id);
            $lokasi_id = $mutasi->lokasi_id;
            $lokasi = Lokasi::find($lokasi_id);


            if (Auth::user()->hasRole('Purchasing')) {
                 if($retur->tipe_komplain == "Refund"){
                     $retur->update([
                         'no_retur' => $request->no_retur,
                         'tipe_komplain' => "Refund",
                         'mutasiinden_id' => $request->mutasiinden_id,
                         'refund' => $request->refund,
                         'sisa_refund' => $request->refund,
                         'total_akhir' => 0,
                         'pembuat_id' => $request->pembuat_id,
                         'status_dibuat' => $request->status_dibuat,
                         'tgl_dibuat' => $request->tgl_dibuat,
                     ]);
                }elseif($retur->tipe_komplain == "Diskon"){
                    $retur->update([
                        'no_retur' => $request->no_retur,
                        'tipe_komplain' => "Diskon",
                        'mutasiinden_id' => $request->mutasiinden_id,
                        'refund' => $request->refund,
                        'sisa_refund' => 0,
                        'total_akhir' => $request->total_akhir,
                        'pembuat_id' => $request->pembuat_id,
                        'status_dibuat' => $request->status_dibuat,
                        'tgl_dibuat' => $request->tgl_dibuat,
                    ]);
               }
                
            } elseif(Auth::user()->hasRole('Finance')) {
                if($retur->tipe_komplain == "Refund"){
                    $retur->update([
                        'no_retur' => $request->no_retur,
                        'tipe_komplain' => "Refund",
                        'mutasiinden_id' => $request->mutasiinden_id,
                        'refund' => $request->refund,
                        'sisa_refund' => $request->refund,
                        'total_akhir' => 0,
                        'pembuku_id' => $request->pembuku_id,
                        'status_dibukukan' => $request->status_dibukukan,
                        'tgl_dibukukan' => $request->tgl_dibukukan,
                    ]);
                }elseif($retur->tipe_komplain == "Diskon"){
                    $retur->update([
                        'no_retur' => $request->no_retur,
                        'tipe_komplain' => "Diskon",
                        'mutasiinden_id' => $request->mutasiinden_id,
                        'refund' => $request->refund,
                        'sisa_refund' => 0,
                        'total_akhir' => $request->total_akhir,
                        'pembuku_id' => $request->pembuku_id,
                        'status_dibukukan' => $request->status_dibukukan,
                        'tgl_dibukukan' => $request->tgl_dibukukan,
                    ]);
                }
            }

            if($retur->status_dibuat == "BATAL"){
                $retur->status_dibukukan = $request->status_dibuat;
            }
            
                // Upload file retur jika ada
            if ($request->hasFile('file_retur')) {
                $file = $request->file('file_retur');
                $fileName = $request->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('bukti_retur_inden', $fileName, 'public');
                $retur->foto = $filePath;
            }

               // Save perubahan pada returinden
            $retur->save();

            // if ($retur->tipe_komplain == "Diskon") {
            //     $retur->sisa_refund = 0;
            //     $retur->save();
            // }
        
            // Update jumlah bayar khusus belum lunas dan dilakukan oleh finance
            if ($retur->tipe_komplain == "Diskon" && $request->status_dibukukan == "MENUNGGU PEMBAYARAN") {
                $mutasi->sisa_bayar = $mutasi->total_biaya - $request->refund;
                $mutasi->save();
            }


                
            // Loop melalui data produk dan masukkan ke tabel Produkreturinden
        foreach ($validated['produk_mutasi_inden_id'] as $index => $produk_mutasi_inden_id) {
                Produkreturinden::create([
                    'returinden_id' => $retur->id,
                    'produk_mutasi_inden_id' => $produk_mutasi_inden_id,
                    'alasan' => $validated['alasan'][$index],
                    'jml_diretur' => $validated['jml_diretur'][$index],
                    'harga_satuan' => $validated['harga_satuan'][$index],
                    'totalharga' => $validated['totalharga'][$index],
                ]);
            

                //pengurangan dilakukan setelah acc finance

            if($request->status_dibukukan == "MENUNGGU PEMBAYARAN"){
                $produkmutasi = ProdukMutasiInden::where('id', $produk_mutasi_inden_id)->first();
                $kondisi = $produkmutasi->kondisi_id;
                $kode_produk = $produkmutasi->produk->kode_produk;

                if ($lokasi && $kode_produk) {
                    if ($lokasi->tipe_lokasi == 1) {
                        $checkInven = InventoryGallery::where('kode_produk', $kode_produk)
                            ->where('kondisi_id', $kondisi)
                            ->where('lokasi_id', $lokasi->id)
                            ->first();
                        if ($checkInven) {
                            $checkInven->jumlah -= $validated['jml_diretur'][$index];
                            $checkInven->update();
                        }
                    } elseif ($lokasi->tipe_lokasi == 3) {
                        $checkInven = InventoryGreenHouse::where('kode_produk', $kode_produk)
                            ->where('kondisi_id', $kondisi)
                            ->where('lokasi_id', $lokasi->id)
                            ->first();
                        if ($checkInven) {
                            $checkInven->jumlah -= $validated['jml_diretur'][$index];
                            $checkInven->update();
                        }
                    } elseif ($lokasi->tipe_lokasi == 4) {
                        $checkInven = InventoryGudang::where('kode_produk', $kode_produk)
                            ->where('kondisi_id', $kondisi)
                            ->where('lokasi_id', $lokasi->id)
                            ->first();
                        if ($checkInven) {
                            $checkInven->jumlah -= $validated['jml_diretur'][$index];
                            $checkInven->update();
                        }
                    }
                    }
            }

        }

            // Commit transaksi jika tidak ada error
            DB::commit();

            return redirect()->route('returinden.index')->with('success', 'Data retur berhasil diperbarui.');
        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi error
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Gagal mengupdate data: ' . $e->getMessage());
        }
    }

    

}
