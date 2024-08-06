<?php

namespace App\Http\Controllers;

use App\Models\Komponen_Produk_Jual;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class KomponenProdukTerjualController extends Controller
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Komponen_Produk_Terjual  $komponen_Produk_Terjual
     * @return \Illuminate\Http\Response
     */
    public function show(Komponen_Produk_Terjual $komponen_Produk_Terjual)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Komponen_Produk_Terjual  $komponen_Produk_Terjual
     * @return \Illuminate\Http\Response
     */
    public function edit(Komponen_Produk_Terjual $komponen_Produk_Terjual)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Komponen_Produk_Terjual  $komponen_Produk_Terjual
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Komponen_Produk_Terjual $komponen_Produk_Terjual)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Komponen_Produk_Terjual  $komponen_Produk_Terjual
     * @return \Illuminate\Http\Response
     */
    public function destroy(Komponen_Produk_Terjual $komponen_Produk_Terjual)
    {
        //
    }

    public function addKomponen(Request $req)
    {
        // Validasi
        $validator = Validator::make($req->all(), [
            'data_produk_id' => 'required',
            'new_produk' => 'required',
            'jml_tambahan' => 'required',
            'new_produk_kondisi' => 'required',
        ]);

        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        DB::beginTransaction();

        try {
            $produkTerjual = Produk_Terjual::find($data['data_produk_id']);

            $oldBungaPot = Komponen_Produk_Terjual::where('produk_terjual_id', $produkTerjual->id)
                                                ->whereIn('tipe_produk', [1, 2])
                                                ->delete();

            for ($i = 0; $i < count($data['new_produk']); $i++) {
                $produk = Produk::find($data['new_produk'][$i]);

                if (!$produk) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }

                $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                    'produk_terjual_id' => $produkTerjual->id,
                    'kode_produk' => $produk->kode,
                    'nama_produk' => $produk->nama,
                    'tipe_produk' => $produk->tipe_produk,
                    'kondisi' => $data['new_produk_kondisi'][$i],
                    'deskripsi' => $produk->deskripsi,
                    'jumlah' => $data['jml_tambahan'][$i],
                    'harga_satuan' => 0,
                    'harga_total' => 0
                ]);

                if (!$komponen_produk_terjual) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }
            }

            DB::commit();

            $route = explode(',', $req->route);
            if (count($route) == 1) {
                return redirect()->route($route[0])->with('success', 'Produk berhasil ditambahkan');
            } else {
                return redirect()->route($route[0], [$route[1] => $route[2]])->with('success', 'Produk berhasil ditambahkan');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
    }
}
