<?php

namespace App\Http\Controllers;

use App\Models\Operasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OperasionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $operasional = Operasional::all();
        return view('operasional.index', compact('operasional'));
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
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        if (Operasional::where('nama', $req->nama)->exists()) return redirect()->back()->withInput()->with('fail', 'Nama sudah digunakan');
        $data = $req->except(['_token', '_method']);

        // save data
        $check = Operasional::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Operasional  $operasional
     * @return \Illuminate\Http\Response
     */
    public function show(Operasional $operasional)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Operasional  $operasional
     * @return \Illuminate\Http\Response
     */
    public function edit($operasional)
    {
        $data = Operasional::find($operasional);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Operasional  $operasional
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $operasional)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        if (Operasional::where('nama', $req->nama)->where('id', '!=', $operasional)->exists()) return redirect()->back()->withInput()->with('fail', 'Nama sudah digunakan');
        $data = $req->except(['_token', '_method']);

        // save data
        $check = Operasional::find($operasional)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Operasional  $operasional
     * @return \Illuminate\Http\Response
     */
    public function destroy($operasional)
    {
        $data = Operasional::find($operasional);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
