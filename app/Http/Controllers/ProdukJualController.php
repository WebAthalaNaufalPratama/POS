<?php

namespace App\Http\Controllers;

use App\Models\Komponen_Produk_Jual;
use App\Models\Kondisi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Tipe_Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProdukJualController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $jenis = $req->path();
        if($jenis == 'tradisional'){
            $getTipe = Tipe_Produk::where('nama', 'tradisional')->first();
            $tradisionals = Produk_Jual::with('komponen')->where('tipe_produk', $getTipe->id)->get();
            return view('tradisional.index', compact('tradisionals'));
        }
        elseif($jenis == 'gift'){
            $getTipe = Tipe_Produk::where('nama', 'gift')->first();
            $gifts = Produk_Jual::with('komponen')->where('tipe_produk', $getTipe->id)->get();
            return view('gift.index', compact('gifts'));
        }
        else{
            return redirect()->back()->with('fail', 'Url salah');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $req)
    {
        $path = $req->path();
        $path = explode('/', $path);
        $jenis = $path[0];
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        if($jenis == 'tradisional'){

            // ambil tipe produk
            $getTipe = Tipe_Produk::where('nama', 'tradisional')->first();

            // penentuan kode produk
            $latestProduks = Produk_Jual::withTrashed()->where('tipe_produk', $getTipe->id)->orderBy('kode', 'desc')->get();
            if(count($latestProduks) < 1){
                $getKode = 'TRD-00001';
            } else {
                $lastProduk = $latestProduks->first();
                $kode = explode('-', $lastProduk->kode);
                $getKode = 'TRD-' . str_pad((int)$kode[1] + 1, 5, '0', STR_PAD_LEFT);
            }

            return view('tradisional.create', compact('getKode', 'produks', 'kondisi'));
        }
        elseif($jenis == 'gift'){

            // ambil tipe produk
            $getTipe = Tipe_Produk::where('nama', 'gift')->first();

            // penentuan kode produk
            $latestProduks = Produk_Jual::withTrashed()->where('tipe_produk', $getTipe->id)->orderBy('kode', 'desc')->get();
            if(count($latestProduks) < 1){
                $getKode = 'GFT-00001';
            } else {
                $lastProduk = $latestProduks->first();
                $kode = explode('-', $lastProduk->kode);
                $getKode = 'GFT-' . str_pad((int)$kode[1] + 1, 5, '0', STR_PAD_LEFT);
            }
            return view('gift.create', compact('getKode', 'produks', 'kondisi'));
        }
        else{
            return redirect()->back()->with('fail', 'Url salah');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        // dd($req);
        $path = $req->path();
        $path = explode('/', $path);
        $jenis = $path[0];
        if($jenis == 'tradisional'){
            // validasi
            $validator = Validator::make($req->all(), [
                'kode' => 'required',
                'nama' => 'required',
                'harga' => 'required|integer',
                'harga_jual' => 'required|integer',
                'deskripsi' => 'required',
            ]);
            $error = $validator->errors()->all();
            if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
            $data = $req->except(['_token', '_method']);

            // ambil tipe
            $getTipe = Tipe_Produk::where('nama', 'tradisional')->first()->id;

            // save data produk jual
            $data['tipe_produk'] = $getTipe;
            $check = Produk_Jual::create($data);
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            // save data komponen
            for ($i=0; $i < count($req->nama_produk); $i++) { 
                $produk = Produk::find($req->nama_produk[$i]);
                $datakomponen = array(
                    'produk_jual_id' => $check->id,
                    'kode_produk' => $produk->kode,
                    'nama_produk' => $produk->nama,
                    'tipe_produk' => $produk->tipe_produk,
                    'kondisi' => $req->kondisi[$i],
                    'deskripsi' => $produk->deskripsi,
                    'jumlah' => $req->jumlah[$i],
                    'harga_satuan' => $req->harga_satuan[$i],
                    'harga_total' => $req->harga_total[$i]
                );
                $check2 = Komponen_Produk_Jual::create($datakomponen);
                if(!$check2) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
            return redirect(route('tradisional.index'))->with('success', 'Data tersimpan');
        }
        elseif($jenis == 'gift'){

            return view('gift.create', compact('getKode'));
        }
        else{
            return redirect()->back()->with('fail', 'Url salah');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produk_Jual  $produk_Jual
     * @return \Illuminate\Http\Response
     */
    public function show(Produk_Jual $produk_Jual)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produk_Jual  $produk_Jual
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $req, $produk_Jual)
    {
        $jenis = $req->path();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk_Jual  $produk_Jual
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $produk_Jual)
    {
        $jenis = $req->path();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk_Jual  $produk_Jual
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $req, $produk_Jual)
    {
        $data = Produk_Jual::find($produk_Jual);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
