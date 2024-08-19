<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Promo;
use App\Models\Lokasi;
use App\Models\Produk_Jual;
use App\Models\Tipe_Produk;
use App\Models\Karyawan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::orderByDesc('id')->get();
        $lokasis = Lokasi::all();
        $produk_juals = Produk_Jual::all();
        $tipe_produks = Tipe_Produk::where('kategori', 'Jual')->get();
        return view('promo.index', compact('promos', 'lokasis', 'produk_juals', 'tipe_produks'));
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
            'tanggal_mulai' => 'required',
            'tanggal_berakhir' => 'required',
            'ketentuan' => 'required',
            'diskon' => 'required',
            'lokasi_id' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // save data
        $check = Promo::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(Promo $promo)
    {
        $data = Promo::find($promo);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function edit($promo)
    {
        $data = Promo::find($promo);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $promo)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_berakhir' => 'required',
            'ketentuan' => 'required',
            'diskon' => 'required',
            'lokasi_id' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // update data
        $check = Promo::find($promo)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data berhsail diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy($promo)
    {
        $data = Promo::find($promo);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function checkPromo(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'total_transaksi' => 'required',
            'tipe_produk' => 'required',
            'produk' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return response()->json(['msg' => $error], 400);
        $data = $req->except(['_token', '_method']);
        $tanggalSekarang = Carbon::now()->toDateString();
        // $lokasi = Auth::user()->karyawan->lokasi_id;
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->first();
        $semualokasi = Lokasi::where('nama', 'Semua Lokasi')->first();
        // check promo minimal transaksi
        $promoMinTransaksi = [];
        $minTransaksi = Promo::where(function($query) use ($lokasi, $semualokasi) {
            $query->where('lokasi_id', $lokasi->lokasi_id)
                  ->orWhere('lokasi_id', $semualokasi->id);
        })->where('ketentuan', 'min_transaksi')->where('ketentuan_min_transaksi', '<', intval($data['total_transaksi']))->whereDate('tanggal_mulai', '<=', $tanggalSekarang)->whereDate('tanggal_berakhir', '>=', $tanggalSekarang)->get();
        if($minTransaksi->isNotEmpty()){
            foreach ($minTransaksi as $item) {
                $promoMinTransaksi[] = $item;
            }
        }
        // check promo produk
        $promoProduk = [];
        foreach ($data['produk'] as $item) {
            $checkProduk = Promo::where(function($query) use ($lokasi, $semualokasi) {
                $query->where('lokasi_id', $lokasi->lokasi_id)
                      ->orWhere('lokasi_id', $semualokasi->id);
            })->where('ketentuan', 'produk')->where('ketentuan_produk', $item)->whereDate('tanggal_mulai', '<=', $tanggalSekarang)->whereDate('tanggal_berakhir', '>=', $tanggalSekarang)->get();
            if($checkProduk->isNotEmpty()){
                foreach ($checkProduk as $item) {
                    $promoProduk[] = $item;
                }
            }
        }
        
        // check promo tipe_produk
        $promoTipeProduk = [];
        foreach ($data['tipe_produk'] as $item) {
            $checkTipeProduk = Promo::where(function($query) use ($lokasi, $semualokasi) {
                $query->where('lokasi_id', $lokasi->lokasi_id)
                      ->orWhere('lokasi_id', $semualokasi->id);
            })->where('ketentuan', 'tipe_produk')->where('ketentuan_tipe_produk', $item)->whereDate('tanggal_mulai', '<=', $tanggalSekarang)->whereDate('tanggal_berakhir', '>=', $tanggalSekarang)->get();
            if($checkTipeProduk->isNotEmpty()){
                foreach ($checkTipeProduk as $item) {
                    $promoTipeProduk[] = $item;
                }
            }
        }
        // dd($data['tipe_produk']);
        $validPromo = array(
            'produk' => $promoProduk,
            'tipe_produk' => $promoTipeProduk,
            'min_transaksi' => $promoMinTransaksi,
        );

        return response()->json($validPromo);
    }

    public function getPromo(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'promo_id' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return response()->json(['msg' => $error], 400);
        $data = $req->except(['_token', '_method']);

        $promo = Promo::with('free_produk')->find($data['promo_id']);

        return response()->json($promo);
    }
}
