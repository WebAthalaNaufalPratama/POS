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
use Spatie\Activitylog\Models\Activity;

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
        $querySaldo = Rekening::query();

        // Apply filters for location and rekening if provided
        if ($req->lokasi) {
            $queryMasuk->where('lokasi_penerima', $req->lokasi);
            $queryKeluar->where('lokasi_pengirim', $req->lokasi);
            $querySaldo->where('lokasi_id', $req->lokasi);
        } else {
            $queryMasuk->whereHas('lok_penerima', fn($q) => $q->where('operasional_id', 1));
            $queryKeluar->whereHas('lok_pengirim', fn($q) => $q->where('operasional_id', 1));
            $dataLokasi = Lokasi::where('operasional_id', 1)->pluck('id');
            $querySaldo->whereIn('lokasi_id', $dataLokasi);
        }

        if ($req->rekening) {
            $getRekening = Rekening::find($req->rekening);
            if($getRekening->jenis == 'Rekening'){
                $queryMasuk->where('rekening_penerima', $req->rekening);
                $queryKeluar->where('rekening_pengirim', $req->rekening);
            } else {
                $queryMasuk->whereNull('rekening_penerima')->where('lokasi_penerima', $getRekening->lokasi_id);
                $queryKeluar->whereNull('rekening_pengirim')->where('lokasi_pengirim', $getRekening->lokasi_id);
            }
            $querySaldo->where('id', $req->rekening);
        }

        $saldoRekening = (clone $querySaldo)->where('jenis', 'Rekening')->sum('saldo_akhir');
        $saldoCash = (clone $querySaldo)->where('jenis', 'Cash')->sum('saldo_akhir');
        $saldo = $saldoCash + $saldoRekening;
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

        $rekenings = Rekening::whereHas('lokasi', fn($q) => $q->where('operasional_id', 1))->get();
        $akuns = Akun::all();
        $lokasis = Lokasi::where('operasional_id', 1)->orWhere('tipe_lokasi', 1)->get();

        return view('kas_pusat.index', compact(
            'lokasis', 'rekenings', 
            'saldoMasuk', 'saldoKeluar', 'saldoMasukRekening', 
            'saldoKeluarRekening', 'saldoMasukCash', 'saldoKeluarCash', 
            'saldo', 'saldoRekening', 'saldoCash', 'akuns'
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
        // Validasi input
        $validator = Validator::make($req->all(), [
            'akun_id' => 'required|exists:akuns,id',
            'metode' => 'required|in:Transfer,Cash',
            'jenis' => 'required|in:Lainnya,Pemindahan Saldo',
            'lokasi_pengirim' => 'required|exists:lokasis,id',
            'lokasi_penerima' => 'required_if:jenis,Pemindahan Saldo|exists:lokasis,id',
            'keterangan' => 'required',
            'nominal' => 'required|numeric',
            'biaya_lain' => 'nullable|min:0',
            'tanggal' => 'required|date',
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Conditional validation
        $validator->sometimes('rekening_penerima', 'required|exists:rekenings,id', function ($input) {
            return $input->metode === 'Transfer' && $input->jenis === 'Pemindahan Saldo';
        });
        $validator->sometimes('rekening_pengirim', 'required|exists:rekenings,id', function ($input) {
            return $input->metode === 'Transfer' && $input->jenis === 'Pemindahan Saldo';
        });

        // Jika validasi gagal
        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        // Cek saldo dan validasi rekening/cash di awal
        $totalKeluar = $req->nominal + ($req->biaya_lain ?? 0);

        if ($req->metode == 'Cash') {
            $dataPengirim = Rekening::where('jenis', 'Cash')->where('lokasi_id', $req->lokasi_pengirim)->first();
            if (!$dataPengirim) {
                return redirect()->back()->withInput()->with('fail', 'Data cash belum ada');
            }

            if ($dataPengirim->saldo_akhir < $totalKeluar) {
                return redirect()->back()->withInput()->with('fail', 'Saldo tidak mencukupi');
            }

            if ($req->jenis == 'Pemindahan Saldo') {
                $dataPenerima = Rekening::where('jenis', 'Cash')->where('lokasi_id', $req->lokasi_penerima)->first();
                if (!$dataPenerima) {
                    return redirect()->back()->withInput()->with('fail', 'Data cash penerima belum ada');
                }
            }
        } else {
            // Untuk metode Transfer
            $dataPengirim = Rekening::find($req->rekening_pengirim);
            if (!$dataPengirim) {
                return redirect()->back()->withInput()->with('fail', 'Data rekening pengirim belum ada');
            }

            if ($dataPengirim->saldo_akhir < $totalKeluar) {
                return redirect()->back()->withInput()->with('fail', 'Saldo tidak mencukupi');
            }

            if ($req->jenis == 'Pemindahan Saldo') {
                $dataPenerima = Rekening::find($req->rekening_penerima);
                if (!$dataPenerima) {
                    return redirect()->back()->withInput()->with('fail', 'Data rekening penerima belum ada');
                }
            }
        }

        $data = $req->except(['_token', '_method']);

        // Simpan file
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = 'kas_pusat' . date('YmdHis') . '.jpg';
            $filePath = 'bukti_transaksi_kas/' . $fileName;
        
            // Check file size (500KB = 500 * 1024 bytes)
            if ($file->getSize() > 500 * 1024) {
                Image::make($file)
                    ->encode('jpg', 70)
                    ->save(storage_path('app/public/' . $filePath));
            } else {
                Image::make($file)
                    ->encode('jpg')
                    ->save(storage_path('app/public/' . $filePath));
            }
        
            // Check if the file was saved successfully
            if (!File::exists(storage_path('app/public/' . $filePath))) {
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        
            // Save the file path
            $data['file'] = $filePath;
        }
        
        DB::beginTransaction();
        try {
            $data['status'] = 'DIKONFIRMASI';

            // Simpan transaksi kas
            $transaksi = TransaksiKas::create($data);
            if (!$transaksi) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }

            // Update saldo
            if ($req->metode == 'Cash') {
                $dataPengirim->subtractSaldo($totalKeluar);

                if ($req->jenis == 'Pemindahan Saldo') {
                    $dataPenerima->addSaldo($req->nominal);
                }
            } else {
                $dataPengirim->subtractSaldo($totalKeluar);

                if ($req->jenis == 'Pemindahan Saldo') {
                    $dataPenerima->addSaldo($req->nominal);
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data tersimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        // Validasi input
        $validator = Validator::make($req->all(), [
            'akun_id' => 'required|exists:akuns,id',
            'lokasi_pengirim' => 'required|numeric|exists:lokasis,id',
            'metode' => 'required|in:Transfer,Cash',
            'rekening_pengirim' => 'required_if:metode,Transfer|numeric|exists:rekenings,id',
            'jenis' => 'required|in:Lainnya,Pemindahan Saldo',
            'keterangan' => 'required',
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        // Fetch existing record
        $existingTransaksi = TransaksiKas::find($transaksiKas);
        if (!$existingTransaksi) {
            return redirect()->back()->with('fail', 'Transaksi tidak ditemukan.');
        }

        // Cek saldo
        $totalKeluar = $req->nominal + ($req->biaya_lain ?? 0);
        if ($req->metode == 'Transfer') {
            $saldo = TransaksiKas::getSaldo($req->rekening_pengirim, 'Transfer', 'DIKONFIRMASI', null, null);
            if ($saldo + $existingTransaksi->nominal < $totalKeluar) {
                return redirect()->back()->withInput()->with('fail', 'Saldo rekening tidak mencukupi');
            }
        } else {
            $saldo = TransaksiKas::getSaldo(null, 'Cash', 'DIKONFIRMASI', null, $req->lokasi_pengirim);
            if ($saldo + $existingTransaksi->nominal < $totalKeluar) {
                return redirect()->back()->withInput()->with('fail', 'Saldo cash tidak mencukupi');
            }
        }

        $data = $req->except(['_token', '_method']);
        $data['file'] = $existingTransaksi->file;

        // Handle file upload
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = 'kas_pusat' . date('YmdHis') . '.jpg';
            $filePath = 'bukti_transaksi_kas/' . $fileName;

            // Check file size and compress if necessary
            if ($file->getSize() > 500 * 1024) {
                Image::make($file)
                    ->encode('jpg', 70)
                    ->save(storage_path('app/public/' . $filePath));
            } else {
                Image::make($file)
                    ->encode('jpg')
                    ->save(storage_path('app/public/' . $filePath));
            }

            // Hapus file lama
            if (!empty($existingTransaksi->file)) {
                $oldFilePath = storage_path('app/public/' . $existingTransaksi->file);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }

            // Verifikasi penyimpanan file baru
            if (File::exists(storage_path('app/public/' . $filePath))) {
                $data['file'] = $filePath; // Save new file path
            } else {
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        }

        // Prepare data for update
        $data['rekening_penerima'] = $req->metode === 'Cash' ? null : $data['rekening_penerima'];

        // Update transaction
        DB::beginTransaction();
        try {
            if ($req->metode == 'Cash') {
                $dataPengirim = Rekening::where('jenis', 'Cash')->where('lokasi_id', $req->lokasi_pengirim)->first();
                if (!$dataPengirim) {
                    return redirect()->back()->withInput()->with('fail', 'Data cash pengirim belum ada');
                }

                $dataPengirim->addSaldo($existingTransaksi->nominal); // Revert the previous transaction
                $dataPengirim->subtractSaldo($totalKeluar); // Subtract new amount

                if ($req->jenis == 'Pemindahan Saldo') {
                    $dataPenerima = Rekening::where('jenis', 'Cash')->where('lokasi_id', $req->lokasi_penerima)->first();
                    if (!$dataPenerima) {
                        return redirect()->back()->withInput()->with('fail', 'Data cash penerima belum ada');
                    }
                    $dataPenerima->addSaldo($req->nominal); // Add new amount to the recipient
                }
            } else {
                $dataPengirim = Rekening::find($req->rekening_pengirim);
                if (!$dataPengirim) {
                    return redirect()->back()->withInput()->with('fail', 'Data rekening pengirim belum ada');
                }

                $dataPengirim->addSaldo($existingTransaksi->nominal); // Revert the previous transaction
                $dataPengirim->subtractSaldo($totalKeluar); // Subtract new amount

                if ($req->jenis == 'Pemindahan Saldo') {
                    $dataPenerima = Rekening::find($req->rekening_penerima);
                    if (!$dataPenerima) {
                        return redirect()->back()->withInput()->with('fail', 'Data rekening penerima belum ada');
                    }
                    $dataPenerima->addSaldo($req->nominal); // Add new amount to the recipient
                }
            }

            // Update the transaction record
            $check = $existingTransaksi->update($data);
            if (!$check) {
                return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        $querySaldo = Rekening::query();

        $rekenings = Rekening::whereHas('lokasi', function($q){
            $q->where('tipe_lokasi', 1);
        })->get();
        $akuns = Akun::all();
        $lokasis = Lokasi::where('tipe_lokasi', 1)->get();

        if($req->lokasi){
            $queryMasuk->where('lokasi_penerima', $req->lokasi);
            $queryKeluar->where('lokasi_pengirim', $req->lokasi);
            $lokasi_pengirim = $req->lokasi;
            $rekeningKeluar = Rekening::where('jenis', 'Rekening')->where('lokasi_id', $req->lokasi)->get();
            $querySaldo->where('lokasi_id', $req->lokasi);
        } else {
            if(Auth::user()->hasRole('AdminGallery')){
                $queryMasuk->where('lokasi_penerima', Auth::user()->karyawans->lokasi_id);
                $queryKeluar->where('lokasi_pengirim', Auth::user()->karyawans->lokasi_id);
                $lokasi_pengirim = Auth::user()->karyawans->lokasi_id;
                $rekeningKeluar = Rekening::where('jenis', 'Rekening')->where('lokasi_id', Auth::user()->karyawans->lokasi_id)->get();
                $querySaldo->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
            } else {
                $lokasi_pengirim = $lokasis->first()->id;
                $rekeningKeluar = Rekening::where('jenis', 'Rekening')->where('lokasi_id', $lokasi_pengirim)->get();
                $queryMasuk->where('lokasi_penerima', $lokasis->first()->id);
                $queryKeluar->where('lokasi_pengirim', $lokasis->first()->id);
                $querySaldo->where('lokasi_id', $lokasis->first()->id);
            }
        }
        if($req->rekening){
            $getRekening = Rekening::find($req->rekening);
            if($getRekening->jenis == 'Rekening'){
                $queryMasuk->where('rekening_penerima', $req->rekening);
                $queryKeluar->where('rekening_pengirim', $req->rekening);
            } else {
                $queryMasuk->whereNull('rekening_penerima')->where('lokasi_penerima', $getRekening->lokasi_id);
                $queryKeluar->whereNull('rekening_pengirim')->where('lokasi_pengirim', $getRekening->lokasi_id);
            }
            $querySaldo->where('id', $req->rekening);
        }

        $saldoRekening = (clone $querySaldo)->where('jenis', 'Rekening')->sum('saldo_akhir');
        $saldoCash = (clone $querySaldo)->where('jenis', 'Cash')->sum('saldo_akhir');
        $saldo = $saldoCash + $saldoRekening;
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

        return view('kas_gallery.index', compact(
            'lokasis', 'rekenings', 
            'saldoMasuk', 'saldoKeluar', 'saldoMasukRekening', 
            'saldoKeluarRekening', 'saldoMasukCash', 'saldoKeluarCash', 
            'saldo', 'saldoRekening', 'saldoCash', 'lokasi_pengirim', 'rekeningKeluar', 'akuns'
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
            'akun_id' => 'required|exists:akuns,id',
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

        // Cek saldo dan validasi rekening/cash di awal
        $totalKeluar = $req->nominal + ($req->biaya_lain ?? 0);

        if ($req->metode == 'Cash') {
            $dataPengirim = Rekening::where('jenis', 'Cash')->where('lokasi_id', $req->lokasi_pengirim)->first();
            if (!$dataPengirim) {
                return redirect()->back()->withInput()->with('fail', 'Data cash belum ada');
            }

            if ($dataPengirim->saldo_akhir < $totalKeluar) {
                return redirect()->back()->withInput()->with('fail', 'Saldo tidak mencukupi');
            }
        } else {
            // Untuk metode Transfer
            $dataPengirim = Rekening::find($req->rekening_pengirim);
            if (!$dataPengirim) {
                return redirect()->back()->withInput()->with('fail', 'Data rekening pengirim belum ada');
            }

            if ($dataPengirim->saldo_akhir < $totalKeluar) {
                return redirect()->back()->withInput()->with('fail', 'Saldo tidak mencukupi');
            }
        }

        $data = $req->except(['_token', '_method']);

        // Simpan file
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = 'kas_gallery' . date('YmdHis') . '.jpg';
            $filePath = 'bukti_transaksi_kas/' . $fileName;
        
            // Check file size (500KB = 500 * 1024 bytes)
            if ($file->getSize() > 500 * 1024) {
                Image::make($file)
                    ->encode('jpg', 70)
                    ->save(storage_path('app/public/' . $filePath));
            } else {
                Image::make($file)
                    ->encode('jpg')
                    ->save(storage_path('app/public/' . $filePath));
            }
        
            // Check if the file was saved successfully
            if (!File::exists(storage_path('app/public/' . $filePath))) {
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        
            // Save the file path
            $data['file'] = $filePath;
        }

        DB::beginTransaction();
        try {
            $data['status'] = 'DIKONFIRMASI';

            // Simpan transaksi kas
            $transaksi = TransaksiKas::create($data);
            if (!$transaksi) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }

            // Update saldo
            if ($req->metode == 'Cash') {
                $dataPengirim->subtractSaldo($totalKeluar);
            } else {
                $dataPengirim->subtractSaldo($totalKeluar);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data tersimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        // Validasi input
        $validator = Validator::make($req->all(), [
            'akun_id' => 'required|exists:akuns,id',
            'lokasi_pengirim' => 'required|numeric|exists:lokasis,id',
            'metode' => 'required|in:Transfer,Cash',
            'rekening_pengirim' => 'required_if:metode,Transfer|numeric|exists:rekenings,id',
            'jenis' => 'required|in:Lainnya,Pemindahan Saldo',
            'keterangan' => 'required',
            'nominal' => 'required|numeric',
            'tanggal' => 'required|date',
            'file' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        // Fetch existing record
        $existingTransaksi = TransaksiKas::find($transaksiKas);
        if (!$existingTransaksi) {
            return redirect()->back()->with('fail', 'Transaksi tidak ditemukan.');
        }

        // Cek saldo dan validasi rekening/cash di awal
        $totalKeluar = $req->nominal + ($req->biaya_lain ?? 0);

        if ($req->metode == 'Cash') {
            $dataPengirim = Rekening::where('jenis', 'Cash')->where('lokasi_id', $req->lokasi_pengirim)->first();
            if (!$dataPengirim) {
                return redirect()->back()->withInput()->with('fail', 'Data cash belum ada');
            }

            if ($dataPengirim->saldo_akhir + $existingTransaksi->nominal < $totalKeluar) {
                return redirect()->back()->withInput()->with('fail', 'Saldo tidak mencukupi');
            }
        } else {
            // Untuk metode Transfer
            $dataPengirim = Rekening::find($req->rekening_pengirim);
            if (!$dataPengirim) {
                return redirect()->back()->withInput()->with('fail', 'Data rekening pengirim belum ada');
            }

            if ($dataPengirim->saldo_akhir + $existingTransaksi->nominal < $totalKeluar) {
                return redirect()->back()->withInput()->with('fail', 'Saldo tidak mencukupi');
            }
        }

        // Simpan file baru jika ada
        $data = $req->except(['_token', '_method']);
        $data['file'] = $existingTransaksi->file;

        if ($req->hasFile('file')) {
            // Simpan file baru
            $file = $req->file('file');
            $fileName = 'kas_gallery' . date('YmdHis') . '.jpg';
            $filePath = 'bukti_transaksi_kas/' . $fileName;

            // Check file size and optimize
            if ($file->getSize() > 500 * 1024) {
                Image::make($file)
                    ->encode('jpg', 70)
                    ->save(storage_path('app/public/' . $filePath));
            } else {
                Image::make($file)
                    ->encode('jpg')
                    ->save(storage_path('app/public/' . $filePath));
            }

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
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        }

        // Proses update dalam transaksi
        DB::beginTransaction();
        try {
            // Update saldo
            if ($req->metode == 'Cash') {
                $dataPengirim->addSaldo($existingTransaksi->nominal); // Revert the previous transaction amount
                $dataPengirim->subtractSaldo($totalKeluar); // Subtract new total
            } else {
                $dataPengirim->addSaldo($existingTransaksi->nominal); // Revert the previous transaction amount
                $dataPengirim->subtractSaldo($totalKeluar); // Subtract new total
            }

            // Update transaksi kas
            $check = $existingTransaksi->update($data);
            if (!$check) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Data diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        $rekenings = Rekening::where('jenis', 'Rekening')->where('lokasi_id', $req->lokasi_id)->get();
        return response()->json($rekenings);
    }

    public function log(Request $req, $id)
    {
        $queryLogs = Activity::with('causer', 'subject')->where('subject_type', TransaksiKas::class)->where('subject_id', $id)->orderBy('id', 'desc');
        $start = $req->input('start');
        $length = $req->input('length');
        $order = $req->input('order')[0]['column'];
        $dir = $req->input('order')[0]['dir'];
        $columnName = $req->input('columns')[$order]['data'];

        $queryLogs->orderBy($columnName, $dir);
        $recordsFiltered = $queryLogs->count();
        $tempData = $queryLogs->offset($start)->limit($length)->get();

        $currentPage = ($start / $length) + 1;
        $perPage = $length;
    
        $data = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
            $item->no = ($currentPage - 1) * $perPage + ($index + 1);
            $changes = $item->changes();
    
            if (isset($changes['old'])) {
                $diff = array_keys(array_diff_assoc($changes['attributes'], $changes['old']));
                $descriptionLog = [];
                foreach ($diff as $key => $value) {
                    $descriptionLog[] = [
                        'field' => $value,
                        'old' => $changes['old'][$value],
                        'new' => $changes['attributes'][$value]
                    ];
                }
            } else {
                $descriptionLog = [['message' => 'Data Kas Terbuat']];
            }
            
            $item->description_log = $descriptionLog;
            $item->pengubah = $item->causer->name;
            return $item;
        });

        // search
        $search = $req->input('search.value');
        if (!empty($search)) {
            $data = $data->filter(function($item) use ($search) {
                $descriptionLogMatches = collect($item->description_log)->filter(function($log) use ($search) {
                    // Pastikan key yang diakses ada
                    $field = $log['field'] ?? '';
                    $old = $log['old'] ?? '';
                    $new = $log['new'] ?? '';
                    $message = $log['message'] ?? '';

                    return stripos($field, $search) !== false
                        || stripos($old, $search) !== false
                        || stripos($message, $search) !== false
                        || stripos($new, $search) !== false;
                })->isNotEmpty();

                return $descriptionLogMatches
                    || stripos($item->no, $search) !== false
                    || stripos($item->pengubah, $search) !== false
                    || stripos($item->created_at, $search) !== false;
            });
        }

        return response()->json([
            'draw' => $req->input('draw'),
            'recordsTotal' => Activity::count(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }
}
