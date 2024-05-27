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
        $data = $query->orderByDesc('id')->get();
        $customer = DeliveryOrder::select('customer_id')
        ->distinct()
        ->join('customers', 'delivery_orders.customer_id', '=', 'customers.id')
        ->orderBy('customers.nama')
        ->get();
        $driver = DeliveryOrder::select('driver')
        ->distinct()
        ->join('karyawans', 'delivery_orders.driver', '=', 'karyawans.id')
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
        $drivers = Karyawan::where('jabatan', 'driver')->get();
        $produkjuals = Produk_Jual::all();
        $produkSewa = $kontrak->produk()->whereHas('produk', function($q){
            $q->whereColumn('jumlah_dikirim', '<', 'jumlah')->orWhere('jumlah_dikirim', null);
        })->get();
        $latestDO = DeliveryOrder::withTrashed()->orderByDesc('id')->first();

        // kode do
        if (!$latestDO) {
            $getKode = 'DVO' . date('Ymd') . '00001';
        } else {
            $lastDate = substr($latestDO->no_do, 3, 8);
            $todayDate = date('Ymd');
            if ($lastDate != $todayDate) {
                $getKode = 'DVO' . date('Ymd') . '00001';
            } else {
                $lastNumber = substr($latestDO->no_do, -5);
                $nextNumber = str_pad((int)$lastNumber + 1, 5, '0', STR_PAD_LEFT);
                $getKode = 'DVO' . date('Ymd') . $nextNumber;
            }
        }

        // cek jika sudah dikirim semua
        // $sisa_sewa = collect();
        // $do_terbuat = DeliveryOrder::with('produk')->where('no_referensi', $kontrak->no_kontrak)->get();
        // if($do_terbuat){
        //     foreach ($produkSewa as $item_sewa) {
        //         $sisa_produk = $item_sewa->jumlah; // Sisa produk diinisialisasi dengan jumlah awal dari sewa
        //         foreach ($do_terbuat as $do) { // Dapatkan DO
        //             foreach ($do->produk as $item_do) { // Dapatkan produk terjual DO
        //                 if($item_sewa->produk_jual_id == $item_do->produk_jual_id){ // Periksa produk yang sama antara sewa dan DO
        //                     $sisa_produk -= intval($item_do->jumlah); // Kurangi jumlah produk terjual dari sisa produk sewa
        //                 }
        //             }
        //         }
        //         if ($sisa_produk > 0) {
        //             $sisa_sewa->push([ // masukkan sisa produk ke array
        //                 'id' => $item_sewa->produk_jual_id,
        //                 'kode_produk' => $item_sewa->produk->kode,
        //                 'jumlah' => $sisa_produk
        //             ]);
        //         }
        //     }
        // }

        // cek jika sudah terkirim semua
        $terkirimSemua = false;
        if ($produkSewa->isEmpty()) {
            $terkirimSemua = true;
        }
        return view('do_sewa.create', compact('kontrak', 'drivers', 'produkjuals', 'getKode', 'produkSewa', 'terkirimSemua'));
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
            'tanggal_kirim' => 'required',
            'driver' => 'required',
            'customer_id' => 'required',
            'pic' => 'required',
            'handphone' => 'required',
            'alamat' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['jenis_do'] = 'SEWA';
        $data['status'] = 'DRAFT';
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

        // cek jika terkirim semua
        if ($produkSewa->isEmpty()) {
            return redirect()->back()->withInput()->with('fail', 'Produk sudah dikirim semua');
        }

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

        // cek jumlah input dengan jumlah sewa
        // foreach ($variable as $key => $value) {
        //     # code...
        // }

        // check sisa produk sewa yang belum dikirim
        // $sisa_sewa = collect();
        // $do_terbuat = DeliveryOrder::with('produk')->where('no_referensi', $data['no_referensi'])->get();
        // if($do_terbuat){
        //     foreach ($produkSewa as $item_sewa) {
        //         $sisa_produk = $item_sewa->jumlah; // Sisa produk diinisialisasi dengan jumlah awal dari sewa
        //         foreach ($do_terbuat as $do) { // Dapatkan DO
        //             foreach ($do->produk as $item_do) { // Dapatkan produk terjual DO
        //                 if($item_sewa->produk_jual_id == $item_do->produk_jual_id){ // Periksa produk yang sama antara sewa dan DO
        //                     $sisa_produk -= intval($item_do->jumlah); // Kurangi jumlah produk terjual dari sisa produk sewa
        //                 }
        //             }
        //         }
        //         if ($sisa_produk > 0) {
        //             $sisa_sewa->push([ // masukkan sisa produk ke array
        //                 'id' => $item_sewa->produk_jual_id,
        //                 'produk_terjual_id' => $item_sewa->id,
        //                 'kode_produk' => $item_sewa->produk->kode,
        //                 'jumlah' => $sisa_produk
        //             ]);
        //         }
        //     }
        // }

        // // cek jika sudah terkirim semua
        // if ($sisa_sewa->isEmpty()) {
        //     return redirect()->back()->withInput()->with('fail', 'Produk sudah dikirim semua');
        // }

        // cek input dengan sisa produk dari do terbuat
        // if(count($data['produk_id']) > 1){
        //     for ($j=0; $j < count($data['produk_id']); $j++) { // loop sisa produk
        //         $terkirim = 0;
        //         for ($i=0; $i < count($sisa_sewa); $i++) { // loop produk dari input
        //             if($sisa_sewa[$i]['produk_terjual_id'] == $data['produk_id'][$j]){ // cek kode produk
        //                 if($data['jumlah'][$j] > $sisa_sewa[$i]['jumlah']){ // cek jumlah
        //                     return redirect()->back()->withInput()->with('fail', 'Jumlah produk tidak sesuai');
        //                 }
        //             } else {
        //                 $terkirim++;
        //                 if($terkirim == count($data['nama_produk'])){
        //                     return redirect()->back()->withInput()->with('fail', 'Produk sudah dikirim');
        //                 }
        //             }
        //         }
        //     }
        // } else {
        //     foreach ($sisa_sewa as $produk) {
        //         if($produk['produk_terjual_id'] == $data['produk_id'][0]){
        //             if ($produk['jumlah'] < $data['jumlah'][0]) {
        //                 return redirect()->back()->withInput()->with('fail', 'Jumlah produk tidak sesuai');
        //             }
        //         }
        //     }
        // }

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

        // cek stok inventory
        foreach ($dataDO as $key => $value) {
            $inventory = InventoryGallery::where('kode_produk', $key)->where('kondisi_id', $value['kondisi'])->where('lokasi_id', $kontrak->lokasi_id)->first();
            if(!$inventory) return redirect()->back()->withInput()->with('fail', 'Stok tidak ada');
            if($inventory->jumlah > $value['jumlah']){
                $sufficient = $inventory->jumlah - $value['jumlah'] >= $inventory->min_stok;
                if(!$sufficient) return redirect()->back()->withInput()->with('fail', 'Stok dibawah minimum');
            } else {
                return redirect()->back()->withInput()->with('fail', 'Stok tidak mencukupi');
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

        // pengurangan stok
        foreach ($dataDO as $key => $value) {
            $inventory = InventoryGallery::where('kode_produk', $key)->where('kondisi_id', $value['kondisi'])->where('lokasi_id', $kontrak->lokasi_id)->first();
            $inventory->jumlah = $inventory->jumlah - intval($value['jumlah']);
            $inventory->update();
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
        $drivers = Karyawan::where('jabatan', 'DRIVER')->get();
        return view('do_sewa.show', compact('data', 'produkJuals', 'drivers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function edit_sewa(DeliveryOrder $deliveryOrder)
    {
        //
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
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = $req->no_do . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_do_sewa', $fileName, 'public');
            $data['file'] = $filePath;

            // update bukti DO
            $do = DeliveryOrder::find($deliveryOrder);
            $do->file = $data['file'];
            $do->update();
            return redirect()->back()->with('success', 'File tersimpan');
        } else {
            return redirect()->back()->with('fail', 'Gagal menyimpan file');
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
        //
    }
}
