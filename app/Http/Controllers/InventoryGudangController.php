<?php

namespace App\Http\Controllers;

use App\Models\InventoryGudang;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kondisi;
use App\Models\Mutasi;
use App\Models\Lokasi;
use App\Models\Produk_Terjual;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class InventoryGudangController extends Controller
{
    public function index()
    {
        $data = InventoryGudang::all();
        $mutasigg = Mutasi::where('no_mutasi', 'LIKE', 'MPG%')->where('status', 'DIKONFIRMASI')->get();

        $riwayat = collect();

        if ($mutasigg) {
            $arraymutasi = $mutasigg->pluck('no_mutasi')->toArray();
    
            $produkTerjual = Produk_Terjual::whereIn('no_mutasigg', $arraymutasi)
                ->with('komponen')
                ->get();
    
            foreach ($produkTerjual as $produk) {
                $produkActivity = Activity::where('subject_type', Produk_Terjual::class)
                    ->where('subject_id', $produk->id)
                    ->where('description', 'created')
                    ->orderBy('id', 'desc')
                    ->first();
    
                if ($produkActivity) {
                    $produkActivity->jenis = 'Produk Terjual';
                    $produkActivity->komponen = $produk->komponen;
                    $riwayat->push($produkActivity);
                }
            }
        }
    
        $riwayat = $riwayat->sortByDesc('id')->values();
        return view('inven_gudang.index', compact('data', 'riwayat'));
    }

    public function create()
    {
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 4)->get();
        return view('inven_gudang.create', compact('produks', 'kondisi', 'gallery'));
    }

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
        // dd($data);
        // check duplikasi
        $duplicate = InventoryGudang::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryGudang::create($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_gudang.index'))->with('success', 'Data tersimpan');
    }
    
    public function edit($inventoryGudang)
    {
        $data = InventoryGudang::find($inventoryGudang);
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 4)->get();
        return view('inven_gudang.edit', compact('data', 'produks', 'kondisi', 'gallery'));
    }
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
        $duplicate = InventoryGudang::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->where('id', '!=', $inventoryGallery)->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryGudang::find($inventoryGallery)->update($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_gudang.index'))->with('success', 'Data tersimpan');
    }

    public function destroy($inventoryGallery)
    {
        $data = InventoryGudang::find($inventoryGallery);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function show($inventoryGudang)
    {
        $data = InventoryGudang::find($inventoryGudang);
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 3)->get();
        return view('inven_gudang.show', compact('data', 'produks', 'kondisi', 'gallery'));
    }
}
