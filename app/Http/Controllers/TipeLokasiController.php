<?php

namespace App\Http\Controllers;

use App\Models\Tipe_Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TipeLokasiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tipe_lokasis = Tipe_Lokasi::all();
        return view('tipe_lokasis.index', compact('tipe_lokasis'));
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
            'deskripsi' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // save data
        $check = Tipe_Lokasi::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tipe_Lokasi  $tipe_Lokasi
     * @return \Illuminate\Http\Response
     */
    public function show(Tipe_Lokasi $tipe_Lokasi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tipe_Lokasi  $tipe_Lokasi
     * @return \Illuminate\Http\Response
     */
    public function edit($tipe_Lokasi)
    {
        $data = Tipe_Lokasi::find($tipe_Lokasi);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tipe_Lokasi  $tipe_Lokasi
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $tipe_Lokasi)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required',
            'deskripsi' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // update data
        $check = Tipe_Lokasi::find($tipe_Lokasi)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data berhsail diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tipe_Lokasi  $tipe_Lokasi
     * @return \Illuminate\Http\Response
     */
    public function destroy($tipe_Lokasi)
    {
        $data = Tipe_Lokasi::find($tipe_Lokasi);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
