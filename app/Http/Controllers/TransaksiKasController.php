<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\Rekening;
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
    public function index_pusat(Request $req)
    {
        $queryMasuk = TransaksiKas::query();
        $queryKeluar = TransaksiKas::query();
        $rekenings = Rekening::whereHas('lokasi', function($q){
            $q->where('operasional_id', 1);
        })->get();
        $lokasis = Lokasi::where('operasional_id', 1)->orWhere('tipe_lokasi', 1)->get();
        if($req->lokasi){
            $queryMasuk->where('lokasi_penerima', $req->lokasi);
            $queryKeluar->where('lokasi_pengirim', $req->lokasi);
        } else {
            $queryMasuk->whereHas('lok_penerima', function($q){
                $q->where('operasional_id', 1);
            });
            $queryKeluar->whereHas('lok_pengirim', function($q){
                $q->where('operasional_id', 1);
            });
        }
        if($req->rekening){
            $queryMasuk->where('rekening_penerima', $req->rekening);
            $queryKeluar->where('rekening_pengirim', $req->rekening);
        }
        $dataMasuk = $queryMasuk->get();
        $dataKeluar = $queryKeluar->get();
        $saldoMasuk = $queryMasuk->where('status', 'DIKONFIRMASI')->get()->sum('nominal');
        $saldoKeluar = $queryKeluar->where('status', 'DIKONFIRMASI')->get()->sum('nominal') + $queryKeluar->where('status', 'DIKONFIRMASI')->get()->sum('biaya_lain');
        
        return view('kas_pusat.index', compact('dataMasuk', 'dataKeluar', 'lokasis', 'rekenings', 'saldoMasuk', 'saldoKeluar'));
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
            'lokasi_pengirim' => 'required|numeric|exists:lokasis,id',
            'metode' => 'required|in:Transfer,Cash',
            'rekening_pengirim' => 'required_if:metode,Transfer|numeric|exists:rekenings,id',
            'jenis' => 'required|in:Lainnya,Pemindahan Saldo',
            'keterangan' => 'required',
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['status'] = 'DIKONFIRMASI';

        // save data
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = 'kas' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_transaksi_kas', $fileName, 'public');
            $data['file'] = $filePath;
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
            'lokasi_pengirim' => 'required|numeric|exists:lokasis,id',
            'metode' => 'required|in:Transfer,Cash',
            'rekening_pengirim' => 'required_if:metode,Transfer|numeric|exists:rekenings,id',
            'jenis' => 'required|in:Lainnya,Pemindahan Saldo',
            'keterangan' => 'required',
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // Fetch existing record
        $existingTransaksi = TransaksiKas::find($transaksiKas);
        if (!$existingTransaksi) {
            return redirect()->back()->with('fail', 'Transaksi tidak ditemukan.');
        }
        if($req->metode == 'Cash'){
            $data['rekening_penerima'] = null;
            $data['rekening_pengirim'] = null;
        }

        // save data
        $data['file'] = $existingTransaksi->file;
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = 'kas' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_transaksi_kas', $fileName, 'public');
            $data['file'] = $filePath;
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
        // $data = TransaksiKas::find($transaksiKas);
        // if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        // $check = $data->delete();
        // if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        // return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function index_gallery(Request $req)
    {
        $queryMasuk = TransaksiKas::query();
        $queryKeluar = TransaksiKas::query();
        $rekenings = Rekening::whereHas('lokasi', function($q){
            $q->where('tipe_lokasi', 1);
        })->get();
        $lokasis = Lokasi::where('tipe_lokasi', 1)->get();
        if($req->lokasi){
            $queryMasuk->where('lokasi_penerima', $req->lokasi);
            $queryKeluar->where('lokasi_pengirim', $req->lokasi);
            $lokasi_pengirim = $req->lokasi;
            $rekeningKeluar = Rekening::where('lokasi_id', $req->lokasi)->get();
        } else {
            if(Auth::user()->hasRole('AdminGallery')){
                $queryMasuk->where('lokasi_penerima', Auth::user()->karyawans->lokasi_id);
                $queryKeluar->where('lokasi_pengirim', Auth::user()->karyawans->lokasi_id);
                $lokasi_pengirim = Auth::user()->karyawans->lokasi_id;
                $rekeningKeluar = Rekening::where('lokasi_id', Auth::user()->karyawans->lokasi_id)->get();
            } else {
                $lokasi_pengirim = $lokasis->first()->id;
                $rekeningKeluar = Rekening::where('lokasi_id', $lokasi_pengirim)->get();
                $queryMasuk->where('lokasi_penerima', $lokasis->first()->id);
                $queryKeluar->where('lokasi_pengirim', $lokasis->first()->id);
            }
        }
        if($req->rekening){
            $queryMasuk->where('rekening_penerima', $req->rekening);
            $queryKeluar->where('rekening_pengirim', $req->rekening);
        }
        $dataMasuk = $queryMasuk->get();
        $dataKeluar = $queryKeluar->get();
        $saldoMasuk = $queryMasuk->where('status', 'DIKONFIRMASI')->get()->sum('nominal');
        $saldoKeluar = $queryKeluar->where('status', 'DIKONFIRMASI')->get()->sum('nominal') + $queryKeluar->where('status', 'DIKONFIRMASI')->get()->sum('biaya_lain');
        return view('kas_gallery.index', compact('dataMasuk', 'dataKeluar', 'lokasis', 'rekenings', 'rekeningKeluar', 'lokasi_pengirim', 'saldoMasuk', 'saldoKeluar'));
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
            'lokasi_pengirim' => 'required|numeric|exists:lokasis,id',
            'metode' => 'required|in:Transfer,Cash',
            'rekening_pengirim' => 'required_if:metode,Transfer|numeric|exists:rekenings,id',
            'jenis' => 'required|in:Lainnya,Pemindahan Saldo',
            'keterangan' => 'required',
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        if($req->jenis == 'Pemindahan Saldo'){
            if($data['rekening_penerima'] == $data['rekening_pengirim']) return redirect()->back()->withInput()->with('fail', 'Tidak bisa transfer ke rekening yang sama');
        }
        $data['status'] = 'DIKONFIRMASI';

        // save data
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = 'kas' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_transaksi_kas', $fileName, 'public');
            $data['file'] = $filePath;
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
            'lokasi_pengirim' => 'required|numeric|exists:lokasis,id',
            'metode' => 'required|in:Transfer,Cash',
            'rekening_pengirim' => 'required_if:metode,Transfer|numeric|exists:rekenings,id',
            'jenis' => 'required|in:Lainnya,Pemindahan Saldo',
            'keterangan' => 'required',
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
            'status' => 'required|in:DIKONFIRMASI,BATAL'
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
        $data['file'] = $existingTransaksi->file;
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = 'kas' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_transaksi_kas', $fileName, 'public');
            $data['file'] = $filePath;
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
        // $data = TransaksiKas::find($transaksiKas);
        // if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        // $check = $data->delete();
        // if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        // return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function rekeningPerLokasi(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'lokasi_id' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return response()->json($error, 400);
        $rekenings = Rekening::where('lokasi_id', $req->lokasi_id)->get();
        return response()->json($rekenings);
    }
}
