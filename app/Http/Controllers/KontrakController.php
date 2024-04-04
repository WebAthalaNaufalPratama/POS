<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Karyawan;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kondisi;
use App\Models\Kontrak;
use App\Models\Lokasi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use App\Models\Promo;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class KontrakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kontraks = Kontrak::all();
        return view('kontrak.index', compact('kontraks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $produkjuals = Produk_Jual::all();
        $lokasis = Lokasi::all();
        $customers = Customer::where('tipe', 'sewa')->get();
        $rekenings = Rekening::all();
        $promos = Promo::all();
        $sales = Karyawan::where('jabatan', 'sales')->get();

        $latestKontrak = Kontrak::withTrashed()->get();
            if(count($latestKontrak) < 1){
                $getKode = 'KSW-00001';
            } else {
                $lastKontrak = $latestKontrak->first();
                $kode = explode('-', $lastKontrak->no_kontrak);
                $getKode = 'KSW-' . str_pad((int)$kode[1] + 1, 5, '0', STR_PAD_LEFT);
            }

        return view('kontrak.create', compact('produkjuals', 'lokasis', 'customers', 'rekenings', 'promos', 'sales', 'getKode'));
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
        // validasi
        $validator = Validator::make($req->all(), [
            'no_kontrak' => 'required',
            'masa_sewa' => 'required|integer',
            'tanggal_kontrak' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_selesai' => 'required',
            'customer_id' => 'required',
            'pic' => 'required',
            'handphone' => 'required',
            'alamat' => 'required',
            'no_npwp' => 'required',
            'nama_npwp' => 'required',
            'ppn_nominal' => 'required',
            'pph_nominal' => 'required',
            'subtotal' => 'required',
            'total_harga' => 'required',
            'status' => 'required',
            'sales' => 'required',
            'rekening_id' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        // dd($data);
        $data['lokasi_id'] = 1;
        $data['pembuat'] = Auth::user()->id;
        $data['tanggal_pembuat'] = now();
        $data['tanggal_sales'] = now();
        $data['ongkir_id'] = 1;

        // save data kontrak
        $check = Kontrak::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        
        // save data produk kontrak
        for ($i=0; $i < count($data['nama_produk']); $i++) { 
            $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->id,
                'no_sewa' => $check->no_kontrak,
                'harga' => $data['harga_satuan'][$i],
                'jumlah' => $data['jumlah'][$i],
                'harga_jual' => $data['harga_total'][$i]
            ]);

            if(!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            foreach ($getProdukJual->komponen as $komponen ) {
                $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                    'produk_terjual_id' => $produk_terjual->id,
                    'kode_produk' => $komponen->kode_produk,
                    'nama_produk' => $komponen->nama_produk,
                    'tipe_produk' => $komponen->tipe_produk,
                    'kondisi' => $komponen->kondisi,
                    'deskripsi' => $komponen->deskripsi,
                    'jumlah' => $komponen->jumlah,
                    'harga_satuan' => $komponen->harga_satuan,
                    'harga_total' => $komponen->harga_total
                ]);
                if(!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
        }
        return redirect(route('kontrak.index'))->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kontrak  $kontrak
     * @return \Illuminate\Http\Response
     */
    public function show(Kontrak $kontrak)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kontrak  $kontrak
     * @return \Illuminate\Http\Response
     */
    public function edit(Kontrak $kontrak)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kontrak  $kontrak
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Kontrak $kontrak)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Kontrak  $kontrak
     * @return \Illuminate\Http\Response
     */
    public function destroy($kontrak)
    {
        $data = Kontrak::find($kontrak);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $getProduks = Produk_Terjual::where('no_sewa', $data->no_kontrak)->get();
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        if($getProduks){
            $getProduks->each->delete();
        }
        foreach ($getProduks as $item) {
            $getKomponenProduks = Komponen_Produk_Terjual::where('produk_terjual_id', $item->id)->get();
            if($getKomponenProduks){
                $getKomponenProduks->each->delete();
            }
        }
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
