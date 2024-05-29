<?php

namespace App\Http\Controllers;

use App\Models\InventoryGallery;
use App\Models\PemakaianSendiri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PemakaianSendiriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'lokasi_id' => 'required|exists:lokasis,id',
            'produk_inven_id.*' => 'required|exists:inventory_galleries,id',
            'karyawan_id.*' => 'required|exists:karyawans,id',
            'jumlah.*' => 'required|numeric',
            'alasan' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // save data
        for ($i=0; $i < count($data['produk_inven_id']); $i++) { 
            $getProduk = InventoryGallery::with('produk')->find($data['produk_inven_id'][$i]);
            $singleData = [
                'lokasi_id' => $data['lokasi_id'],
                'produk_id' => $getProduk->produk->id,
                'kondisi_id' =>$getProduk->kondisi_id,
                'karyawan_id' => $data['karyawan_id'][$i],
                'jumlah' => $data['jumlah'][$i],
                'alasan' => $data['alasan'][$i],
            ];
            $check = PemakaianSendiri::create($singleData);
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            // kurangi stok
            $getProduk = InventoryGallery::find($data['produk_inven_id'][$i]);
            $getProduk->jumlah -= $data['jumlah'][$i];
            $getProduk->update();
        }
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PemakaianSendiri  $pemakaianSendiri
     * @return \Illuminate\Http\Response
     */
    public function show(PemakaianSendiri $pemakaianSendiri)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PemakaianSendiri  $pemakaianSendiri
     * @return \Illuminate\Http\Response
     */
    public function edit(PemakaianSendiri $pemakaianSendiri)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PemakaianSendiri  $pemakaianSendiri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PemakaianSendiri $pemakaianSendiri)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PemakaianSendiri  $pemakaianSendiri
     * @return \Illuminate\Http\Response
     */
    public function destroy(PemakaianSendiri $pemakaianSendiri)
    {
        //
    }
}
