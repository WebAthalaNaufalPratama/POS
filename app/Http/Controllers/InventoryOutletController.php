<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\InventoryOutlet;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use Illuminate\Support\Facades\Validator;

class InventoryOutletController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = InventoryOutlet::all();
        return view('inven_outlet.index', compact('data'));
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
        $gallery = Lokasi::where('tipe_lokasi', 2)->get();
        return view('inven_outlet.create', compact('produks', 'kondisi', 'gallery'));
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
        $duplicate = InventoryOutlet::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryOutlet::create($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_outlet.index'))->with('success', 'Data tersimpan');
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
        $gallery = Lokasi::where('tipe_lokasi', 2)->get();
        return view('inven_outlet.show', compact('data', 'produks', 'kondisi', 'gallery'));
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
        $gallery = Lokasi::where('tipe_lokasi', 2)->get();
        return view('inven_outlet.edit', compact('data', 'produks', 'kondisi', 'gallery'));
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
