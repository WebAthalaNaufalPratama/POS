<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $produks = Produk::all();
        return view('produks.index', compact('produks'));
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
            'nama' => 'required',
            'tipe_produk' => 'required|integer',
            'deskripsi' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // penentuan kode produk
        $latestProduks = Produk::withTrashed()->orderBy('kode', 'desc')->get();
        if(count($latestProduks) < 1){
            $getKode = 'PRD-001';
        } else {
            $lastProduk = $latestProduks->first();
            $kode = explode('-', $lastProduk->kode);
            $getKode = 'PRD-' . str_pad((int)$kode[1] + 1, 3, '0', STR_PAD_LEFT);
        }
        $data['kode'] = $getKode;

        // save data
        $check = Produk::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(Produk $produk)
    {
        $data = Produk::find($produk);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function edit(Produk $produk)
    {
        $data = Produk::find($produk);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, Produk $produk)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required',
            'tipe_produk' => 'required|integer',
            'deskripsi' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // update data
        $check = $produk->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data berhsail diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produk $produk)
    {
        if(!$produk) return redirect()->back()->withInput()->with('fail', 'Data tidak ditemukan');
        $check = $produk->delete();
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menghapus data');
        return redirect()->back()->withInput()->with('success', 'Data berhasil dihapus');
    }
}