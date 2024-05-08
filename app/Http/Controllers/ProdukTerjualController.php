<?php

namespace App\Http\Controllers;

use App\Models\FormPerangkai;
use App\Models\Produk_Terjual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukTerjualController extends Controller
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
     * @param  \App\Models\Produk_Terjual  $produk_Terjual
     * @return \Illuminate\Http\Response
     */
    public function show(Produk_Terjual $produk_Terjual)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produk_Terjual  $produk_Terjual
     * @return \Illuminate\Http\Response
     */
    public function edit(Produk_Terjual $produk_Terjual)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk_Terjual  $produk_Terjual
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Produk_Terjual $produk_Terjual)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk_Terjual  $produk_Terjual
     * @return \Illuminate\Http\Response
     */
    public function destroy(Produk_Terjual $produk_Terjual)
    {
        //
    }

    public function getProdukTerjual(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'produk_id' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return response()->json(['msg' => $error], 400);
        $data = $req->except(['_token', '_method']);
        // dd($data);
        $result = Produk_Terjual::with('produk', 'perangkai', 'komponen')->find($data['produk_id']);
        $latestForm = FormPerangkai::withTrashed()->orderByDesc('id')->get();
            if(count($latestForm) < 1){
                $getKode = 'FRM' . date('Ymd') . '00001';
            } else {
                $lastForm = $latestForm->first();
                $kode = substr($lastForm->no_form, -5);
                $getKode = 'FRM' . date('Ymd') . str_pad((int)$kode + 1, 5, '0', STR_PAD_LEFT);
            }
        $result->kode_form = $getKode;
        return response()->json($result);
    }
}
