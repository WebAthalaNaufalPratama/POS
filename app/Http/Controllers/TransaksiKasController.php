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
        // Define the base query for incoming and outgoing transactions
        $queryMasuk = TransaksiKas::query();
        $queryKeluar = TransaksiKas::query();

        // Apply filters for location and rekening if provided
        if ($req->lokasi) {
            $queryMasuk->where('lokasi_penerima', $req->lokasi);
            $queryKeluar->where('lokasi_pengirim', $req->lokasi);
        } else {
            $queryMasuk->whereHas('lok_penerima', fn($q) => $q->where('operasional_id', 1));
            $queryKeluar->whereHas('lok_pengirim', fn($q) => $q->where('operasional_id', 1));
        }

        if ($req->rekening) {
            $queryMasuk->where('rekening_penerima', $req->rekening);
            $queryKeluar->where('rekening_pengirim', $req->rekening);
        }

        // Common condition for confirmed status
        $confirmedCondition = fn($q) => $q->where('status', 'DIKONFIRMASI');

        // Calculate totals using aggregated queries
        $saldoMasuk = $queryMasuk->clone()->where($confirmedCondition)->sum('nominal');
        $saldoKeluar = $queryKeluar->clone()->where($confirmedCondition)->sum('nominal') 
                    + $queryKeluar->clone()->where($confirmedCondition)->sum('biaya_lain');
        $saldo = $saldoMasuk - $saldoKeluar;

        $saldoMasukRekening = $queryMasuk->clone()->where($confirmedCondition)
                                            ->where('metode', 'Transfer')
                                            ->sum('nominal');
        $saldoKeluarRekening = $queryKeluar->clone()->where($confirmedCondition)
                                                ->where('metode', 'Transfer')
                                                ->sum('nominal') 
                        + $queryKeluar->clone()->where($confirmedCondition)
                                                ->where('metode', 'Transfer')
                                                ->sum('biaya_lain');
        $saldoRekening = $saldoMasukRekening - $saldoKeluarRekening;

        $saldoMasukCash = $queryMasuk->clone()->where($confirmedCondition)
                                            ->where('metode', 'Cash')
                                            ->sum('nominal');
        $saldoKeluarCash = $queryKeluar->clone()->where($confirmedCondition)
                                            ->where('metode', 'Cash')
                                            ->sum('nominal') 
                            + $queryKeluar->clone()->where($confirmedCondition)
                                            ->where('metode', 'Cash')
                                            ->sum('biaya_lain');
        $saldoCash = $saldoMasukCash - $saldoKeluarCash;

        // Retrieve data for view
        $dataMasuk = $queryMasuk->get();
        $dataKeluar = $queryKeluar->get();

        // Get the basic data for locations and accounts
        $rekenings = Rekening::whereHas('lokasi', fn($q) => $q->where('operasional_id', 1))->get();
        $lokasis = Lokasi::where('operasional_id', 1)->orWhere('tipe_lokasi', 1)->get();

        return view('kas_pusat.index', compact(
            'dataMasuk', 'dataKeluar', 'lokasis', 'rekenings', 
            'saldoMasuk', 'saldoKeluar', 'saldoMasukRekening', 
            'saldoKeluarRekening', 'saldoMasukCash', 'saldoKeluarCash', 
            'saldo', 'saldoRekening', 'saldoCash'
        ));
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

        // Common condition for confirmed status
        $confirmedCondition = fn($q) => $q->where('status', 'DIKONFIRMASI');

        // Calculate totals using aggregated queries
        $saldoMasuk = $queryMasuk->clone()->where($confirmedCondition)->sum('nominal');
        $saldoKeluar = $queryKeluar->clone()->where($confirmedCondition)->sum('nominal') 
                    + $queryKeluar->clone()->where($confirmedCondition)->sum('biaya_lain');
        $saldo = $saldoMasuk - $saldoKeluar;

        $saldoMasukRekening = $queryMasuk->clone()->where($confirmedCondition)
                                            ->where('metode', 'Transfer')
                                            ->sum('nominal');
        $saldoKeluarRekening = $queryKeluar->clone()->where($confirmedCondition)
                                                ->where('metode', 'Transfer')
                                                ->sum('nominal') 
                        + $queryKeluar->clone()->where($confirmedCondition)
                                                ->where('metode', 'Transfer')
                                                ->sum('biaya_lain');
        $saldoRekening = $saldoMasukRekening - $saldoKeluarRekening;

        $saldoMasukCash = $queryMasuk->clone()->where($confirmedCondition)
                                            ->where('metode', 'Cash')
                                            ->sum('nominal');
        $saldoKeluarCash = $queryKeluar->clone()->where($confirmedCondition)
                                            ->where('metode', 'Cash')
                                            ->sum('nominal') 
                            + $queryKeluar->clone()->where($confirmedCondition)
                                            ->where('metode', 'Cash')
                                            ->sum('biaya_lain');
        $saldoCash = $saldoMasukCash - $saldoKeluarCash;

        // Retrieve data for view
        $dataMasuk = $queryMasuk->get();
        $dataKeluar = $queryKeluar->get();
        
        return view('kas_gallery.index', compact(
            'dataMasuk', 'dataKeluar', 'lokasis', 'rekenings', 
            'saldoMasuk', 'saldoKeluar', 'saldoMasukRekening', 
            'saldoKeluarRekening', 'saldoMasukCash', 'saldoKeluarCash', 
            'saldo', 'saldoRekening', 'saldoCash', 'lokasi_pengirim', 'rekeningKeluar'
        ));
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

    public function cekSaldo($lokasi_id = null, $rekening_id = null)
    {
        if($lokasi_id != null){
            $transaksiKeluar =  TransaksiKas::where('lokasi_pengirim', $lokasi_id)->where('status', 'IKONFIRMASI')->get()->sum('nominal', 'biaya_lain');
            $transaksiMasuk =  TransaksiKas::where('lokasi_penerima', $lokasi_id)->where('status', 'IKONFIRMASI')->get()->sum('nominal');
        } elseif($rekening_id){

        }
    }
}
