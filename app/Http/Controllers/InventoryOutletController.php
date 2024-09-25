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
use App\Models\Tipe_Produk;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
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
        $produks = InventoryOutlet::with('produk.tipe', 'outlet')->orderBy('kode_produk')->orderBy('kondisi_id')->get();
        $uniqueProduks = $produks->groupBy('kode_produk')->map(function ($items) {
            return [
                'kode_produk' => $items->first()->kode_produk,
                'nama_produk' => $items->first()->produk->nama
            ];
        })->values();
        $tipe_produks = Tipe_Produk::where('kategori', 'Jual')->get();
        $namaproduks = InventoryOutlet::with('produk')->get()->unique('kode_produk');
        $outlets = Lokasi::where('tipe_lokasi', 2)->get();
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->first();

        // start datatable inventory
            if ($req->ajax() && $req->table == 'inventory') {
                $query = InventoryOutlet::with('produk', 'outlet')->orderBy('kode_produk', 'asc');
                
                if ($req->has('produk') && !empty($req->produk)) {
                    $query->whereIn('kode_produk', $req->produk);
                }
                if ($req->filled('tipe_produk')) {
                    $query->whereHas('produk', function ($q) use ($req) {
                        $q->whereIn('tipe_produk', (array) $req->tipe_produk);
                    });
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
                        ->orWhereHas('outlet', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('produk', function($c) use($search){
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
                    'recordsTotal' => InventoryOutlet::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                    'total_jumlah' => $total_jumlah,
                ]);

            }
        // end datatable inventory

        if($user->hasRole(['KasirOutlet', 'Auditor', 'Finance', 'SuperAdmin'])) {
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
        
        return view('inven_outlet.index', compact('riwayat', 'namaproduks', 'outlets', 'uniqueProduks', 'produks', 'tipe_produks'));
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
        $outlets = Lokasi::where('tipe_lokasi', 2)->when(Auth::user()->hasRole('KasirOutlet'), function ($query) {
            return $query->where('id', Auth::user()->karyawans->lokasi_id);
        })->get();
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
            'kode_produk' => 'required|array',
            'lokasi_id' => 'required|array',
            'jumlah' => 'required|array',
            'min_stok' => 'required|array',
            'kode_produk.*' => 'required',
            'lokasi_id.*' => 'required|integer',
            'jumlah.*' => 'required|integer|min:1',
            'min_stok.*' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        DB::beginTransaction();

        try {
            foreach ($req->kode_produk as $index => $kode_produk) {
                $lokasi_id = $req->lokasi_id[$index];
                $jumlah = $req->jumlah[$index];
                $min_stok = $req->min_stok[$index];

                // Cek duplikat
                $duplicate = InventoryOutlet::where('kode_produk', $kode_produk)
                    ->where('lokasi_id', $lokasi_id)
                    ->first();

                if ($duplicate) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', "Produk {$kode_produk} dengan lokasi tersebut sudah ada.");
                }

                // Simpan data
                InventoryOutlet::create([
                    'kode_produk' => $kode_produk,
                    'lokasi_id' => $lokasi_id,
                    'jumlah' => $jumlah,
                    'min_stok' => $min_stok,
                ]);
            }

            DB::commit();
            return redirect(route('inven_outlet.index'))->with('success', 'Data berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
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
