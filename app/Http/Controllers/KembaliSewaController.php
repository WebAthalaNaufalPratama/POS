<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\InventoryGallery;
use App\Models\Karyawan;
use App\Models\KembaliSewa;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kondisi;
use App\Models\Kontrak;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;

class KembaliSewaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $query = KembaliSewa::query();
        if(Auth::user()->hasRole('AdminGallery')){
            $query->whereHas('sewa', function($q) {
                $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
            });
        }
        if ($req->customer) {
            $query->whereHas('sewa',function($q) use($req){
                $q->where('customer_id', $req->input('customer'));
            });
        }
        if ($req->driver) {
            $query->where('driver', $req->input('driver'));
        }
        if ($req->dateStart) {
            $query->where('tanggal_kembali', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kembali', '<=', $req->input('dateEnd'));
        }
        if(Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Auditor')){
            $query->where('status', 'DIKONFIRMASI');
        }
       
        if ($req->ajax()) {
            $start = $req->input('start');
            $length = $req->input('length');
            $order = $req->input('order')[0]['column'];
            $dir = $req->input('order')[0]['dir'];
            $columnName = $req->input('columns')[$order]['data'];

            // search
            $search = $req->input('search.value');
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('no_kembali', 'like', "%$search%")
                    ->orWhere('no_sewa', 'like', "%$search%")
                    ->orWhere('tanggal_kembali', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('tanggal_pembuat', 'like', "%$search%")
                    ->orWhere('tanggal_penyetuju', 'like', "%$search%")
                    ->orWhere('tanggal_pemeriksa', 'like', "%$search%")
                    ->orWhereHas('sewa', function($c) use($search){
                        $c->whereHas('customer', function($d) use($search){
                            $d->where('nama', 'like', "%$search%");
                        });
                    })
                    ->orWhereHas('data_driver', function($c) use($search){
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
                $item->tanggal_kembali_format = $item->tanggal_kembali == null ? '-' : tanggalindo($item->tanggal_kembali);
                $item->tanggal_pembuat_format = $item->tanggal_pembuat == null ? '-' : tanggalindo($item->tanggal_pembuat);
                $item->tanggal_penyetuju_format = $item->tanggal_penyetuju == null ? '-' : tanggalindo($item->tanggal_penyetuju);
                $item->tanggal_pemeriksa_format = $item->tanggal_pemeriksa == null ? '-' : tanggalindo($item->tanggal_pemeriksa);
                $item->nama_customer = $item->sewa->customer->nama;
                $item->nama_driver = $item->data_driver->nama;
                $item->userRole = Auth::user()->getRoleNames()->first();
                $item->hasKembaliSewa = KembaliSewa::where('no_sewa', $item->no_sewa)->where('status', 'DIKONFIRMASI')->exists();
                return $item;
            });

            return response()->json([
                'draw' => $req->input('draw'),
                'recordsTotal' => KembaliSewa::count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);

        }

        $customer = Kontrak::whereHas('kembali_sewa')->select('customer_id')
        ->distinct()
        ->join('customers', 'kontraks.customer_id', '=', 'customers.id')
        ->when(Auth::user()->hasRole('AdminGallery'), function ($query) {
            return $query->where('customers.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('customers.nama')
        ->get();
        $driver = DeliveryOrder::select('driver')
        ->distinct()
        ->join('karyawans', 'delivery_orders.driver', '=', 'karyawans.id')
        ->when(Auth::user()->hasRole('AdminGallery'), function ($query) {
            return $query->where('karyawans.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('karyawans.nama')
        ->get();
        return view('kembali_sewa.index', compact('driver', 'customer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kontrak' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'route']);

        // data
        $kontrak = Kontrak::with('produk')->find($data['kontrak']);
        $do = DeliveryOrder::with('produk', 'produk.komponen', 'produk.produk')->where('no_referensi', $kontrak->no_kontrak)->get();
        $drivers = Karyawan::where('jabatan', 'Driver')->when(Auth::user()->hasRole('AdminGallery'), function ($query) {
            return $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        })->get();
        $produkjuals = Produk_Jual::all();
        $kondisi = Kondisi::all();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $latestKembali = KembaliSewa::withTrashed()->orderByDesc('id')->first();
        $detail_lokasi = Produk_Terjual::whereNotNull('detail_lokasi')->whereHas('do_sewa', function($q) use($kontrak){
            $q->where('no_referensi', $kontrak->no_kontrak);
        })->get()->unique('detail_lokasi');

        // kode kembali sewa
        if (!$latestKembali) {
            $getKode = 'KMB' . date('Ymd') . '00001';
        } else {
            $lastDate = substr($latestKembali->no_kembali, 3, 8);
            $todayDate = date('Ymd');
            if ($lastDate != $todayDate) {
                $getKode = 'KMB' . date('Ymd') . '00001';
            } else {
                $lastNumber = substr($latestKembali->no_kembali, -5);
                $nextNumber = str_pad((int)$lastNumber + 1, 5, '0', STR_PAD_LEFT);
                $getKode = 'KMB' . date('Ymd') . $nextNumber;
            }
        }
        return view('kembali_sewa.create', compact('kontrak', 'drivers', 'produkjuals', 'getKode', 'produkSewa', 'do', 'kondisi', 'detail_lokasi'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        // Validasi
        $validator = Validator::make($req->all(), [
            'no_kembali' => 'required',
            'no_sewa' => 'required',
            'tanggal_kembali' => 'required',
            'tanggal_driver' => 'required',
            'driver' => 'required',
            'no_do_produk' => 'required',
            'namaKomponen' => 'required',
            'kondisiKomponen' => 'required',
            'jumlahKomponen' => 'required',
            'jumlah' => 'required',
            'lokasi' => 'required',
            'file' => 'required|file',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        // Ambil data
        $data = $req->except(['_token', '_method']);
        $data['status'] = 'TUNDA';
        $data['tanggal_pembuat'] = now();
        $data['pembuat'] = Auth::user()->id;

        DB::beginTransaction();
        try {
            // Cek produk dan kuantitas dari sewa
            $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_sewa'])->firstOrFail();
            $produkSewa = $kontrak->produk()->whereHas('produk')->get();

            $inputProduk = [];
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $kodeProduk = $data['nama_produk'][$i];
                $jumlahProduk = $data['jumlah'][$i];
                if (!isset($inputProduk[$kodeProduk])) {
                    $inputProduk[$kodeProduk] = 0;
                }
                $inputProduk[$kodeProduk] += $jumlahProduk;
            }

            $sesuaiKontrak = $produkSewa->every(function ($produk) use (&$inputProduk) {
                $kodeProduk = $produk->produk->kode;
                if (isset($inputProduk[$kodeProduk])) {
                    $inputProduk[$kodeProduk] -= $produk->jumlah;
                    if ($inputProduk[$kodeProduk] < 0) return false;
                    if ($inputProduk[$kodeProduk] == 0) unset($inputProduk[$kodeProduk]);
                }
                return true;
            });

            if (!empty($inputProduk) || !$sesuaiKontrak) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Produk tidak sesuai kontrak');
            }

            // Cek jika ada DO
            $do_terbuat = DeliveryOrder::with('produk')->where('no_referensi', $kontrak->no_kontrak)->get();
            if ($do_terbuat->isEmpty()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Belum ada DO yang terbuat');
            }

            // Ambil barang DO dan jadikan satu
            $produkDO = $do_terbuat->flatMap(function ($item) {
                return $item->produk()->whereNull('no_kembali_sewa')->whereNull('jenis')->whereHas('produk')->get();
            })->groupBy('produk_jual_id')->map(function ($group) {
                return [
                    'produk_jual_id' => $group->first()->produk_jual_id,
                    'kode' => $group->first()->produk->kode,
                    'jumlah' => $group->sum('jumlah'),
                ];
            });

            // Kurangi DO dengan kembali sewa
            $kembali_sewa = KembaliSewa::with('produk')->where('no_sewa', $data['no_sewa'])->get();
            $produkDO = $produkDO->map(function ($item) use ($kembali_sewa) {
                foreach ($kembali_sewa as $sewa) {
                    foreach ($sewa->produk as $produk_sewa) {
                        if ($item['produk_jual_id'] == $produk_sewa->produk_jual_id) {
                            $item['jumlah'] -= intval($produk_sewa->jumlah);
                        }
                    }
                }
                return $item;
            });

            if ($produkDO->every(fn($item) => $item['jumlah'] == 0)) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Barang sudah kembali semua');
            }

            // Kurangi sisa barang DO dengan input
            $produkDO = $produkDO->map(function ($item) use ($data) {
                for ($i = 0; $i < count($data['nama_produk']); $i++) {
                    if ($item['kode'] == $data['nama_produk'][$i]) {
                        $item['jumlah'] -= intval($data['jumlah'][$i]);
                    }
                }
                return $item;
            });

            if ($produkDO->contains(fn($item) => $item['jumlah'] < 0)) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Jumlah barang tidak sesuai');
            }

            // store file
            if ($req->hasFile('file')) {
                // Simpan file baru
                $file = $req->file('file');
                $fileName = $req->no_kembali . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = 'bukti_kembali_sewa/' . $fileName;
            
                // Optimize dan simpan file baru
                Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                    ->save(storage_path('app/public/' . $filePath));
            
                // Hapus file lama
                // if (!empty($pembayaran->bukti)) {
                //     $oldFilePath = storage_path('app/public/' . $pembayaran->bukti);
                //     if (File::exists($oldFilePath)) {
                //         File::delete($oldFilePath);
                //     }
                // }
            
                // Verifikasi penyimpanan file baru
                if (File::exists(storage_path('app/public/' . $filePath))) {
                    $data['file'] = $filePath;
                } else {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                }
            }

            // Simpan data kembali
            $check = KembaliSewa::create($data);
            if (!$check) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }

            // Simpan produk kembali
            $startIdx = 0;
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->firstOrFail();
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_do' => $data['no_do_produk'][$i],
                    'no_kembali_sewa' => $check->no_kembali,
                    'jumlah' => $data['jumlah'][$i],
                    'detail_lokasi' => $data['lokasi'][$i],
                    'jenis' => 'KEMBALI_SEWA'
                ]);

                if (!$produk_terjual) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }

                // Simpan komponen produk terjual
                $endIdx = $startIdx + $data['indexKomponen'][$i];
                $namaKomponen = array_slice($data['namaKomponen'], $startIdx, $data['indexKomponen'][$i]);
                $kondisiKomponen = array_slice($data['kondisiKomponen'], $startIdx, $data['indexKomponen'][$i]);
                $jumlahKomponen = array_slice($data['jumlahKomponen'], $startIdx, $data['indexKomponen'][$i]);
                $startIdx = $endIdx;

                foreach ($namaKomponen as $index => $komponenKode) {
                    $komponen = Produk::where('kode', $komponenKode)->firstOrFail();
                    $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                        'produk_terjual_id' => $produk_terjual->id,
                        'kode_produk' => $komponenKode,
                        'nama_produk' => $komponen->nama,
                        'tipe_produk' => $komponen->tipe_produk,
                        'kondisi' => $kondisiKomponen[$index],
                        'deskripsi' => $komponen->deskripsi,
                        'jumlah' => $jumlahKomponen[$index],
                        'harga_satuan' => 0,
                        'harga_total' => 0
                    ]);

                    if (!$komponen_produk_terjual) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    }
                }
            }
            DB::commit();
            return redirect(route('kembali_sewa.index'))->with('success', 'Data tersimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\KembaliSewa  $kembaliSewa
     * @return \Illuminate\Http\Response
     */
    public function show($kembaliSewa)
    {
        // data
        $data = KembaliSewa::with('produk', 'produk.komponen', 'produk.produk')->find($kembaliSewa);
        $data2 = Produk_Terjual::with('produk', 'komponen', 'kembali_sewa')->where('no_kembali_sewa', $data->no_kembali)->get();
        // dd($data2[0]);
        $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_sewa'])->first();
        $do = DeliveryOrder::with('produk', 'produk.komponen', 'produk.produk')->where('no_referensi', $kontrak->no_kontrak)->get();
        $drivers = Karyawan::where('jabatan', 'Driver')->get();
        $produkjuals = Produk_Jual::all();
        $kondisi = Kondisi::all();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $latestKembali = KembaliSewa::withTrashed()->orderByDesc('id')->get();
        $detail_lokasi = Produk_Terjual::whereNotNull('detail_lokasi')->whereHas('do_sewa', function($q) use($kontrak){
            $q->where('no_referensi', $kontrak->no_kontrak);
        })->get();
        $riwayat = Activity::where('subject_type', KembaliSewa::class)->where('subject_id', $kembaliSewa)->orderBy('id', 'desc')->get();

        // kode do
        if(count($latestKembali) < 1){
            $getKode = 'KMB' . date('Ymd') . '00001';
        } else {
            $lastKembali = $latestKembali->first();
            $kode = substr($lastKembali->no_do, -5);
            $getKode = 'KMB' . date('Ymd') . str_pad((int)$kode + 1, 5, '0', STR_PAD_LEFT);
        }
        return view('kembali_sewa.show', compact('kontrak', 'drivers', 'produkjuals', 'getKode', 'produkSewa', 'do', 'kondisi', 'detail_lokasi', 'data', 'riwayat', 'data2'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\KembaliSewa  $kembaliSewa
     * @return \Illuminate\Http\Response
     */
    public function edit($kembaliSewa)
    {
        // data
        $data = KembaliSewa::with('produk', 'produk.komponen', 'produk.produk')->find($kembaliSewa);
        $data2 = Produk_Terjual::with('produk', 'komponen', 'kembali_sewa')->where('no_kembali_sewa', $data->no_kembali)->get();
        $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_sewa'])->first();
        $do = DeliveryOrder::with('produk', 'produk.komponen', 'produk.produk')->where('no_referensi', $kontrak->no_kontrak)->get();
        $drivers = Karyawan::where('jabatan', 'Driver')->get();
        $produkjuals = Produk_Jual::all();
        $kondisi = Kondisi::all();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $latestKembali = KembaliSewa::withTrashed()->orderByDesc('id')->get();
        $detail_lokasi = Produk_Terjual::whereNotNull('detail_lokasi')->whereHas('do_sewa', function($q) use($kontrak){
            $q->where('no_referensi', $kontrak->no_kontrak);
        })->get();
        $riwayat = Activity::where('subject_type', KembaliSewa::class)->where('subject_id', $kembaliSewa)->orderBy('id', 'desc')->get();
        return view('kembali_sewa.edit', compact('kontrak', 'drivers', 'produkjuals', 'produkSewa', 'do', 'kondisi', 'detail_lokasi', 'data', 'riwayat', 'data2'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\KembaliSewa  $kembaliSewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $kembaliSewa)
    {
        if($req->konfirmasi){
            $kembaliSewa = KembaliSewa::with('produk.komponen')->find($kembaliSewa);
            if($req->konfirmasi == 'confirm'){
                $kembaliSewa->status = 'DIKONFIRMASI';
                // update stok
                if(Auth::user()->hasRole('AdminGallery')){
                    foreach ($kembaliSewa->produk as $produk) {
                        foreach ($produk->komponen as $komponen) {
                            if(in_array($komponen->tipe_produk, [1, 2])){
                                $stok = InventoryGallery::where('lokasi_id', $kembaliSewa->sewa->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                                if(!$stok){
                                    $stok = InventoryGallery::create([
                                        'kode_produk' => $komponen->kode_produk,
                                        'kondisi_id' => $komponen->kondisi,
                                        'lokasi_id' => $kembaliSewa->sewa->lokasi_id,
                                        'jumlah' => 0,
                                        'min_stok' => 20,
                                    ]);
                                }
                                $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($produk->jumlah));
                                $stok->update();
                            }
                        }
                    }
                }
                $msg = 'Dikonfirmasi';
            } else if($req->konfirmasi == 'cancel'){
                $kembaliSewa->status = 'BATAl';
                $msg = 'Dibatalkan';
            } else {
                return redirect()->back()->withInput()->with('fail', 'Status tidak sesuai');
            }
            if(Auth::user()->hasRole('Auditor')){
                $kembaliSewa->penyetuju = Auth::user()->id;
                $kembaliSewa->tanggal_penyetuju = $req->tanggal_penyetuju;
            }
            if(Auth::user()->hasRole('Finance')){
                $kembaliSewa->pemeriksa = Auth::user()->id;
                $kembaliSewa->tanggal_pemeriksa = $req->tanggal_pemeriksa;
            }
            $check = $kembaliSewa->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal mengubah status');
            return redirect()->back()->withInput()->with('success', 'Data Berhasil ' . $msg);
        } else {
            // validasi
            $validator = Validator::make($req->all(), [
                'no_kembali' => 'required',
                'no_sewa' => 'required',
                'tanggal_kembali' => 'required',
                // 'tanggal_driver' => 'required',
                'driver' => 'required',
                'no_do_produk' => 'required',
                'namaKomponen' => 'required',
                'kondisiKomponen' => 'required',
                'jumlahKomponen' => 'required',
                'jumlah' => 'required',
                'lokasi' => 'required',
            ]);
            $error = $validator->errors()->all();
            if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
            $data = $req->except(['_token', '_method']);

            $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_sewa'])->first();

            // old data
            $oldData = KembaliSewa::with('produk.komponen')->find($kembaliSewa);

            DB::beginTransaction();
            try {
                // store file
                if ($req->hasFile('file')) {
                    $file = $req->file('file');
                    $fileName = $req->no_kembali . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                    $filePath = 'bukti_kembali_sewa/' . $fileName;
            
                    Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                        ->save(storage_path('app/public/' . $filePath));
            
                    if (!empty($oldData->file)) {
                        $oldFilePath = storage_path('app/public/' . $oldData->file);
                        if (File::exists($oldFilePath)) {
                            File::delete($oldFilePath);
                        }
                    }
            
                    if (File::exists(storage_path('app/public/' . $filePath))) {
                        $data['file'] = $filePath;
                    } else {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                    }
                }
            
                // Kembalikan stok area diupdate
                if ($oldData->status == 'DIKONFIRMASI') {
                    foreach ($oldData->produk as $produk) {
                        foreach ($produk->komponen as $komponen) {
                            $stok = InventoryGallery::where('lokasi_id', $kontrak->lokasi_id)
                                ->where('kode_produk', $komponen->kode_produk)
                                ->where('kondisi_id', $komponen->kondisi)
                                ->first();
                            if (!$stok) {
                                $stok = InventoryGallery::create([
                                    'kode_produk' => $komponen->kode_produk,
                                    'kondisi_id' => $komponen->kondisi,
                                    'lokasi_id' => $kontrak->lokasi_id,
                                    'jumlah' => 0,
                                    'min_stok' => 20,
                                ]);
                            }
                            $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($produk->jumlah));
                            $stok->update();
                            $komponen->forceDelete();
                        }
                        $produk->forceDelete();
                    }
                }
            
                // save data kembali
                $check = KembaliSewa::find($kembaliSewa)->update($data);
                if (!$check) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }
            
                // save produk kembali
                $startIdx = 0;
                $tempNama = $tempKondisi = $tempJumlah = [];
                for ($i = 0; $i < count($data['nama_produk']); $i++) {
                    $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                    $produk_terjual = Produk_Terjual::create([
                        'produk_jual_id' => $getProdukJual->id,
                        'no_do' => $data['no_do_produk'][$i],
                        'no_kembali_sewa' => KembaliSewa::find($kembaliSewa)->no_kembali,
                        'jumlah' => $data['jumlah'][$i],
                        'detail_lokasi' => $data['lokasi'][$i],
                        'jenis' => 'KEMBALI_SEWA'
                    ]);
            
                    $endIdx = $startIdx + $data['indexKomponen'][$i];
                    $tempNama[$data['nama_produk'][$i]] = array_slice($data['namaKomponen'], $startIdx, $data['indexKomponen'][$i]);
                    $tempKondisi[$data['nama_produk'][$i]] = array_slice($data['kondisiKomponen'], $startIdx, $data['indexKomponen'][$i]);
                    $tempJumlah[$data['nama_produk'][$i]] = array_slice($data['jumlahKomponen'], $startIdx, $data['indexKomponen'][$i]);
                    $startIdx = $endIdx;
            
                    if (!$produk_terjual) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    }
            
                    for ($j = 0; $j < count($tempNama[$data['nama_produk'][$i]]); $j++) {
                        $komponen = Produk::where('kode', $tempNama[$data['nama_produk'][$i]][$j])->first();
                        $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                            'produk_terjual_id' => $produk_terjual->id,
                            'kode_produk' => $tempNama[$data['nama_produk'][$i]][$j],
                            'nama_produk' => $komponen->nama,
                            'tipe_produk' => $komponen->tipe_produk,
                            'kondisi' => $tempKondisi[$data['nama_produk'][$i]][$j],
                            'deskripsi' => $komponen->deskripsi,
                            'jumlah' => $tempJumlah[$data['nama_produk'][$i]][$j],
                            'harga_satuan' => 0,
                            'harga_total' => 0
                        ]);
            
                        if (!$komponen_produk_terjual) {
                            DB::rollBack();
                            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                        }
            
                        if (in_array($komponen->tipe_produk, [1, 2]) && $oldData->status == 'DIKONFIRMASI') {
                            $stok = InventoryGallery::where('lokasi_id', $kontrak->lokasi_id)
                                ->where('kode_produk', $tempNama[$data['nama_produk'][$i]][$j])
                                ->where('kondisi_id', $tempKondisi[$data['nama_produk'][$i]][$j])
                                ->first();
                            if (!$stok) {
                                $stok = InventoryGallery::create([
                                    'kode_produk' => $tempNama[$data['nama_produk'][$i]][$j],
                                    'kondisi_id' => $tempKondisi[$data['nama_produk'][$i]][$j],
                                    'lokasi_id' => $kontrak->lokasi_id,
                                    'jumlah' => 0,
                                    'min_stok' => 20,
                                ]);
                            }
                            $stok->jumlah = intval($stok->jumlah) + (intval($tempJumlah[$data['nama_produk'][$i]][$j]) * intval($data['jumlah'][$i]));
                            $stok->update();
                        }
                    }
                }
            
                DB::commit();
                return redirect(route('kembali_sewa.index'))->with('success', 'Data tersimpan');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KembaliSewa  $kembaliSewa
     * @return \Illuminate\Http\Response
     */
    public function destroy($kembaliSewa)
    {
        $data = KembaliSewa::find($kembaliSewa);
        if(!$data) return response()->json(['msg' => 'kembali sewa tidak ditemukan']);
        $data->status = 'BATAL';
        $check = $data->update();
        if(!$check) return response()->json(['msg' => 'Gagal membatalkan kembali sewa']);
        return response()->json(['msg' => 'Berhasil membatalkan kembali sewa']);
    }
}
