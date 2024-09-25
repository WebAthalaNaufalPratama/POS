<?php

namespace App\Http\Controllers;

use App\Models\InventoryGudang;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kondisi;
use App\Models\Mutasi;
use App\Models\Lokasi;
use App\Models\Pembelian;
use App\Models\Produk_Terjual;
use App\Models\Tipe_Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class InventoryGudangController extends Controller
{
    public function index(Request $req)
    {
        $produks = InventoryGudang::with('kondisi', 'produk.tipe', 'gallery')->orderBy('kode_produk')->orderBy('kondisi_id')->get();
        $uniqueProduks = $produks->groupBy('kode_produk')->map(function ($items) {
            return [
                'kode_produk' => $items->first()->kode_produk,
                'nama_produk' => $items->first()->produk->nama
            ];
        })->values();
        $namaproduks = InventoryGudang::with('produk')->get()->unique('kode_produk');
        $tipe_produks = Tipe_Produk::where('kategori', 'Master')->get();
        $kondisis = Kondisi::all();
        $gudangs = Lokasi::where('tipe_lokasi', 4)->get();

        // start datatable inventory
            if ($req->ajax() && $req->table == 'inventory') {
                $query = InventoryGudang::with('produk', 'gallery', 'kondisi')->orderBy('kode_produk', 'asc')->orderBy('kondisi_id', 'asc');
                
                if ($req->has('produk') && !empty($req->produk)) {
                    $query->whereIn('kode_produk', $req->produk);
                }
                if ($req->filled('tipe_produk')) {
                    $query->whereHas('produk.tipe', function ($q) use ($req) {
                        $q->whereIn('id', (array) $req->tipe_produk);
                    });
                }
                if ($req->has('kondisi') && !empty($req->kondisi)) {
                    $query->whereIn('kondisi_id', $req->kondisi);
                }
                if ($req->has('lokasi') && !empty($req->lokasi)) {
                    $query->whereIn('lokasi_id', $req->lokasi);
                }
            
                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $query->where(function($q) use ($search) {
                        $q->where('jumlah', 'like', "%$search%")
                        ->orWhere('kode_produk', 'like', "%$search%")
                        ->orWhereHas('gallery', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('produk', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('kondisi', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        });
                    });
                }
        
                $query->orderBy($columnName, $dir);
                $recordsFiltered = $query->count();
                $tempData = $query->offset($start)->limit($length)->get();
        
                $currentPage = ($start / $length) + 1;
                $perPage = $length;
            
                $data = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
                    $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                    $item->min_stok = $item->min_stok ?? 0;
                    $item->tipe_produk = $item->produk->tipe->nama;
                    return $item;
                });
                $total_jumlah = $data->sum('jumlah');

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => InventoryGudang::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                    'total_jumlah' => $total_jumlah,
                ]);

            }
        // end datatable inventory

        $query = InventoryGudang::query();
        if ($req->produk) {
            $query->where('kode_produk', $req->input('produk'));
        }
        if ($req->kondisi) {
            $query->where('kondisi_id', $req->input('kondisi'));
        }
        if ($req->gallery) {
            $query->where('lokasi_id', $req->input('gallery'));
        }
        $data = $query->get();
        $lokasi = Lokasi::where('tipe_lokasi', 4)->get();
        $arraylokasi = $lokasi->pluck('id')->toArray();
        $mutasigg = Mutasi::where('no_mutasi', 'LIKE', 'MPG%')->where('status', 'DIKONFIRMASI')->get();
        $mutasimasukgg = Mutasi::where(function ($query) {
            $query->Where('no_mutasi', 'LIKE', 'MGG%');
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
                $produkActivity = Activity::where('subject_type', Pembelian::class)
                    ->where('subject_id', $produk->id)
                    ->orderBy('id', 'desc')
                    ->first();

                if ($produkActivity) {
                    $produkActivity->jenis = 'Produk Beli';
                    $produkActivity->produkbeli = $produk->produkbeli;
                    $riwayat->push($produkActivity);
                }
            }

        }
    
        $riwayat = $riwayat->sortByDesc('id')->values();
        return view('inven_gudang.index', compact('data', 'riwayat', 'namaproduks', 'kondisis', 'gudangs', 'uniqueProduks', 'tipe_produks', 'produks'));
    }

    public function create()
    {
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gudangs = Lokasi::where('tipe_lokasi', 4)->get();
        return view('inven_gudang.create', compact('produks', 'kondisi', 'gudangs'));
    }

    public function store(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kode_produk.*' => 'required',
            'kondisi_id.*' => 'required|integer',
            'lokasi_id.*' => 'required',
            'jumlah.*' => 'required|integer|min:1',
            'min_stok.*' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        DB::beginTransaction();

        try {
            foreach ($req->kode_produk as $index => $kode_produk) {
                $kondisi_id = $req->kondisi_id[$index];
                $lokasi_id = $req->lokasi_id[$index];
                $jumlah = $req->jumlah[$index];
                $min_stok = $req->min_stok[$index];

                // Cek duplikat
                $duplicate = InventoryGudang::where('kode_produk', $kode_produk)
                ->where('kondisi_id', $kondisi_id)
                ->where('lokasi_id', $lokasi_id)
                ->first();

                if ($duplicate) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', "Produk {$kode_produk} dengan kondisi dan lokasi tersebut sudah ada.");
                }

                // Simpan data
                InventoryGudang::create([
                    'kode_produk' => $kode_produk,
                    'kondisi_id' => $kondisi_id,
                    'lokasi_id' => $lokasi_id,
                    'jumlah' => $jumlah,
                    'min_stok' => $min_stok,
                ]);
            }

            DB::commit();
            return redirect(route('inven_gudang.index'))->with('success', 'Data berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
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

    public function ubahKondisi(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'produk_id' => 'required|array',
            'kondisi_akhir' => 'required|array',
            'jumlah' => 'required|array',
            'produk_id.*' => 'required|exists:inventory_gudangs,id',
            'kondisi_akhir.*' => 'required|integer|exists:kondisis,id',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        foreach ($req->produk_id as $index => $produk_id) {
            $stok = $req->input("jumlah.$index");
            $produk = InventoryGudang::find($produk_id);

            // cek stok apakah mencukupi
            if (!$produk || $produk->jumlah < $stok) {
                return redirect()->back()->withInput()->with('fail', "Stok tidak mencukupi untuk produk : " . $produk->produk->nama);
            }
        }

        DB::beginTransaction();

        try {
            foreach ($req->produk_id as $index => $produk_id) {
                $kondisi_akhir_id = $req->input("kondisi_akhir.$index");
                $jumlah = $req->input("jumlah.$index");

                $produk = InventoryGudang::find($produk_id);

                $produk->update([
                    'jumlah' => $produk->jumlah - $jumlah
                ]);

                $inventory = InventoryGudang::where('kode_produk', $produk->kode_produk)
                ->where('kondisi_id', $kondisi_akhir_id)
                ->where('lokasi_id', $produk->lokasi_id)
                ->first();

                if ($inventory) {
                    $inventory->update([
                        'jumlah' => $inventory->jumlah + $jumlah
                    ]);
                } else {
                    InventoryGudang::create([
                        'kode_produk' => $produk->kode_produk,
                        'kondisi_id' => $kondisi_akhir_id,
                        'lokasi_id' => $produk->lokasi_id,
                        'jumlah' => $jumlah,
                        'min_stok' => $produk->min_stok,
                    ]);
                }
            }
            DB::commit();
            return redirect(route('inven_gudang.index'))->with('success', 'Data tersimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}
