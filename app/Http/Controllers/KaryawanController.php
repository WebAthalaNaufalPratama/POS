<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use Illuminate\Http\Request;
use App\Models\Karyawan;
use App\Models\Lokasi;
use Illuminate\Support\Facades\Validator;

class KaryawanController extends Controller
{
    public function index()
    {
        $karyawans = Karyawan::all();
        $lokasis = Lokasi::all();
        $jabatans = Jabatan::all();
        return view('karyawan.index', compact('karyawans', 'lokasis', 'jabatans'));
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
            'jabatan' => 'required',
            'lokasi_id' => 'required',
            'handphone' => 'required',
            'alamat' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['user_id'] = 0;

        // save data
        $check = Karyawan::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(Karyawan $karyawan)
    {
        $data = Karyawan::find($karyawan);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function edit($karyawan)
    {
        $data = Karyawan::find($karyawan);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $karyawan)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required',
            'jabatan' => 'required',
            'lokasi_id' => 'required',
            'handphone' => 'required',
            'alamat' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // update data
        $check = Karyawan::find($karyawan)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data berhsail diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy($karyawan)
    {
        $data = Karyawan::find($karyawan);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
