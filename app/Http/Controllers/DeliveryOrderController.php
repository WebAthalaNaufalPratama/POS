<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Karyawan;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kontrak;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use Illuminate\Http\Request;
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
            $kode = substr($lastDO->no_form, -5);
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
            'no_sewa' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'driver' => 'required',
            'customer_id' => 'required',
            'penerima' => 'required',
            'handphone' => 'required',
            'alamat' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['jenis_do'] = 'SEWA';
        $data['status'] = 'DRAFT';
        $data['tanggal_pembuat'] = now();

        // data kontrak
        $kontrak = Kontrak::where('no_kontrak', $data['no_sewa'])->first();
        $data['pembuat'] = $kontrak->pembuat;
        
        // save data do
        $check = DeliveryOrder::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

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
            }
        }

        // save data tambahan
        for ($i=0; $i < count($data['nama_produk2']); $i++) { 
            $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk2'][$i])->first();
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->id,
                'no_do' => $check->no_do,
                'jumlah' => $data['jumlah2'][$i],
                'satuan' => $data['satuan2'][$i],
                'jenis' => 'TAMBAHAN',
                'detail_lokasi' => $data['detail_lokasi2'][$i]
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
    public function update_sewa(Request $request, DeliveryOrder $deliveryOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy_sewa(DeliveryOrder $deliveryOrder)
    {
        //
    }
}
