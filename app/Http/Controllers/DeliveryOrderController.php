<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\InventoryGallery;
use App\Models\Karyawan;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kontrak;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_sewa(Request $req)
    {
        $query = DeliveryOrder::where('jenis_do', 'SEWA');
        if(Auth::user()->karyawans){
            $query->whereHas('kontrak', function($q) {
                $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
            });
        }
        if ($req->customer) {
            $query->where('customer_id', $req->input('customer'));
        }
        if ($req->driver) {
            $query->where('driver', $req->input('driver'));
        }
        if ($req->dateStart) {
            $query->where('tanggal_kirim', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kirim', '<=', $req->input('dateEnd'));
        }
        if(Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Auditor')){
            $query->where('status', 'DIKONFIRMASI');
        }
        $data = $query->orderByDesc('id')->get();
        $customer = DeliveryOrder::select('customer_id')
        ->distinct()
        ->join('customers', 'delivery_orders.customer_id', '=', 'customers.id')
        ->when(Auth::user()->karyawans, function ($query) {
            return $query->where('customers.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('customers.nama')
        ->get();
        $driver = DeliveryOrder::select('driver')
        ->distinct()
        ->join('karyawans', 'delivery_orders.driver', '=', 'karyawans.id')
        ->when(Auth::user()->karyawans, function ($query) {
            return $query->where('karyawans.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('karyawans.nama')
        ->get();
        return view('do_sewa.index', compact('data', 'driver', 'customer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_sewa(Request $req)
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
        if(Auth::user()->roles()->value('name') != 'admin'){
            $drivers = Karyawan::where('jabatan', 'driver')->where('lokasi_id',Auth::user()->karyawans->lokasi_id)->get();
        } else {
            $drivers = Karyawan::where('jabatan', 'driver')->get();
        }
        $produkjuals = Produk_Jual::all();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $latestDO = DeliveryOrder::withTrashed()->orderByDesc('id')->first();

        // kode do
        if (!$latestDO) {
            $getKode = 'DVS' . date('Ymd') . '00001';
        } else {
            $lastDate = substr($latestDO->no_do, 3, 8);
            $todayDate = date('Ymd');
            if ($lastDate != $todayDate) {
                $getKode = 'DVS' . date('Ymd') . '00001';
            } else {
                $lastNumber = substr($latestDO->no_do, -5);
                $nextNumber = str_pad((int)$lastNumber + 1, 5, '0', STR_PAD_LEFT);
                $getKode = 'DVS' . date('Ymd') . $nextNumber;
            }
        }

        // cek jika sudah terkirim semua
        // $terkirimSemua = false;
        // if ($produkSewa->isEmpty()) {
        //     $terkirimSemua = true;
        // }
        return view('do_sewa.create', compact('kontrak', 'drivers', 'produkjuals', 'getKode', 'produkSewa'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_sewa(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'no_do' => 'required',
            'no_referensi' => 'required',
            'tanggal_kirim' => 'required|date',
            'driver' => 'required',
            'customer_id' => 'required',
            'pic' => 'required',
            'handphone' => 'required|numeric|digits_between:11,13',
            'alamat' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['jenis_do'] = 'SEWA';
        $data['status'] = 'TUNDA';
        $data['tanggal_pembuat'] = now();
        $data['pembuat'] = Auth::user()->id;
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = $req->no_do . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_do_sewa', $fileName, 'public');
            $data['file'] = $filePath;
        }

        // check produk and quantity from sewa
        $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_referensi'])->first();
        $produkSewa = $kontrak->produk()->whereHas('produk', function($q){
            $q->whereColumn('jumlah_dikirim', '<', 'jumlah')->orWhere('jumlah_dikirim', null);
        })->get();

        // cek input dengan sewa
        foreach ($produkSewa as $item) {
            for ($i=0; $i < count($data['produk_id']); $i++) {
                if($data['produk_id'][$i] == $item->id){
                    if($data['jumlah'][$i] > $item->jumlah || $data['jumlah'][$i] > ($item->jumlah - $item->jumlah_dikirim)){
                        return redirect()->back()->withInput()->with('fail', 'Jumlah produk tidak sesuai dengan kontrak');
                    }
                }
            }
        }


        // ambil akumulasi komponen DO
        $dataDO = [];
        for ($i=0; $i < count($data['produk_id']); $i++) { 
            $produkTerjual = Produk_Terjual::find($data['produk_id'][$i])->komponen;
            foreach ($produkTerjual as $item) {
                if(isset($dataDO[$item->kode_produk]['kondisi'])){
                    $dataDO[$item->kode_produk]['jumlah'] += $item->jumlah * $data['jumlah'][$i];
                } else {
                    $dataDO[$item->kode_produk] = ['kondisi' => $item->kondisi, 'jumlah' => $item->jumlah * $data['jumlah'][$i]];
                }
            }
        }

        // ambil akumulasi komponen tambahan DO
        $dataTambahanDO = [];
        if(isset($data['nama_produk2'][0])){
            for ($i=0; $i < count($data['produk_id2']); $i++) { 
                $produkTerjual = Produk_Jual::with('komponen')->find($data['produk_id2'][$i])->komponen;
                foreach ($produkTerjual as $item) {
                    if(isset($dataTambahanDO[$item->kode_produk]['kondisi'])){
                        $dataTambahanDO[$item->kode_produk]['jumlah'] += $item->jumlah * $data['jumlah2'][$i];
                    } else {
                        $dataTambahanDO[$item->kode_produk] = ['kondisi' => $item->kondisi, 'jumlah' => $item->jumlah * $data['jumlah2'][$i]];
                    }
                }
            }

            // penggabungan data DO dan tambahan
            foreach ($dataTambahanDO as $key => $value) {
                if (isset($dataDO[$key]) && $dataDO[$key]['kondisi'] == $value['kondisi']) {
                    $dataDO[$key]['jumlah'] += $value['jumlah'];
                } else {
                    $dataDO[$key] = $value;
                }
            }
        }

        // save data do
        $check = DeliveryOrder::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

        // save produk do
        for ($i=0; $i < count($data['nama_produk']); $i++) { 
            $getProdukTerjual = Produk_Terjual::with('komponen')->find($data['produk_id'][$i]);
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukTerjual->produk_jual_id,
                'no_do' => $check->no_do,
                'jumlah' => $data['jumlah'][$i],
                'satuan' => $data['satuan'][$i],
                'detail_lokasi' => $data['detail_lokasi'][$i]
            ]);
            $getProdukSewa = Produk_Terjual::find($data['produk_id'][$i]);
            $getProdukSewa->jumlah_dikirim = ($getProdukSewa->jumlah_dikirim ?? 0) + intval($data['jumlah'][$i]);
            $getProdukSewa->update();

            if(!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            foreach ($getProdukTerjual->komponen as $komponen ) {
                $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                    'produk_terjual_id' => $produk_terjual->id,
                    'kode_produk' => $komponen->kode_produk,
                    'nama_produk' => $komponen->nama_produk,
                    'tipe_produk' => $komponen->tipe_produk,
                    'kondisi' => $komponen->kondisi,
                    'deskripsi' => $komponen->deskripsi,
                    'jumlah' => $komponen->jumlah,
                    'harga_satuan' => $komponen->harga_satuan,
                    'harga_total' => $komponen->harga_total
                ]);
                if(!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
        }

        // save data tambahan
        if(isset($data['nama_produk2'][0])){
            for ($i=0; $i < count($data['nama_produk2']); $i++) { 
                $getProdukJual = Produk_Jual::with('komponen')->find($data['produk_id2'][$i]);
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_do' => $check->no_do,
                    'jumlah' => $data['jumlah2'][$i],
                    'satuan' => $data['satuan2'][$i],
                    'jenis' => 'TAMBAHAN',
                    'keterangan' => $data['keterangan2'][$i]
                ]);
    
                if(!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                foreach ($getProdukJual->komponen as $komponen ) {
                    $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                        'produk_terjual_id' => $produk_terjual->id,
                        'kode_produk' => $komponen->kode_produk,
                        'nama_produk' => $komponen->nama_produk,
                        'tipe_produk' => $komponen->tipe_produk,
                        'kondisi' => $komponen->kondisi,
                        'deskripsi' => $komponen->deskripsi,
                        'jumlah' => $komponen->jumlah,
                        'harga_satuan' => $komponen->harga_satuan,
                        'harga_total' => $komponen->harga_total
                    ]);
                    if(!$komponen_produk_terjual) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }
            }
        }

        return redirect(route('kontrak.index'))->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function show_sewa($deliveryOrder)
    {
        $data = DeliveryOrder::find($deliveryOrder);
        $produkJuals = Produk_Jual::all();
        $kontrak = Kontrak::where('no_kontrak', $data->kontrak->no_kontrak)->first();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $drivers = Karyawan::where('jabatan', 'DRIVER')->get();
        $riwayat = Activity::where('subject_type', DeliveryOrder::class)->where('subject_id', $deliveryOrder)->orderBy('id', 'desc')->get();
        return view('do_sewa.show', compact('data', 'produkJuals', 'drivers', 'riwayat', 'produkSewa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function edit_sewa($deliveryOrder)
    {
        $data = DeliveryOrder::find($deliveryOrder);
        $produkJuals = Produk_Jual::all();
        $kontrak = Kontrak::where('no_kontrak', $data->kontrak->no_kontrak)->first();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $drivers = Karyawan::where('jabatan', 'DRIVER')->get();
        $riwayat = Activity::where('subject_type', DeliveryOrder::class)->where('subject_id', $deliveryOrder)->orderBy('id', 'desc')->get();
        return view('do_sewa.edit', compact('data', 'produkJuals', 'drivers', 'riwayat', 'produkSewa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function update_sewa(Request $req, $deliveryOrder)
    {
        if($req->konfirmasi){
            $do_sewa = DeliveryOrder::find($deliveryOrder);
            if($req->konfirmasi == 'confirm'){
                $do_sewa->status = 'DIKONFIRMASI';
                $msg = 'Dikonfirmasi';
                
                // cek stok inventory
                foreach ($do_sewa->produk as $produk) {
                    foreach ($produk->komponen as $komponen) {
                        $inventory = InventoryGallery::where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->where('lokasi_id', $do_sewa->kontrak->lokasi_id)->first();
                        if(!$inventory) return redirect()->back()->withInput()->with('fail', 'Stok '.$komponen->kode_produk.' tidak ada');
                        if($inventory->jumlah > ($komponen->jumlah * $produk->jumlah)){
                            $sufficient = $inventory->jumlah - ($komponen->jumlah * $produk->jumlah) >= $inventory->min_stok;
                            if(!$sufficient) return redirect()->back()->withInput()->with('fail', 'Stok '.$komponen->kode_produk.' dibawah minimum');
                        } else {
                            return redirect()->back()->withInput()->with('fail', 'Stok '.$komponen->kode_produk.' tidak mencukupi');
                        }
                    }
                }

                // pengurangan stok
                foreach ($do_sewa->produk as $produk) {
                    foreach ($produk->komponen as $komponen) {
                        $inventory = InventoryGallery::where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->where('lokasi_id', $do_sewa->kontrak->lokasi_id)->first();
                        $inventory->jumlah = $inventory->jumlah - (intval($komponen->jumlah) * $produk->jumlah);
                        $inventory->update();
                    }
                }
            } else if($req->konfirmasi == 'cancel'){
                $do_sewa->status = 'BATAl';
                $msg = 'Dibatalkan';
            } else {
                return redirect()->back()->withInput()->with('fail', 'Status tidak sesuai');
            }
            if(Auth::user()->hasRole('Auditor')){
                $do_sewa->penyetuju = Auth::user()->id;
                $do_sewa->tanggal_penyetuju = $req->tanggal_penyetuju;
            }
            if(Auth::user()->hasRole('Finance')){
                $do_sewa->pemeriksa = Auth::user()->id;
                $do_sewa->tanggal_pemeriksa = $req->tanggal_pemeriksa;
            }
            $check = $do_sewa->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal mengubah status');
            return redirect()->back()->withInput()->with('success', 'Data Berhasil ' . $msg);
        } else {
            // validasi
            $validator = Validator::make($req->all(), [
                'no_do' => 'required',
                'no_referensi' => 'required',
                'tanggal_kirim' => 'required',
                'driver' => 'required',
                'customer_id' => 'required',
                'pic' => 'required',
                'handphone' => 'required|numeric|digits_between:11,13',
                'alamat' => 'required',
            ]);
            $error = $validator->errors()->all();
            if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
            $data = $req->except(['_token', '_method']);
            if ($req->hasFile('file')) {
                $file = $req->file('file');
                $fileName = $req->no_do . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('bukti_do_sewa', $fileName, 'public');
                $data['file'] = $filePath;
            }
            // check produk and quantity from sewa
            $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_referensi'])->first();
            $produkSewa = $kontrak->produk()->whereHas('produk')->get();

            // cek input dengan sewa
            $sesuaiKontrak = true;

            $produkSewa->each(function ($produk) use($data, &$sesuaiKontrak) {
                for ($i = 0; $i < count($data['nama_produk']); $i++) {
                    if ($produk->produk->kode == $data['nama_produk'][$i]) {
                        $produk->jumlah -= $data['jumlah'][$i];
                        if ($produk->jumlah < 0) {
                            $sesuaiKontrak = false;
                            break;
                        }
                    }
                }
            });

            if (!$sesuaiKontrak) {
                return redirect()->back()->withInput()->with('fail', 'Produk tidak sesuai kontrak');
            }

            // save data do
            $check = DeliveryOrder::find($deliveryOrder)->update($data);
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal mengupdate data');

            $data_do =  DeliveryOrder::find($deliveryOrder);
            $dataProduk = Produk_Terjual::where('no_do', $data_do->no_do)->get();

            // delete data
            foreach ($dataProduk as $item) {
                foreach ($item->komponen as $komponen) { // pengembalian stok
                    $inventory = InventoryGallery::where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->where('lokasi_id', $kontrak->lokasi_id)->first();
                        $inventory->jumlah = $inventory->jumlah + (intval($komponen->jumlah) * intval($item->jumlah));
                        $inventory->update();
                }
                $komponen = Komponen_Produk_Terjual::where('produk_terjual_id', $item->id)->forceDelete();
                $produkTerjual = Produk_Terjual::find($item->id)->forceDelete();
            }

            // save produk do
            for ($i=0; $i < count($data['nama_produk']); $i++) { 
                $getProdukTerjual = Produk_Terjual::with('komponen')->find($data['produk_id'][$i]);
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukTerjual->produk_jual_id,
                    'no_do' => $data_do->no_do,
                    'jumlah' => $data['jumlah'][$i],
                    'satuan' => $data['satuan'][$i],
                    'detail_lokasi' => $data['detail_lokasi'][$i]
                ]);
                $getProdukSewa = Produk_Terjual::find($data['produk_id'][$i]);
                $getProdukSewa->jumlah_dikirim = ($getProdukSewa->jumlah_dikirim ?? 0) + intval($data['jumlah'][$i]);
                $getProdukSewa->update();

                if(!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                foreach ($getProdukTerjual->komponen as $komponen ) {
                    $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                        'produk_terjual_id' => $produk_terjual->id,
                        'kode_produk' => $komponen->kode_produk,
                        'nama_produk' => $komponen->nama_produk,
                        'tipe_produk' => $komponen->tipe_produk,
                        'kondisi' => $komponen->kondisi,
                        'deskripsi' => $komponen->deskripsi,
                        'jumlah' => $komponen->jumlah,
                        'harga_satuan' => $komponen->harga_satuan,
                        'harga_total' => $komponen->harga_total
                    ]);
                    if(!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

                    // pengurangan stok
                    $inventory = InventoryGallery::where('kode_produk', $komponen_produk_terjual->kode_produk)->where('kondisi_id', $komponen_produk_terjual->kondisi)->where('lokasi_id', $kontrak->lokasi_id)->first();
                    $inventory->jumlah = $inventory->jumlah - (intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah));
                    $inventory->update();
                }
            }

            // // save data tambahan
            if(isset($data['nama_produk2'][0])){
                for ($i=0; $i < count($data['nama_produk2']); $i++) { 
                    $getProdukJual = Produk_Jual::with('komponen')->find($data['produk_id2'][$i]);
                    $produk_terjual = Produk_Terjual::create([
                        'produk_jual_id' => $getProdukJual->id,
                        'no_do' => $data_do->no_do,
                        'jumlah' => $data['jumlah2'][$i],
                        'satuan' => $data['satuan2'][$i],
                        'jenis' => 'TAMBAHAN',
                        'keterangan' => $data['keterangan2'][$i]
                    ]);
        
                    if(!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    foreach ($getProdukJual->komponen as $komponen ) {
                        $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                            'produk_terjual_id' => $produk_terjual->id,
                            'kode_produk' => $komponen->kode_produk,
                            'nama_produk' => $komponen->nama_produk,
                            'tipe_produk' => $komponen->tipe_produk,
                            'kondisi' => $komponen->kondisi,
                            'deskripsi' => $komponen->deskripsi,
                            'jumlah' => $komponen->jumlah,
                            'harga_satuan' => $komponen->harga_satuan,
                            'harga_total' => $komponen->harga_total
                        ]);
                        if(!$komponen_produk_terjual) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

                        // pengurangan stok
                        $inventory = InventoryGallery::where('kode_produk', $komponen_produk_terjual->kode_produk)->where('kondisi_id', $komponen_produk_terjual->kondisi)->where('lokasi_id', $kontrak->lokasi_id)->first();
                        $inventory->jumlah = $inventory->jumlah - (intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah));
                        $inventory->update();
                    }
                }
            }

            return redirect(route('do_sewa.index'))->with('success', 'Data tersimpan');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy_sewa($deliveryOrder)
    {
        $data = DeliveryOrder::find($deliveryOrder);
        if(!$data) return response()->json(['msg' => 'Delivery Order tidak ditemukan']);
        $data->status = 'BATAL';
        $check = $data->update();
        if(!$check) return response()->json(['msg' => 'Gagal membatalkan delivery order']);
        return response()->json(['msg' => 'Berhasil membatalkan delivery order']);
    }

    public function getProdukDo(Request $req)
    {
        $produksDO = Produk_Terjual::with('komponen', 'produk')->where('no_do', $req->no_do)->get();
        if(!$produksDO) return response()->json('Not found', 404);
        return response()->json($produksDO);
    }
}
