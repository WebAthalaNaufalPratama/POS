<?php

namespace App\Http\Controllers;

use App\Exports\PergantianExport;
use App\Models\Customer;
use App\Models\Karyawan;
use App\Models\KembaliSewa;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kondisi;
use App\Models\Kontrak;
use App\Models\Lokasi;
use App\Models\Ongkir;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use App\Models\Promo;
use App\Models\Rekening;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Activitylog\Models\Activity;

class KontrakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $query = Kontrak::with('customer', 'invoice');
        if(Auth::user()->hasRole('AdminGallery')){
            $query->where('lokasi_id',Auth::user()->karyawans->lokasi_id);
        }
        if ($req->customer) {
            $query->where('customer_id', $req->input('customer'));
        }
        if ($req->sales) {
            $query->where('sales', $req->input('sales'));
        }
        if ($req->dateStart) {
            $query->where('tanggal_kontrak', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kontrak', '<=', $req->input('dateEnd'));
        }
        if(Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Auditor')){
            $query->where('status', 'DIKONFIRMASI');
        }
        $kontraks = $query->orderByDesc('id')->get();
        $kontraks->map(function($kontrak){
            $kontrak->hasKembali = KembaliSewa::where('no_sewa', $kontrak->no_kontrak)->where('status', 'DIKONFIRMASI')->exists();
        });

        $customer = Kontrak::select('customer_id')
        ->distinct()
        ->join('customers', 'kontraks.customer_id', '=', 'customers.id')
        ->when(Auth::user()->hasRole('AdminGallery'), function ($query) {
            return $query->where('customers.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('customers.nama')
        ->get();
        $sales = Kontrak::select('sales')
        ->distinct()
        ->join('karyawans', 'kontraks.sales', '=', 'karyawans.id')
        ->when(Auth::user()->hasRole('AdminGallery'), function ($query) {
            return $query->where('karyawans.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('karyawans.nama')
        ->get();
        return view('kontrak.index', compact('kontraks', 'customer', 'sales'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user()->hasRole('AdminGallery')) {
            $produkjuals = Produk_Jual::all();
            $lokasis = Lokasi::find(Auth::user()->karyawans->lokasi_id);
            $customers = Customer::where('tipe', 'sewa')->where('lokasi_id', Auth::user()->karyawans->lokasi_id)->get();
            $rekenings = Rekening::where('lokasi_id', Auth::user()->karyawans->lokasi_id)->get();
            $promos = Promo::where('lokasi_id', Auth::user()->karyawans->lokasi_id)->get();
            $sales = Karyawan::where('lokasi_id', Auth::user()->karyawans->lokasi_id)->where('jabatan', 'sales')->get();
            $ongkirs = Ongkir::where('lokasi_id', Auth::user()->karyawans->lokasi_id)->get();
        } else {
            $produkjuals = Produk_Jual::all();
            $lokasis = Lokasi::all();
            $customers = Customer::where('tipe', 'sewa')->get();
            $rekenings = Rekening::all();
            $promos = Promo::all();
            $sales = Karyawan::where('jabatan', 'sales')->get();
            $ongkirs = Ongkir::all();
        }

        $latestKontrak = Kontrak::withTrashed()->orderByDesc('id')->first();
        if (!$latestKontrak) {
            $getKode = 'KSW' . date('Ymd') . '00001';
        } else {
            $lastDate = substr($latestKontrak->no_kontrak, 3, 8);
            $todayDate = date('Ymd');
            if ($lastDate != $todayDate) {
                $getKode = 'KSW' . date('Ymd') . '00001';
            } else {
                $lastNumber = substr($latestKontrak->no_kontrak, -5);
                $nextNumber = str_pad((int)$lastNumber + 1, 5, '0', STR_PAD_LEFT);
                $getKode = 'KSW' . date('Ymd') . $nextNumber;
            }
        }

        return view('kontrak.create', compact('produkjuals', 'lokasis', 'customers', 'rekenings', 'promos', 'sales', 'getKode', 'ongkirs'));
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
            'no_kontrak' => 'required',
            'masa_sewa' => 'required|integer',
            'tanggal_kontrak' => 'required|date',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'pic' => 'required',
            'handphone' => 'required|numeric',
            'alamat' => 'required',
            'no_npwp' => 'required|',
            'nama_npwp' => 'required',
            'ppn_nominal' => 'required|integer',
            'pph_nominal' => 'required|integer',
            'subtotal' => 'required|integer',
            'total_harga' => 'required|integer',
            'status' => 'required',
            'sales' => 'required|exists:karyawans,id',
            'rekening_id' => 'required|exists:rekenings,id',
            'tanggal_sales' => 'required|date',
            'ongkir_nominal' => 'required|integer',
            'promo_persen' => 'required|integer',
            'total_promo' => 'required|integer',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['lokasi_id'] = Auth::user()->karyawans->lokasi_id ?? '';
        $data['pembuat'] = Auth::user()->id;
        $data['tanggal_pembuat'] = now();
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = $req->no_kontrak . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_kontrak', $fileName, 'public');
            $data['file'] = $filePath;
        }

        // save data kontrak
        $check = Kontrak::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        $newProdukTerjual = [];
        
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

            if($getProdukJual->tipe_produk == 6){
                $newProdukTerjual[] = $produk_terjual;
            }

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

        if(!empty($newProdukTerjual)){
            return redirect(route('kontrak.show', ['kontrak' => $check->id]))->with('success', 'Silakan set komponen gift');
        }
        return redirect(route('kontrak.index'))->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Kontrak  $kontrak
     * @return \Illuminate\Http\Response
     */
    public function show($kontrak)
    {
        $kontraks = Kontrak::find($kontrak);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_sewa', $kontraks->no_kontrak)->get();
        $riwayat = Activity::where('subject_type', Kontrak::class)->where('subject_id', $kontrak)->orderBy('id', 'desc')->get();
        $bungapot = Produk::where('tipe_produk',1)->orWhere('tipe_produk',2)->get();
        $kondisi = Kondisi::all();
        $produkjuals = Produk_Jual::all();
        $lokasis = Lokasi::find($kontraks->lokasi_id);
        $customers = Customer::where('tipe', 'sewa')->where('lokasi_id', $kontraks->lokasi_id)->get();
        $rekenings = Rekening::where('lokasi_id', $kontraks->lokasi_id)->get();
        $promos = Promo::where('lokasi_id', $kontraks->lokasi_id)->get();
        $sales = Karyawan::where('lokasi_id', $kontraks->lokasi_id)->where('jabatan', 'sales')->get();
        $ongkirs = Ongkir::where('lokasi_id', $kontraks->lokasi_id)->get();
        $perangkai = Karyawan::where('lokasi_id', $kontraks->lokasi_id)->where('jabatan', 'Perangkai')->get();
        
        return view('kontrak.show', compact('kontraks', 'produks', 'produkjuals', 'lokasis', 'customers', 'rekenings', 'promos', 'sales', 'ongkirs', 'riwayat', 'perangkai', 'bungapot', 'kondisi'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Kontrak  $kontrak
     * @return \Illuminate\Http\Response
     */
    public function edit($kontrak)
    {
        $kontraks = Kontrak::find($kontrak);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_sewa', $kontraks->no_kontrak)->get();
        $riwayat = Activity::where('subject_type', Kontrak::class)->where('subject_id', $kontrak)->orderBy('id', 'desc')->get();
        $produkjuals = Produk_Jual::all();
        $lokasis = Lokasi::find($kontraks->lokasi_id);
        $customers = Customer::where('tipe', 'sewa')->where('lokasi_id', $kontraks->lokasi_id)->get();
        $rekenings = Rekening::where('lokasi_id', $kontraks->lokasi_id)->get();
        $promos = Promo::where('lokasi_id', $kontraks->lokasi_id)->get();
        $sales = Karyawan::where('lokasi_id', $kontraks->lokasi_id)->where('jabatan', 'sales')->get();
        $ongkirs = Ongkir::where('lokasi_id', $kontraks->lokasi_id)->get();
        $perangkai = Karyawan::where('lokasi_id', $kontraks->lokasi_id)->where('jabatan', 'Perangkai')->get();
        return view('kontrak.edit', compact('kontraks', 'produks', 'produkjuals', 'lokasis', 'customers', 'rekenings', 'promos', 'sales', 'ongkirs', 'riwayat'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Kontrak  $kontrak
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $kontrak)
    {
        if($req->konfirmasi){
            $kontrak = Kontrak::where('no_kontrak', $req->no_kontrak)->first();
            if($req->konfirmasi == 'confirm'){
                $kontrak->status = 'DIKONFIRMASI';
                $msg = 'Dikonfirmasi';
            } else if($req->konfirmasi == 'cancel'){
                $kontrak->status = 'BATAl';
                $msg = 'Dibatalkan';
            } else {
                return redirect()->back()->withInput()->with('fail', 'Status tidak sesuai');
            }
            if(Auth::user()->hasRole('Auditor')){
                $kontrak->penyetuju = Auth::user()->id;
                $kontrak->tanggal_penyetuju = $req->tanggal_penyetuju;
            }
            if(Auth::user()->hasRole('Finance')){
                $kontrak->pemeriksa = Auth::user()->id;
                $kontrak->tanggal_pemeriksa = $req->tanggal_pemeriksa;
            }
            $check = $kontrak->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal mengubah status');
            return redirect()->back()->withInput()->with('success', 'Data Berhasil ' . $msg);
        } else {
            // validasi
            $validator = Validator::make($req->all(), [
                'no_kontrak' => 'required',
                'masa_sewa' => 'required|integer',
                'tanggal_kontrak' => 'required|date',
                'tanggal_mulai' => 'required|date',
                'tanggal_selesai' => 'required|date',
                'customer_id' => 'required|exists:customers,id',
                'pic' => 'required',
                'handphone' => 'required|numeric',
                'alamat' => 'required',
                'no_npwp' => 'required|',
                'nama_npwp' => 'required',
                'ppn_nominal' => 'required|integer',
                'pph_nominal' => 'required|integer',
                'subtotal' => 'required|integer',
                'total_harga' => 'required|integer',
                'status' => 'required',
                'sales' => 'required|exists:karyawans,id',
                'rekening_id' => 'required|exists:rekenings,id',
                'tanggal_sales' => 'required|date',
                'ongkir_nominal' => 'required|integer',
                'promo_persen' => 'required|integer',
                'total_promo' => 'required|integer',
            ]);
            $error = $validator->errors()->all();
            if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
            $data = $req->except(['_token', '_method', 'log']);
            
            $dataKontrak = Kontrak::find($kontrak);
            $data['lokasi_id'] = $dataKontrak->lokasi_id;
            $data['pembuat'] = $dataKontrak->pembuat;
            $data['tanggal_pembuat'] = $dataKontrak->tanggal_pembuat;
            if ($req->hasFile('file')) {
                $file = $req->file('file');
                $fileName = $req->no_kontrak . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('bukti_kontrak', $fileName, 'public');
                $data['file'] = $filePath;
            }

            // save data kontrak
            $check = Kontrak::find($kontrak)->update($data);
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            
            $dataProduk = Produk_Terjual::where('no_sewa', $dataKontrak->no_kontrak)->get();
            // delete data
            foreach ($dataProduk as $item) {
                $komponen = Komponen_Produk_Terjual::where('produk_terjual_id', $item->id)->forceDelete();
                $produkTerjual = Produk_terjual::find($item->id)->forceDelete();
            }

            // create new data
            for ($i=0; $i < count($data['nama_produk']); $i++) { 
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_sewa' => $dataKontrak->no_kontrak,
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
        if(!$data) return response()->json(['msg' => 'Kontrak tidak ditemukan']);
        $data->status = 'BATAL';
        $check = $data->update();
        if(!$check) return response()->json(['msg' => 'Gagal membatalkan kontrak']);
        return response()->json(['msg' => 'Berhasil membatalkan kontrak']);
    }

    public function create_gift(Request $req)
    {
        $gift = Kontrak::with('produk')->find($req->kontrak);
        $bungapot = Produk::where('tipe_produk',1)->orWhere('tipe_produk',2)->get();
        $kondisi = Kondisi::all();
        return view('kontrak.create_gift', compact('gift', 'bungapot', 'kondisi'));
    }
    public function datatable(Request $request)
    {
        $query = Kontrak::with('customer');

        if ($request->has('customer')) {
            $query->where('customer_id', $request->input('customer'));
        }
        $data = $query->paginate($request->input('length'));
    
        $formattedData = [];
        foreach ($data as $index => $kontrak) {
            $formattedData[] = [
                'loop_number' => $index + 1,
                'no_kontrak' => $kontrak->no_kontrak,
                'customer' => $kontrak->customer->nama,
                'pic' => $kontrak->pic,
                'handphone' => $kontrak->handphone,
                'masa_sewa' => $kontrak->masa_sewa . ' bulan',
                'rentang_tanggal' => $kontrak->tanggal_mulai . ' - ' . $kontrak->tanggal_selesai,
                'total_biaya' => $kontrak->total_harga,
            ];
        }
    
        $response = [
            'draw' => $request->input('draw'),
            'recordsTotal' => $data->total(),
            'recordsFiltered' => $data->total(),
            'data' => $formattedData,
        ];
    
        return response()->json($response);
    }

    public function pdfKontrak($id)
    {
        $data = Kontrak::with('lokasi', 'lokasi.operasional', 'customer', 'rekening', 'data_sales', 'invoice', 'produk.produk')->find($id)->toArray();
        $pdf = PDF::loadView('kontrak.pdf', $data);

        return $pdf->stream('Kontrak.pdf');
    }

    public function excelPergantian($id)
    {
        return Excel::download(new PergantianExport($id), 'users.xlsx');
    }
}
