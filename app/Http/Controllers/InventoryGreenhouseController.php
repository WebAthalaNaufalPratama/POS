<?php

namespace App\Http\Controllers;

use App\Models\InventoryGreenHouse;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Mutasi;
use App\Models\Pembelian;
use Illuminate\Support\Collection;
use App\Models\Produk_Terjual;
use App\Models\Produkbeli;
use App\Models\Komponen_Produk_Terjual;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class InventoryGreenhouseController extends Controller
{
    public function index()
    {
        $data = InventoryGreenHouse::all();
        $mutasigg = Mutasi::where('no_mutasi', 'LIKE', 'MGG%')->where('status', 'DIKONFIRMASI')->get();
        $lokasi = Lokasi::where('tipe_lokasi', 3)->get();
        $arraylokasi = $lokasi->pluck('id')->toArray();
        $mutasimasukgg = Mutasi::where(function ($query) {
            $query->where('no_mutasi', 'LIKE', 'MPG%');
        })
        ->whereIn('penerima', $arraylokasi)
        ->where('status', 'DIKONFIRMASI')
        ->get();
        $pomasukgg = Pembelian::whereIn('lokasi_id', $arraylokasi)
                ->whereNotNull('status_diperiksa')
                ->whereNotNull('tgl_diperiksa')
                ->where('status_diperiksa', 'DIKONFIRMASI')
                ->with('produkbeli')->get();        

        $riwayat = collect();

        if ($mutasigg) {
            $arraymutasi = $mutasigg->pluck('no_mutasi')->toArray();
    
            $produkTerjual = Produk_Terjual::whereIn('no_mutasigg', $arraymutasi)
                ->with('komponen')
                ->get();
    
            foreach ($produkTerjual as $produk) {
                $produkActivity = Activity::where('subject_type', Produk_Terjual::class)
                    ->where('subject_id', $produk->id)
                    ->orderBy('id', 'desc')
                    ->first();
    
                if ($produkActivity) {
                    $produkActivity->jenis = 'Produk Terjual';
                    $produkActivity->komponen = $produk->komponen;
                    $riwayat->push($produkActivity);
                }
            }
        }
        if ($mutasimasukgg) {
            $arraymutasi = $mutasimasukgg->pluck('no_mutasi')->toArray();
    
            $produkTerjual = Produk_Terjual::whereIn('no_mutasigg', $arraymutasi)->whereNotNull('jumlah_diterima')
                ->with('komponen')
                ->get();
    
            foreach ($produkTerjual as $produk) {
                $produkActivity = Activity::where('subject_type', Produk_Terjual::class)
                    ->where('subject_id', $produk->id)
                    ->orderBy('id', 'desc')
                    ->first();
    
                if ($produkActivity) {
                    $produkActivity->jenis = 'Produk Terjual';
                    $produkActivity->komponen = $produk->komponen;
                    $riwayat->push($produkActivity);
                }
            }
        }
        if($pomasukgg) {
            foreach ($pomasukgg as $produk) {
                $produkActivity = Activity::where('subject_type', Produkbeli::class)
                    ->where('subject_id', $produk->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($produkActivity) {
                    $produkActivity->jenis = 'Produk Beli';
                    $produkActivity->komponen = $produk->produkbeli;
                    $riwayat->push($produkActivity);
                }
            }

        }
    
        $riwayat = $riwayat->sortByDesc('id')->values();
        // dd($riwayat);
        return view('inven_greenhouse.index', compact('data', 'riwayat'));
    }

    public function create()
    {
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 3)->get();
        return view('inven_greenhouse.create', compact('produks', 'kondisi', 'gallery'));
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
        $duplicate = InventoryGreenHouse::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryGreenHouse::create($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_greenhouse.index'))->with('success', 'Data tersimpan');
    }
    
    public function edit($inventoryGreenhouse)
    {
        $data = InventoryGreenHouse::find($inventoryGreenhouse);
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 3)->get();
        return view('inven_greenhouse.edit', compact('data', 'produks', 'kondisi', 'gallery'));
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
        $duplicate = InventoryGreenHouse::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->where('id', '!=', $inventoryGallery)->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryGreenHouse::find($inventoryGallery)->update($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_greenhouse.index'))->with('success', 'Data tersimpan');
    }

    public function destroy($inventoryGallery)
    {
        $data = InventoryGreenHouse::find($inventoryGallery);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function show($inventoryGreenhouse)
    {
        $data = InventoryGreenHouse::find($inventoryGreenhouse);
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 3)->get();
        return view('inven_greenhouse.show', compact('data', 'produks', 'kondisi', 'gallery'));
    }
}
