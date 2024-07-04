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
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

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
        if(Auth::user()->roles()->value('name') != 'admin'){
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
        $data = $query->orderByDesc('id')->get();
        $customer = Kontrak::whereHas('kembali_sewa')->select('customer_id')
        ->distinct()
        ->join('customers', 'kontraks.customer_id', '=', 'customers.id')
        ->when(Auth::user()->roles()->value('name') != 'admin', function ($query) {
            return $query->where('customers.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('customers.nama')
        ->get();
        $driver = DeliveryOrder::select('driver')
        ->distinct()
        ->join('karyawans', 'delivery_orders.driver', '=', 'karyawans.id')
        ->when(Auth::user()->roles()->value('name') != 'admin', function ($query) {
            return $query->where('karyawans.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('karyawans.nama')
        ->get();
        return view('kembali_sewa.index', compact('data', 'driver', 'customer'));
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
        $drivers = Karyawan::where('jabatan', 'Driver')->when(Auth::user()->roles()->value('name') != 'admin', function ($query) {
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
        // validasi
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
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['status'] = 'TUNDA';
        $data['tanggal_pembuat'] = now();
        $data['pembuat'] = Auth::user()->id;

        // check produk and quantity from sewa
        $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_sewa'])->first();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();

        // cek input dengan sewa
        foreach ($produkSewa as $item) {
            for ($i=0; $i < count($data['nama_produk']); $i++) {
                if($data['nama_produk'][$i] == $item->produk->kode){
                    if($data['jumlah'][$i] > $item->jumlah){
                        return redirect()->back()->withInput()->with('fail', 'Jumlah produk tidak sesuai dengan kontrak');
                    }
                }
            }
        }

        // cek jika ada do
        $do_terbuat = DeliveryOrder::with('produk')->where('no_referensi', $kontrak->no_kontrak)->get();
        if(!$do_terbuat) return redirect()->back()->withInput()->with('fail', 'Belum ada DO yang terbuat');

        // ambil barang do
        $dataDO = collect();
        foreach ($do_terbuat as $item) {
            $dataDO->push($item->produk()->whereNull('no_kembali_sewa')->whereNull('jenis')->whereHas('produk')->get());
        }

        // jadikan satu barang do
        $produkDO = collect();
        foreach ($dataDO as $do) {
            foreach ($do as $produk) {
                $existingProduk = $produkDO->where('produk_jual_id', $produk->produk_jual_id)->first();
        
                if ($existingProduk) { // jika produk sudah ada
                    $produkDO = $produkDO->map(function ($item) use ($produk) {
                        if ($item['produk_jual_id'] == $produk->produk_jual_id) {
                            $item['jumlah'] += $produk->jumlah;
                        }
                        return $item;
                    });
                } else { // jika produk belum ada
                    $produkDO->push([
                        'produk_jual_id' => $produk->produk_jual_id,
                        'kode' => $produk->produk->kode,
                        'jumlah' => $produk->jumlah,
                    ]);
                }
            }
        }

        // kurangi do dengan kembali sewa
        $kembali_sewa = KembaliSewa::with('produk')->where('no_sewa', $data['no_sewa'])->get();
        if($kembali_sewa){
            $produkDO->transform(function ($item) use ($kembali_sewa) {
                foreach ($kembali_sewa as $sewa) {
                    foreach ($sewa->produk as $produk_sewa) {
                        if ($item['produk_jual_id'] == $produk_sewa->produk_jual_id) {
                            $item['jumlah'] -= intval($produk_sewa->jumlah);
                        }
                    }
                }
                return $item;
            });
            $semuaNol = $produkDO->every(function ($item) {
                return $item['jumlah'] == 0;
            });
            if ($semuaNol) return redirect()->back()->withInput()->with('fail', 'Barang sudah kembali semua');
        }

        // kurangi sisa barang do dengan input
        $produkDO->transform(function ($item) use ($data) {
            for ($i=0; $i < count($data['nama_produk']); $i++) { 
                if($item['kode'] == $data['nama_produk'][$i]){
                    $item['jumlah'] -= intval($data['jumlah'][$i]);
                }
            }
            return $item;
        });
        $sisa = $produkDO->every(function ($item) {
            return $item['jumlah'] >= 0;
        });
        if (!$sisa) return redirect()->back()->withInput()->with('fail', 'Jumlah barang tidak sesuai');

        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = $req->no_kembali . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_kembali_sewa', $fileName, 'public');
            $data['file'] = $filePath;
        }

        // save data kembali
        $check = KembaliSewa::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

        // save produk kembali
        $startIdx = 0;
        $tempNama = $tempKondisi = $tempJumlah = [];
        for ($i=0; $i < count($data['nama_produk']); $i++) { 
            $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->id,
                'no_do' => $data['no_do_produk'][$i],
                'no_kembali_sewa' => $check->no_kembali,
                'jumlah' => $data['jumlah'][$i],
                'detail_lokasi' => $data['lokasi'][$i],
                'jenis' => 'KEMBALI_SEWA'
            ]);

            // pisahkan array komponen per produk terjual
            $endIdx = $startIdx + $data['indexKomponen'][$i];
            $tempNama[$data['nama_produk'][$i]] = array_slice($data['namaKomponen'], $startIdx, $data['indexKomponen'][$i]);
            $tempKondisi[$data['nama_produk'][$i]] = array_slice($data['kondisiKomponen'], $startIdx, $data['indexKomponen'][$i]);
            $tempJumlah[$data['nama_produk'][$i]] = array_slice($data['jumlahKomponen'], $startIdx, $data['indexKomponen'][$i]);
            $startIdx = $endIdx;

            if(!$produk_terjual) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            for ($j=0; $j < count($tempNama[$data['nama_produk'][$i]]); $j++) { 
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
                if(!$komponen_produk_terjual) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

                // update stok
                if(in_array($komponen->tipe_produk, [1, 2])){
                    $stok = InventoryGallery::where('lokasi_id', $kontrak->lokasi_id)->where('kode_produk', $tempNama[$data['nama_produk'][$i]][$j])->where('kondisi_id', $tempKondisi[$data['nama_produk'][$i]][$j])->first();
                    if(!$stok){
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
        return redirect(route('kontrak.index'))->with('success', 'Data tersimpan');
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

        // check produk and quantity from sewa
        $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_sewa'])->first();
        // $produkSewa = $kontrak->produk()->whereHas('produk')->get();

        // // cek input dengan sewa
        // foreach ($produkSewa as $item) {
        //     for ($i=0; $i < count($data['nama_produk']); $i++) {
        //         if($data['nama_produk'][$i] == $item->produk->kode){
        //             if($data['jumlah'][$i] > $item->jumlah){
        //                 return redirect()->back()->withInput()->with('fail', 'Jumlah produk tidak sesuai dengan kontrak');
        //             }
        //         }
        //     }
        // }

        // // cek jika ada do
        // $do_terbuat = DeliveryOrder::with('produk')->where('no_referensi', $kontrak->no_kontrak)->get();
        // if(!$do_terbuat) return redirect()->back()->withInput()->with('fail', 'Belum ada DO yang terbuat');

        // // ambil barang do
        // $dataDO = collect();
        // foreach ($do_terbuat as $item) {
        //     $dataDO->push($item->produk()->whereNull('no_kembali_sewa')->whereNull('jenis')->whereHas('produk')->get());
        // }

        // // jadikan satu barang do
        // $produkDO = collect();
        // foreach ($dataDO as $do) {
        //     foreach ($do as $produk) {
        //         $existingProduk = $produkDO->where('produk_jual_id', $produk->produk_jual_id)->first();
        
        //         if ($existingProduk) { // jika produk sudah ada
        //             $produkDO = $produkDO->map(function ($item) use ($produk) {
        //                 if ($item['produk_jual_id'] == $produk->produk_jual_id) {
        //                     $item['jumlah'] += $produk->jumlah;
        //                 }
        //                 return $item;
        //             });
        //         } else { // jika produk belum ada
        //             $produkDO->push([
        //                 'produk_jual_id' => $produk->produk_jual_id,
        //                 'kode' => $produk->produk->kode,
        //                 'jumlah' => $produk->jumlah,
        //             ]);
        //         }
        //     }
        // }

        // // kurangi do dengan kembali sewa
        // $kembali_sewa = KembaliSewa::with('produk')->where('no_sewa', $data['no_sewa'])->get();
        // if($kembali_sewa){
        //     $produkDO->transform(function ($item) use ($kembali_sewa) {
        //         foreach ($kembali_sewa as $sewa) {
        //             foreach ($sewa->produk as $produk_sewa) {
        //                 if ($item['produk_jual_id'] == $produk_sewa->produk_jual_id) {
        //                     $item['jumlah'] -= intval($produk_sewa->jumlah);
        //                 }
        //             }
        //         }
        //         return $item;
        //     });
        //     $semuaNol = $produkDO->every(function ($item) {
        //         return $item['jumlah'] == 0;
        //     });
        //     if ($semuaNol) return redirect()->back()->withInput()->with('fail', 'Barang sudah kembali semua');
        // }

        // // kurangi sisa barang do dengan input
        // $produkDO->transform(function ($item) use ($data) {
        //     for ($i=0; $i < count($data['nama_produk']); $i++) { 
        //         if($item['kode'] == $data['nama_produk'][$i]){
        //             $item['jumlah'] -= intval($data['jumlah'][$i]);
        //         }
        //     }
        //     return $item;
        // });
        // $sisa = $produkDO->every(function ($item) {
        //     return $item['jumlah'] >= 0;
        // });
        // if (!$sisa) return redirect()->back()->withInput()->with('fail', 'Jumlah barang tidak sesuai');

        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = $req->no_kembali . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_kembali_sewa', $fileName, 'public');
            $data['file'] = $filePath;
        }

        // old data
        $oldData = KembaliSewa::with('produk.komponen')->find($kembaliSewa);

        // kurangi stok
        foreach ($oldData->produk as $produk) {
            foreach ($produk->komponen as $komponen) {
                $stok = InventoryGallery::where('lokasi_id', $kontrak->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                if(!$stok){
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

        // save data kembali
        $check = KembaliSewa::find($kembaliSewa)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

        // save produk kembali
        $startIdx = 0;
        $tempNama = $tempKondisi = $tempJumlah = [];
        for ($i=0; $i < count($data['nama_produk']); $i++) { 
            $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->id,
                'no_do' => $data['no_do_produk'][$i],
                'no_kembali_sewa' => KembaliSewa::find($kembaliSewa)->no_kembali,
                'jumlah' => $data['jumlah'][$i],
                'detail_lokasi' => $data['lokasi'][$i],
                'jenis' => 'KEMBALI_SEWA'
            ]);

            // pisahkan array komponen per produk terjual
            $endIdx = $startIdx + $data['indexKomponen'][$i];
            $tempNama[$data['nama_produk'][$i]] = array_slice($data['namaKomponen'], $startIdx, $data['indexKomponen'][$i]);
            $tempKondisi[$data['nama_produk'][$i]] = array_slice($data['kondisiKomponen'], $startIdx, $data['indexKomponen'][$i]);
            $tempJumlah[$data['nama_produk'][$i]] = array_slice($data['jumlahKomponen'], $startIdx, $data['indexKomponen'][$i]);
            $startIdx = $endIdx;

            if(!$produk_terjual) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            for ($j=0; $j < count($tempNama[$data['nama_produk'][$i]]); $j++) { 
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
                if(!$komponen_produk_terjual) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

                // update stok
                if(in_array($komponen->tipe_produk, [1, 2])){
                    $stok = InventoryGallery::where('lokasi_id', $kontrak->lokasi_id)->where('kode_produk', $tempNama[$data['nama_produk'][$i]][$j])->where('kondisi_id', $tempKondisi[$data['nama_produk'][$i]][$j])->first();
                    if(!$stok){
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
        return redirect(route('kembali_sewa.index'))->with('success', 'Data tersimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\KembaliSewa  $kembaliSewa
     * @return \Illuminate\Http\Response
     */
    public function destroy(KembaliSewa $kembaliSewa)
    {
        //
    }
}
