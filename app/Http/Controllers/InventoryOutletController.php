<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\InventoryOutlet;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Produk;
use Spatie\Activitylog\Models\Activity;
use App\Models\Penjualan;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\DeliveryOrder;
use App\Models\Mutasi;
use App\Models\ReturPenjualan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class InventoryOutletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $namaproduks = InventoryOutlet::with('produk')->get()->unique('kode_produk');
        $outlets = Lokasi::where('tipe_lokasi', 2)->get();
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->first();
        if($user->hasRole(['KasirOutlet'])) {
            $arraylokasi = $lokasi->lokasi_id;
            $query = InventoryOutlet::where('lokasi_id', $arraylokasi);
            if ($req->produk) {
                $query->where('kode_produk', $req->input('produk'));
            }
            if ($req->outlet) {
                $query->where('lokasi_id', $req->input('outlet'));
            }
            $data = $query->get();
            $penjualan = Penjualan::where('lokasi_id', $arraylokasi)->where('distribusi', 'Diambil')->get() ?? null;
            $mergedriwayat =[];
            $do = DeliveryOrder::where('lokasi_pengirim', $arraylokasi)->get() ?? null;
            $retur = ReturPenjualan::where('lokasi_id', $arraylokasi)->get() ?? null;
            $mutasi = Mutasi::where('penerima', $arraylokasi)->where('no_mutasi', 'LIKE', 'MGO%')->get() ?? null;
            if(!empty($penjualan))
            {
                $arraypenjualan = $penjualan->pluck('no_invoice')->toArray();
                $produk = Produk_Terjual::whereIn('no_invoice', $arraypenjualan)->get();
                $arrayproduk = $produk->pluck('id')->toArray();
                $gabungan = new Collection();
                $riwayatpenj = Activity::where('subject_type', Produk_Terjual::class)->whereIn('subject_id', $arrayproduk)->orderBy('id', 'desc')->get();
                $mergedriwayat = array_merge($mergedriwayat, [
                    'riwayatpenj' => $riwayatpenj
                ]);
            }
            if(!empty($do)){
                $arraydo = $do->pluck('no_do')->toArray();
                $produk = Produk_Terjual::whereIn('no_do', $arraydo)->get();
                $arrayproduk = $produk->pluck('id')->toArray();
                $gabungan = new Collection();
                $riwayatdo = Activity::where('subject_type', Produk_Terjual::class)->whereIn('subject_id', $arrayproduk)->orderBy('id', 'desc')->get();
                $mergedriwayat = array_merge($mergedriwayat, [
                    'riwayatdo' => $riwayatdo
                ]);
            }
            if(!empty($retur)){
                $arrayretur = $retur->pluck('no_retur')->toArray();
                $produk = Produk_Terjual::whereIn('no_retur', $arrayretur)->get();
                $arrayproduk = $produk->pluck('id')->toArray();
                $gabungan = new Collection();
                $riwayatretur = Activity::where('subject_type', Produk_Terjual::class)->whereIn('subject_id', $arrayproduk)->orderBy('id', 'desc')->get();
                $mergedriwayat = array_merge($mergedriwayat, [
                    'riwayatretur' => $riwayatretur
                ]);
            }
            if(!empty($mutasi)){
                $arraymutasi = $mutasi->pluck('no_mutasi')->toArray();
                $produk = Produk_Terjual::whereIn('no_mutasigo', $arraymutasi)->get();
                $arrayproduk = $produk->pluck('id')->toArray();
                $gabungan = new Collection();
                $riwayatmutasi = Activity::where('subject_type', Produk_Terjual::class)->whereIn('subject_id', $arrayproduk)->where('description', 'updated')->orderBy('id', 'desc')->get();
                // dd($riwayatmutasi);
                $mergedriwayat = array_merge($mergedriwayat, [
                    'riwayatmutasi' => $riwayatmutasi
                ]);
            }
            
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
        }
        
        return view('inven_outlet.index', compact('data', 'riwayat', 'namaproduks', 'outlets'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $produks = Produk_Jual::all();
        $kondisi = Kondisi::all();
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $outlets = Lokasi::where('id', $karyawan->lokasi_id)->get();
        return view('inven_outlet.create', compact('produks', 'kondisi', 'outlets'));
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
            'lokasi_id' => 'required',
            'jumlah' => 'required',
            'min_stok' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // check duplikasi
        $duplicate = InventoryOutlet::where('kode_produk', $data['kode_produk'])->where('lokasi_id', $data['lokasi_id'])->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryOutlet::create($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect()->back()->withInput()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function show($inventoryGallery)
    {
        $data = InventoryOutlet::find($inventoryGallery);
        $produks = Produk_Jual::all();
        $kondisi = Kondisi::all();
        $outlets = Lokasi::where('tipe_lokasi', 2)->get();
        return view('inven_outlet.show', compact('data', 'produks', 'kondisi', 'outlets'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function edit($inventoryGallery)
    {
        $data = InventoryOutlet::find($inventoryGallery);
        $produks = Produk_Jual::all();
        $kondisi = Kondisi::all();
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $outlets = Lokasi::where('id', $karyawan->lokasi_id)->get();
        return view('inven_outlet.edit', compact('data', 'produks', 'kondisi', 'outlets'));
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
        $duplicate = InventoryOutlet::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->where('id', '!=', $inventoryGallery)->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryOutlet::find($inventoryGallery)->update($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_outlet.index'))->with('success', 'Data tersimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function destroy($inventoryGallery)
    {
        $data = InventoryOutlet::find($inventoryGallery);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
