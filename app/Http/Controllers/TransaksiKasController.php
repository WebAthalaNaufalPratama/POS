<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Lokasi;
use App\Models\Pembayaran;
use App\Models\Rekening;
use App\Models\TransaksiKas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

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
        $queryMasuk = TransaksiKas::with('lok_penerima', 'lok_pengirim', 'rek_pengirim', 'rek_penerima');
        $queryKeluar = TransaksiKas::with('lok_penerima', 'lok_pengirim', 'rek_pengirim', 'rek_penerima');
        $queryRekening = Rekening::query();

        // Apply filters for location and rekening if provided
        if ($req->lokasi) {
            $queryMasuk->where('lokasi_penerima', $req->lokasi);
            $queryKeluar->where('lokasi_pengirim', $req->lokasi);
            $queryRekening->where('lokasi_id', $req->lokasi);
        } else {
            $queryMasuk->whereHas('lok_penerima', fn($q) => $q->where('operasional_id', 1));
            $queryKeluar->whereHas('lok_pengirim', fn($q) => $q->where('operasional_id', 1));
            $dataLokasi = Lokasi::where('operasional_id', 1)->pluck('id');
            $queryRekening->whereIn('lokasi_id', $dataLokasi);
        }

        if ($req->rekening) {
            $queryMasuk->where('rekening_penerima', $req->rekening);
            $queryKeluar->where('rekening_pengirim', $req->rekening);
            $queryRekening->where('id', $req->rekening);
        }

        $saldoAwal = $queryRekening->sum('saldo_awal');
        // start datatable masuk
            if ($req->ajax() && $req->table == 'masuk') {
                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $queryMasuk->where(function($q) use ($search) {
                        $q->where('metode', 'like', "%$search%")
                        ->orWhere('nominal', 'like', "%$search%")
                        ->orWhere('biaya_lain', 'like', "%$search%")
                        ->orWhere('tanggal', 'like', "%$search%")
                        ->orWhere('jenis', 'like', "%$search%")
                        ->orWhere('keterangan', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%")
                        ->orWhereHas('lok_penerima', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('lok_pengirim', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('rek_pengirim', function($c) use($search){
                            $c->where('nama_akun', 'like', "%$search%");
                        })
                        ->orWhereHas('rek_penerima', function($c) use($search){
                            $c->where('nama_akun', 'like', "%$search%");
                        });
                    });
                }
        
                $queryMasuk->orderBy($columnName, $dir);
                $recordsFiltered = $queryMasuk->count();
                $tempData = $queryMasuk->offset($start)->limit($length)->get();
        
                $currentPage = ($start / $length) + 1;
                $perPage = $length;
            
                $dataMasuk = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
                    $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                    $item->tanggal = tanggalindo($item->tanggal);
                    $item->nominal = formatRupiah($item->nominal);
                    $item->biaya_lain = formatRupiah($item->biaya_lain);
                    $item->nama_rek_penerima = $item->rek_penerima ? $item->rek_penerima->nama_akun : '-';
                    $item->nama_rek_pengirim = $item->rek_pengirim ? $item->rek_pengirim->nama_akun : '-';
                    return $item;
                });

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => TransaksiKas::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $dataMasuk,
                ]);

            }
        // end datatable masuk

        // start datatable keluar
            if ($req->ajax() && $req->table == 'keluar') {
                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $queryKeluar->where(function($q) use ($search) {
                        $q->where('metode', 'like', "%$search%")
                        ->orWhere('nominal', 'like', "%$search%")
                        ->orWhere('biaya_lain', 'like', "%$search%")
                        ->orWhere('tanggal', 'like', "%$search%")
                        ->orWhere('jenis', 'like', "%$search%")
                        ->orWhere('keterangan', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%")
                        ->orWhereHas('lok_penerima', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('lok_pengirim', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('rek_pengirim', function($c) use($search){
                            $c->where('nama_akun', 'like', "%$search%");
                        })
                        ->orWhereHas('rek_penerima', function($c) use($search){
                            $c->where('nama_akun', 'like', "%$search%");
                        });
                    });
                }
        
                $queryKeluar->orderBy($columnName, $dir);
                $recordsFiltered = $queryKeluar->count();
                $tempData = $queryKeluar->offset($start)->limit($length)->get();
        
                $currentPage = ($start / $length) + 1;
                $perPage = $length;
            
                $dataKeluar = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
                    $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                    $item->tanggal = tanggalindo($item->tanggal);
                    $item->nominal = formatRupiah($item->nominal);
                    $item->biaya_lain = formatRupiah($item->biaya_lain);
                    $item->nama_rek_penerima = $item->rek_penerima ? $item->rek_penerima->nama_akun : '-';
                    $item->nama_rek_pengirim = $item->rek_pengirim ? $item->rek_pengirim->nama_akun : '-';
                    return $item;
                });

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => TransaksiKas::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $dataKeluar,
                ]);

            }
        // end datatable keluar

        // Common condition for confirmed status
        $confirmedCondition = fn($q) => $q->where('status', 'DIKONFIRMASI');

        // Calculate totals using aggregated queries
        $saldoMasuk = $queryMasuk->clone()->where($confirmedCondition)->sum('nominal');
        $saldoKeluar = $queryKeluar->clone()->where($confirmedCondition)->sum('nominal') 
                    + $queryKeluar->clone()->where($confirmedCondition)->sum('biaya_lain');
        $saldo = $saldoMasuk - $saldoKeluar + $saldoAwal;

        $saldoMasukRekening = $queryMasuk->clone()
                                ->where($confirmedCondition)
                                ->where('metode', 'Transfer')
                                ->sum('nominal');
        $saldoKeluarRekening = $queryKeluar->clone()
                                ->where($confirmedCondition)
                                ->where('metode', 'Transfer')
                                ->sum('nominal') 
                        + $queryKeluar->clone()
                                ->where($confirmedCondition)
                                ->where('metode', 'Transfer')
                                ->sum('biaya_lain');
        $saldoRekening = ($saldoMasukRekening - $saldoKeluarRekening) + $saldoAwal;

        $saldoMasukCash = $queryMasuk->clone()
                            ->where($confirmedCondition)
                            ->where('metode', 'Cash')
                            ->sum('nominal');
        $saldoKeluarCash = $queryKeluar->clone()
                            ->where($confirmedCondition)
                            ->where('metode', 'Cash')
                            ->sum('nominal') 
                        + $queryKeluar->clone()
                            ->where($confirmedCondition)
                            ->where('metode', 'Cash')
                            ->sum('biaya_lain');
        $saldoCash = $saldoMasukCash - $saldoKeluarCash;

        // Get the basic data for locations and accounts
        $rekenings = Rekening::whereHas('lokasi', fn($q) => $q->where('operasional_id', 1))->get();
        $lokasis = Lokasi::where('operasional_id', 1)->orWhere('tipe_lokasi', 1)->get();

        return view('kas_pusat.index', compact(
            'lokasis', 'rekenings', 
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

        // store file
        if ($req->hasFile('file')) {
            // Simpan file baru
            $file = $req->file('file');
            $fileName = 'kas_pusat' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_transaksi_kas/' . $fileName;
        
            // Optimize dan simpan file baru
            Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                ->save(storage_path('app/public/' . $filePath));
        
            // Hapus file lama
            // if (!empty($pembayaran->bukti)) {
            //     $oldFilePath = storage_path('app/public/' . $pembayaran->bukti);
            //     if (File::exists($oldFilePath)) {
            //         File::delete($oldFilePath);
            //     }
            // }
        
            // Verifikasi penyimpanan file baru
            if (File::exists(storage_path('app/public/' . $filePath))) {
                $data['file'] = $filePath;
            } else {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
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

        // store file
        if ($req->hasFile('file')) {
            // Simpan file baru
            $file = $req->file('file');
            $fileName = 'kas_pusat' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_transaksi_kas/' . $fileName;
        
            // Optimize dan simpan file baru
            Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                ->save(storage_path('app/public/' . $filePath));
        
            // Hapus file lama
            if (!empty($existingTransaksi->file)) {
                $oldFilePath = storage_path('app/public/' . $existingTransaksi->file);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }
        
            // Verifikasi penyimpanan file baru
            if (File::exists(storage_path('app/public/' . $filePath))) {
                $data['file'] = $filePath;
            } else {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
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
        $queryMasuk = TransaksiKas::with('lok_penerima', 'lok_pengirim', 'rek_penerima', 'rek_pengirim');
        $queryKeluar = TransaksiKas::with('lok_penerima', 'lok_pengirim', 'rek_penerima', 'rek_pengirim');
        $queryRekening = Rekening::query();

        $rekenings = Rekening::whereHas('lokasi', function($q){
            $q->where('tipe_lokasi', 1);
        })->get();
        $lokasis = Lokasi::where('tipe_lokasi', 1)->get();

        if($req->lokasi){
            $queryMasuk->where('lokasi_penerima', $req->lokasi);
            $queryKeluar->where('lokasi_pengirim', $req->lokasi);
            $lokasi_pengirim = $req->lokasi;
            $rekeningKeluar = Rekening::where('lokasi_id', $req->lokasi)->get();
            $queryRekening->where('lokasi_id', $req->lokasi);
        } else {
            if(Auth::user()->hasRole('AdminGallery')){
                $queryMasuk->where('lokasi_penerima', Auth::user()->karyawans->lokasi_id);
                $queryKeluar->where('lokasi_pengirim', Auth::user()->karyawans->lokasi_id);
                $lokasi_pengirim = Auth::user()->karyawans->lokasi_id;
                $rekeningKeluar = Rekening::where('lokasi_id', Auth::user()->karyawans->lokasi_id)->get();
                $queryRekening->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
            } else {
                $lokasi_pengirim = $lokasis->first()->id;
                $rekeningKeluar = Rekening::where('lokasi_id', $lokasi_pengirim)->get();
                $queryMasuk->where('lokasi_penerima', $lokasis->first()->id);
                $queryKeluar->where('lokasi_pengirim', $lokasis->first()->id);
                $queryRekening->where('lokasi_id', $lokasis->first()->id);
            }
        }
        if($req->rekening){
            $queryMasuk->where('rekening_penerima', $req->rekening);
            $queryKeluar->where('rekening_pengirim', $req->rekening);
            $queryRekening->where('id', $req->rekening);
        }

        $saldoAwal = $queryRekening->sum('saldo_awal');
        // start datatable masuk
            if ($req->ajax() && $req->table == 'masuk') {
                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $queryMasuk->where(function($q) use ($search) {
                        $q->where('metode', 'like', "%$search%")
                        ->orWhere('nominal', 'like', "%$search%")
                        ->orWhere('biaya_lain', 'like', "%$search%")
                        ->orWhere('tanggal', 'like', "%$search%")
                        ->orWhere('jenis', 'like', "%$search%")
                        ->orWhere('keterangan', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%")
                        ->orWhereHas('lok_penerima', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('lok_pengirim', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('rek_pengirim', function($c) use($search){
                            $c->where('nama_akun', 'like', "%$search%");
                        })
                        ->orWhereHas('rek_penerima', function($c) use($search){
                            $c->where('nama_akun', 'like', "%$search%");
                        });
                    });
                }
        
                $queryMasuk->orderBy($columnName, $dir);
                $recordsFiltered = $queryMasuk->count();
                $tempData = $queryMasuk->offset($start)->limit($length)->get();
        
                $currentPage = ($start / $length) + 1;
                $perPage = $length;
            
                $dataMasuk = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
                    $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                    $item->tanggal = tanggalindo($item->tanggal);
                    $item->nominal = formatRupiah($item->nominal);
                    $item->biaya_lain = formatRupiah($item->biaya_lain);
                    $item->nama_rek_penerima = $item->rek_penerima ? $item->rek_penerima->nama_akun : '-';
                    $item->nama_rek_pengirim = $item->rek_pengirim ? $item->rek_pengirim->nama_akun : '-';
                    return $item;
                });

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => TransaksiKas::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $dataMasuk,
                ]);

            }
        // end datatable masuk

        // start datatable keluar
            if ($req->ajax() && $req->table == 'keluar') {
                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $queryKeluar->where(function($q) use ($search) {
                        $q->where('metode', 'like', "%$search%")
                        ->orWhere('nominal', 'like', "%$search%")
                        ->orWhere('biaya_lain', 'like', "%$search%")
                        ->orWhere('tanggal', 'like', "%$search%")
                        ->orWhere('jenis', 'like', "%$search%")
                        ->orWhere('keterangan', 'like', "%$search%")
                        ->orWhere('status', 'like', "%$search%")
                        ->orWhereHas('lok_penerima', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('lok_pengirim', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('rek_pengirim', function($c) use($search){
                            $c->where('nama_akun', 'like', "%$search%");
                        })
                        ->orWhereHas('rek_penerima', function($c) use($search){
                            $c->where('nama_akun', 'like', "%$search%");
                        });
                    });
                }
        
                $queryKeluar->orderBy($columnName, $dir);
                $recordsFiltered = $queryKeluar->count();
                $tempData = $queryKeluar->offset($start)->limit($length)->get();
        
                $currentPage = ($start / $length) + 1;
                $perPage = $length;
            
                $dataKeluar = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
                    $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                    $item->tanggal = tanggalindo($item->tanggal);
                    $item->nominal = formatRupiah($item->nominal);
                    $item->biaya_lain = formatRupiah($item->biaya_lain);
                    $item->nama_rek_penerima = $item->rek_penerima ? $item->rek_penerima->nama_akun : '-';
                    $item->nama_rek_pengirim = $item->rek_pengirim ? $item->rek_pengirim->nama_akun : '-';
                    return $item;
                });

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => TransaksiKas::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $dataKeluar,
                ]);

            }
        // end datatable keluar

        // Common condition for confirmed status
        $confirmedCondition = fn($q) => $q->where('status', 'DIKONFIRMASI');

        // Calculate totals using aggregated queries
        $saldoMasuk = $queryMasuk->clone()->where($confirmedCondition)->sum('nominal');
        $saldoKeluar = $queryKeluar->clone()->where($confirmedCondition)->sum('nominal') 
                    + $queryKeluar->clone()->where($confirmedCondition)->sum('biaya_lain');
        $saldo = $saldoMasuk - $saldoKeluar + $saldoAwal;

        $saldoMasukRekening = $queryMasuk->clone()
                                ->where($confirmedCondition)
                                ->where('metode', 'Transfer')
                                ->sum('nominal');
        $saldoKeluarRekening = $queryKeluar->clone()
                                ->where($confirmedCondition)
                                ->where('metode', 'Transfer')
                                ->sum('nominal') 
                        + $queryKeluar->clone()
                                ->where($confirmedCondition)
                                ->where('metode', 'Transfer')
                                ->sum('biaya_lain');
        $saldoRekening = ($saldoMasukRekening - $saldoKeluarRekening) + $saldoAwal;

        $saldoMasukCash = $queryMasuk->clone()
                                ->where($confirmedCondition)
                                ->where('metode', 'Cash')
                                ->sum('nominal');
        $saldoKeluarCash = $queryKeluar->clone()
                                ->where($confirmedCondition)
                                ->where('metode', 'Cash')
                                ->sum('nominal') 
                            + $queryKeluar->clone()
                                ->where($confirmedCondition)
                                ->where('metode', 'Cash')
                                ->sum('biaya_lain');
        $saldoCash = $saldoMasukCash - $saldoKeluarCash;

        return view('kas_gallery.index', compact(
            'lokasis', 'rekenings', 
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

        // store file
        if ($req->hasFile('file')) {
            // Simpan file baru
            $file = $req->file('file');
            $fileName = 'kas_gallery' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_transaksi_kas/' . $fileName;
        
            // Optimize dan simpan file baru
            Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                ->save(storage_path('app/public/' . $filePath));
        
            // Hapus file lama
            // if (!empty($pembayaran->bukti)) {
            //     $oldFilePath = storage_path('app/public/' . $pembayaran->bukti);
            //     if (File::exists($oldFilePath)) {
            //         File::delete($oldFilePath);
            //     }
            // }
        
            // Verifikasi penyimpanan file baru
            if (File::exists(storage_path('app/public/' . $filePath))) {
                $data['file'] = $filePath;
            } else {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
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
        // store file
        if ($req->hasFile('file')) {
            // Simpan file baru
            $file = $req->file('file');
            $fileName = 'kas_gallery' . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_transaksi_kas/' . $fileName;
        
            // Optimize dan simpan file baru
            Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                ->save(storage_path('app/public/' . $filePath));
        
            // Hapus file lama
            if (!empty($existingTransaksi->file)) {
                $oldFilePath = storage_path('app/public/' . $existingTransaksi->file);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }
        
            // Verifikasi penyimpanan file baru
            if (File::exists(storage_path('app/public/' . $filePath))) {
                $data['file'] = $filePath;
            } else {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
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
