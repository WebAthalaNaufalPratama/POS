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
    public function index_sewa()
    {
        $data = DeliveryOrder::where('jenis_do', 'SEWA')->get();
        return view('do_sewa.index', compact('data'));
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
        $latestDO = DeliveryOrder::withTrashed()->orderByDesc('id')->get();

        // kode do
        if(count($latestDO) < 1){
            $getKode = 'DVO' . date('Ymd') . '00001';
        } else {
            $lastDO = $latestDO->first();
            $kode = substr($lastDO->no_referensi, -5);
            $getKode = 'DVO' . date('Ymd') . str_pad((int)$kode + 1, 5, '0', STR_PAD_LEFT);
        }
        return view('do_sewa.create', compact('kontrak', 'drivers', 'produkjuals', 'getKode'));
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

        // save data do
        $check = DeliveryOrder::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        $kontrak = Kontrak::where('no_kontrak', $data['no_referensi'])->first();

        // save produk do
        for ($i=0; $i < count($data['nama_produk']); $i++) { 
            $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->id,
                'no_do' => $check->no_do,
                'jumlah' => $data['jumlah'][$i],
                'satuan' => $data['satuan'][$i],
                'detail_lokasi' => $data['detail_lokasi'][$i]
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
                if(!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                $stok = InventoryGallery::where('lokasi_id', $kontrak->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($data['jumlah'][$i]));
                $stok->update();
            }
        }

        // save data tambahan
        if(isset($data['nama_produk2'][0])){
            for ($i=0; $i < count($data['nama_produk2']); $i++) { 
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk2'][$i])->first();
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
                    if(!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    $stok = InventoryGallery::where('lokasi_id', $kontrak->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                    $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($data['jumlah2'][$i]));
                    $stok->update();
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
