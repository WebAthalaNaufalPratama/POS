<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TransaksiKasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_pusat()
    {
        $data = TransaksiKas::all();
        $lokasis = Lokasi::all();
        $akuns = Akun::all();
        $totalOperasional = $data->sum('harga_total');
        $totalSewa = Pembayaran::whereHas('sewa')->get()->sum('nominal');
        $totalPenjualan = Pembayaran::whereHas('penjualan')->get()->sum('nominal');
        $saldo = $totalSewa + $totalPenjualan - $totalOperasional;
        return view('kas_pusat.index', compact('data', 'lokasis', 'akuns', 'saldo', 'totalOperasional', 'totalSewa', 'totalPenjualan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_pusat()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_pusat(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'akun_id' => 'required|numeric',
            'keterangan' => 'required',
            'kuantitas' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
            'harga_total' => 'required|numeric',
            'lokasi_id' => 'required',
            'tanggal_transaksi' => 'required|date',
            'bukti' => 'required|file',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['status'] = 'AKTIF';

        // save data
        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = 'kas' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_transaksi_kas', $fileName, 'public');
            $data['bukti'] = $filePath;
        }
        $check = TransaksiKas::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransaksiKas  $transaksiKas
     * @return \Illuminate\Http\Response
     */
    public function show_pusat(TransaksiKas $transaksiKas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransaksiKas  $transaksiKas
     * @return \Illuminate\Http\Response
     */
    public function edit_pusat($transaksiKas)
    {
        $data = TransaksiKas::find($transaksiKas);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransaksiKas  $transaksiKas
     * @return \Illuminate\Http\Response
     */
    public function update_pusat(Request $req, $transaksiKas)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'akun_id' => 'required|numeric',
            'keterangan' => 'required',
            'kuantitas' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
            'harga_total' => 'required|numeric',
            'lokasi_id' => 'required',
            'tanggal_transaksi' => 'required|date',
            'status' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // Fetch existing record
        $existingTransaksi = TransaksiKas::find($transaksiKas);
        if (!$existingTransaksi) {
            return redirect()->back()->with('fail', 'Transaksi tidak ditemukan.');
        }

        // save data
        $data['bukti'] = $existingTransaksi->bukti;
        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = 'kas' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_transaksi_kas', $fileName, 'public');
            $data['bukti'] = $filePath;
        }
        $check = TransaksiKas::find($transaksiKas)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransaksiKas  $transaksiKas
     * @return \Illuminate\Http\Response
     */
    public function destroy_pusat($transaksiKas)
    {
        $data = TransaksiKas::find($transaksiKas);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function index_gallery()
    {
        $data = TransaksiKas::whereHas('lokasi', function($z){
            $z->where('operasional_id', Auth::user()->karyawans->lokasi->operasional_id);
        })->get();
        $lokasis = Lokasi::all();
        $akuns = Akun::all();
        $totalOperasional = $data->sum('harga_total');
        $totalSewa = Pembayaran::whereHas('sewa.sewa.lokasi', function($q) {
            $q->where('operasional_id', Auth::user()->karyawans->lokasi->operasional_id);
        })->get();
        dd($totalSewa);
        $totalPenjualan = Pembayaran::whereHas('penjualan.lokasi', function($q) {
            $q->where('operasional_id', Auth::user()->karyawans->lokasi->operasional_id);
        })->get()->sum('nominal');
        $saldo = $totalSewa + $totalPenjualan - $totalOperasional;
        return view('kas_gallery.index', compact('data', 'lokasis', 'akuns', 'saldo', 'totalOperasional', 'totalSewa', 'totalPenjualan'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_gallery()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_gallery(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'akun_id' => 'required|numeric',
            'keterangan' => 'required',
            'kuantitas' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
            'harga_total' => 'required|numeric',
            'lokasi_id' => 'required',
            'tanggal_transaksi' => 'required|date',
            'bukti' => 'required|file',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['status'] = 'AKTIF';

        // save data
        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = 'kas' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_transaksi_kas', $fileName, 'public');
            $data['bukti'] = $filePath;
        }
        $check = TransaksiKas::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TransaksiKas  $transaksiKas
     * @return \Illuminate\Http\Response
     */
    public function show_gallery(TransaksiKas $transaksiKas)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TransaksiKas  $transaksiKas
     * @return \Illuminate\Http\Response
     */
    public function edit_gallery($transaksiKas)
    {
        $data = TransaksiKas::find($transaksiKas);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TransaksiKas  $transaksiKas
     * @return \Illuminate\Http\Response
     */
    public function update_gallery(Request $req, $transaksiKas)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'akun_id' => 'required|numeric',
            'keterangan' => 'required',
            'kuantitas' => 'required|numeric',
            'harga_satuan' => 'required|numeric',
            'harga_total' => 'required|numeric',
            'lokasi_id' => 'required',
            'tanggal_transaksi' => 'required|date',
            'status' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // Fetch existing record
        $existingTransaksi = TransaksiKas::find($transaksiKas);
        if (!$existingTransaksi) {
            return redirect()->back()->with('fail', 'Transaksi tidak ditemukan.');
        }

        // save data
        $data['bukti'] = $existingTransaksi->bukti;
        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = 'kas' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_transaksi_kas', $fileName, 'public');
            $data['bukti'] = $filePath;
        }
        $check = TransaksiKas::find($transaksiKas)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TransaksiKas  $transaksiKas
     * @return \Illuminate\Http\Response
     */
    public function destroy_gallery($transaksiKas)
    {
        $data = TransaksiKas::find($transaksiKas);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
