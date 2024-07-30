<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Ongkir;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OngkirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $ongkirs = Ongkir::with('lokasi')->get();
        $lokasis = Lokasi::where('tipe_lokasi', 1)->get();
        return view('ongkir.index', compact('ongkirs', 'lokasis'));
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
            'lokasi_id' => 'required|integer',
            'biaya' => 'required|integer',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // save data
        $check = Ongkir::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ongkir  $ongkir
     * @return \Illuminate\Http\Response
     */
    public function show(Ongkir $ongkir)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ongkir  $ongkir
     * @return \Illuminate\Http\Response
     */
    public function edit($ongkir)
    {
        $data = Ongkir::find($ongkir);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ongkir  $ongkir
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $ongkir)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required',
            'lokasi_id' => 'required|integer',
            'biaya' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // update data
        $check = Ongkir::find($ongkir)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data berhsail diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ongkir  $ongkir
     * @return \Illuminate\Http\Response
     */
    public function destroy($ongkir)
    {
        $data = Ongkir::find($ongkir);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
