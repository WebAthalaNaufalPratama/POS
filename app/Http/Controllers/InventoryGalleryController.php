<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\InventoryGallery;
use App\Models\Karyawan;
use App\Models\KembaliSewa;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\PemakaianSendiri;
use App\Models\Produk;
use App\Models\Produk_Terjual;
use App\Models\Produkbeli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InventoryGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produks = InventoryGallery::with('produk')->when(!Auth::user()->roles('admin'), function ($query) {
            return $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        })->orderBy('kode_produk')->orderBy('kondisi_id')->get();
        $karyawans = Karyawan::when(!Auth::user()->roles('admin'), function ($query) {
            return $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        })->get();
        $lokasis = Lokasi::where('tipe_lokasi', 1)->when(!Auth::user()->roles('admin'), function ($query) {
            return $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        })->get();
        $data = InventoryGallery::orderBy('kode_produk', 'asc')->orderBy('kondisi_id', 'asc')->when(!Auth::user()->roles('admin'), function ($query) {
            return $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        })->get();

        $pemakaian_sendiri = PemakaianSendiri::when(!Auth::user()->roles('admin'), function ($query) {
            return $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        })->orderBy('tanggal', 'desc')->orderByDesc('id')->get();


        // log
        // do sewa
        // $logDoSewa = DeliveryOrder::with('produk','produk.komponen', 'produk.komponen.activityLogs')->whereHas('kontrak', function($q){
        //     $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        // })->get();

        // $combinedActivityLogs = $logDoSewa->flatMap(function ($deliveryOrder) {
        //     $lokasiId = $deliveryOrder->kontrak->lokasi_id;
        //     return $deliveryOrder->produk->flatMap(function ($produk) use ($lokasiId) {
        //         return $produk->komponen->flatMap(function ($komponen) use ($lokasiId) {
        //             $logs = $komponen->activityLogs;

        //             // Tambahkan lokasi_id ke setiap log secara manual
        //             return $logs->map(function ($log) use ($lokasiId) {
        //                 $log->lokasi_id = $lokasiId; // Menambahkan properti lokasi_id ke log
        //                 return $log;
        //             });
        //         });
        //     });
        // });
        $isSuperAdmin = Auth::user()->hasRole('SuperAdmin');
        
        // $doSewa = DeliveryOrder::with('produk', 'produk.produk', 'produk.komponen', 'produk.komponen.kondisi', 'kontrak', 'kontrak.data_pembuat')->whereHas('kontrak', function($query) use ($isSuperAdmin) {
        //     if (!$isSuperAdmin) {
        //         $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        //     }
        // })->get();

        // $kblSewa = KembaliSewa::with('produk', 'produk.komponen', 'produk.komponen.kondisi', 'produk.produk', 'sewa', 'sewa.data_pembuat')->whereHas('sewa', function($query) use ($isSuperAdmin) {
        //     if (!$isSuperAdmin) {
        //         $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        //     }
        // })->get();

        $komponenDoSewa = Komponen_Produk_Terjual::with('data_kondisi', 'produk', 'produk_terjual', 'produk_terjual.do_sewa', 'produk_terjual.do_sewa.data_pembuat', 'produk_terjual.do_sewa.kontrak')->whereHas('produk_terjual', function($q) use($isSuperAdmin){
            return $q->whereHas('do_sewa', function($p) use($isSuperAdmin){
                return $p->whereHas('kontrak', function($z) use($isSuperAdmin){
                    if (!$isSuperAdmin) {
                        $z->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                    }
                });
            });
        })->get();
        $dataKomponen = $komponenDoSewa->map(function($komponen){
            return [
                'Id' => $komponen->produk_terjual->id,
                'Pengubah' => optional($komponen->produk_terjual->do_sewa->data_pembuat)->name,
                'No Referensi' => $komponen->produk_terjual->do_sewa->no_do ?? null,
                'Kode Produk Jual' => $komponen->produk_terjual->produk->kode ?? null,
                'Nama Produk Jual' => $komponen->produk_terjual->produk->nama ?? null,
                'Kode Komponen' => $komponen->kode_produk ?? null,
                'Nama Komponen' => $komponen->nama_produk ?? null,
                'Kondisi' => $komponen->data_kondisi->nama ?? null,
                'Masuk' => '-',
                'Keluar' => $komponen->jumlah * $komponen->produk_terjual->jumlah,
                'Waktu' => $komponen->updated_at
            ];
        });

        $komponenKblSewa = Komponen_Produk_Terjual::with('data_kondisi', 'produk', 'produk_terjual', 'produk_terjual.kembali_sewa', 'produk_terjual.kembali_sewa.data_pembuat', 'produk_terjual.kembali_sewa.sewa')->whereHas('produk_terjual', function($q) use($isSuperAdmin){
            return $q->whereHas('kembali_sewa', function($p) use($isSuperAdmin){
                return $p->whereHas('sewa', function($z) use($isSuperAdmin){
                    if (!$isSuperAdmin) {
                        $z->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                    }
                });
            });
        })->get();
        $dataKembaliSewa = $komponenKblSewa->map(function($komponen){
            return [
                'Id' => $komponen->produk_terjual->id,
                'Pengubah' => optional($komponen->produk_terjual->kembali_sewa->data_pembuat)->name,
                'No Referensi' => $komponen->produk_terjual->kembali_sewa->no_kembali ?? null,
                'Kode Produk Jual' => $komponen->produk_terjual->produk->kode ?? null,
                'Nama Produk Jual' => $komponen->produk_terjual->produk->nama ?? null,
                'Kode Komponen' => $komponen->kode_produk ?? null,
                'Nama Komponen' => $komponen->nama_produk ?? null,
                'Kondisi' => $komponen->data_kondisi->nama ?? null,
                'Masuk' => $komponen->jumlah * $komponen->produk_terjual->jumlah,
                'Keluar' => '-',
                'Waktu' => $komponen->updated_at
            ];
        });
        $mergedCollection = $dataKomponen->merge($dataKembaliSewa)->sortBy('Id');

        $dataPO = Produkbeli::whereHas('pembelian', function($q) use($isSuperAdmin){
            if (!$isSuperAdmin) {
                $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
            }
        })->get();
        $produkPO = $dataPO->map(function($produk){
            return [
                'Id' => $produk->id,
                'Pengubah' => optional($produk->pembelian->pembuat)->name,
                'No Referensi' => $produk->pembelian->no_po ?? null,
                'Kode Produk Jual' => '-',
                'Nama Produk Jual' => '-',
                'Kode Komponen' => $produk->produk->kode ?? null,
                'Nama Komponen' => $produk->produk->nama ?? null,
                'Kondisi' => $produk->kondisi->nama ?? null,
                'Masuk' => $produk->jml_diterima,
                'Keluar' => '-',
                'Waktu' => $produk->updated_at
            ];
        });
        $mergedCollection = $mergedCollection->merge($produkPO)->sortByDesc('Waktu');

        $dataMutasiMasuk = Produk_Terjual::whereHas('mutasi', function($q) use($isSuperAdmin){
            if (!$isSuperAdmin) {
                $q->where('penerima', Auth::user()->karyawans->lokasi_id);
            }
        })->get();
        $produkMutasiMasuk = $dataMutasiMasuk->map(function($produk){
            return [
                'Id' => $produk->id,
                'Pengubah' => optional($produk->mutasi->dibuat)->name,
                'No Referensi' => $produk->mutasi->no_mutasi ?? null,
                'Kode Produk Jual' => '-',
                'Nama Produk Jual' => '-',
                'Kode Komponen' => $produk->produk->kode ?? null,
                'Nama Komponen' => $produk->produk->nama ?? null,
                'Kondisi' => $produk->kondisi->nama ?? null,
                'Masuk' => $produk->jml_diterima,
                'Keluar' => '-',
                'Waktu' => $produk->updated_at
            ];
        });

        $mergedCollection = $mergedCollection->merge($produkMutasiMasuk)->sortByDesc('Waktu');

        $dataMutasiKeluar = Produk_Terjual::whereHas('mutasi', function($q) use($isSuperAdmin){
            if (!$isSuperAdmin) {
                $q->where('pengirim', Auth::user()->karyawans->lokasi_id);
            }
        })->get();
        $produkMutasiKeluar = $dataMutasiKeluar->map(function($produk){
            return [
                'Id' => $produk->id,
                'Pengubah' => optional($produk->mutasi->dibuat)->name,
                'No Referensi' => $produk->mutasi->no_mutasi ?? null,
                'Kode Produk Jual' => '-',
                'Nama Produk Jual' => '-',
                'Kode Komponen' => $produk->produk->kode ?? null,
                'Nama Komponen' => $produk->produk->nama ?? null,
                'Kondisi' => $produk->kondisi->nama ?? null,
                'Masuk' => '-',
                'Keluar' => $produk->jml_diterima,
                'Waktu' => $produk->updated_at
            ];
        });

        $mergedCollection = $mergedCollection->merge($produkMutasiKeluar)->sortByDesc('Waktu');
        // dd($mergedCollection);
        return view('inven_galeri.index', compact('data', 'produks', 'karyawans', 'lokasis', 'pemakaian_sendiri', 'mergedCollection'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 1)->when(!Auth::user()->roles('admin'), function ($query) {
            return $query->where('id', Auth::user()->karyawans->lokasi_id);
        })->get();
        return view('inven_galeri.create', compact('produks', 'kondisi', 'gallery'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kode_produk' => 'required',
            'kondisi_id' => 'required|integer',
            'lokasi_id' => 'required',
            'jumlah' => 'required',
            'min_stok' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // check duplikasi
        $duplicate = InventoryGallery::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryGallery::create($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_galeri.index'))->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function show($inventoryGallery)
    {
        $data = InventoryGallery::find($inventoryGallery);
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 1)->get();
        return view('inven_galeri.show', compact('data', 'produks', 'kondisi', 'gallery'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function edit($inventoryGallery)
    {
        $data = InventoryGallery::find($inventoryGallery);
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 1)->when(!Auth::user()->roles('admin'), function ($query) {
            return $query->where('id', Auth::user()->karyawans->lokasi_id);
        })->get();
        return view('inven_galeri.edit', compact('data', 'produks', 'kondisi', 'gallery'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $inventoryGallery)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kode_produk' => 'required',
            'kondisi_id' => 'required|integer',
            'lokasi_id' => 'required',
            'jumlah' => 'required',
            'min_stok' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // check duplikasi
        $duplicate = InventoryGallery::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->where('id', '!=', $inventoryGallery)->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryGallery::find($inventoryGallery)->update($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_galeri.index'))->with('success', 'Data tersimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function destroy($inventoryGallery)
    {
        $data = InventoryGallery::find($inventoryGallery);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
