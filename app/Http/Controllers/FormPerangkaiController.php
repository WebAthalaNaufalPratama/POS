<?php

namespace App\Http\Controllers;

use App\Models\FormPerangkai;
use App\Models\Karyawan;
use App\Models\Produk_Terjual;
use App\Models\Lokasi;
use App\Models\InventoryGallery;
use App\Models\InventoryOutlet;
use App\Models\Komponen_Produk_Terjual;
use Illuminate\Http\Request;
use App\Models\Penjualan;
use App\Models\Mutasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use PDF;

class FormPerangkaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $perangkai = FormPerangkai::select('perangkai_id')
        ->distinct()
        ->join('karyawans', 'form_perangkais.perangkai_id', '=', 'karyawans.id')
        ->when(Auth::user()->karyawans, function ($query) {
            return $query->where('karyawans.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('karyawans.nama')
        ->get();

        $query = FormPerangkai::where('jenis_rangkaian', 'Sewa')->whereHas('produk_terjual');
        if(Auth::user()->karyawans){
            $query->whereHas('perangkai', function($q) {
                $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
            });
        }
        if ($req->perangkai) {
            $query->where('perangkai_id', $req->input('perangkai'));
        }
        if ($req->dateStart) {
            $query->where('tanggal', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal', '<=', $req->input('dateEnd'));
        }

        if ($req->ajax()) {
            $start = $req->input('start');
            $length = $req->input('length');
            $order = $req->input('order')[0]['column'];
            $dir = $req->input('order')[0]['dir'];
            $columnName = $req->input('columns')[$order]['data'];

            $query->orderBy($columnName, $dir);
            $recordsFiltered = $query->count();
            $tempData = $query->offset($start)->limit($length)->get();
    
            $currentPage = ($start / $length) + 1;
            $perPage = $length;
        
            $data = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
                $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                $item->tanggal_format = $item->tanggal == null ? null : tanggalindo($item->tanggal);
                $item->no_kontrak = $item->produk_terjual->no_sewa;
                $item->nama_produk = $item->produk_terjual->produk->nama;
                $item->nama_perangkai = $item->perangkai->nama;
                $item->userRole = Auth::user()->getRoleNames()->first();
                return $item;
            });

            // search
            $search = $req->input('search.value');
            if (!empty($search)) {
                $data = $data->filter(function($item) use ($search) {
                    return stripos($item->no_form, $search) !== false
                        || stripos($item->tanggal_format, $search) !== false
                        || stripos($item->nominal_format, $search) !== false
                        || stripos($item->no_kontrak, $search) !== false
                        || stripos($item->nama_produk, $search) !== false
                        || stripos($item->nama_perangkai, $search) !== false;
                });
            }

            return response()->json([
                'draw' => $req->input('draw'),
                'recordsTotal' => FormPerangkai::where('jenis_rangkaian', 'Sewa')->count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);

        }
        return view('form_sewa.index', compact('perangkai'));
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
            'no_form' => 'required',
            'jenis_rangkaian' => 'required',
            'tanggal' => 'required',
            'perangkai_id.*' => 'required',
            'produk_id' => 'required',
            'prdTerjual_id' => 'required'
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'route', 'produk_id', 'perangkai_id', 'prdTerjual_id']);

        // delete data
        $getPerangkai = FormPerangkai::where('no_form', $data['no_form'])->get();
        if($getPerangkai){
            $getPerangkai->each->forceDelete();
        }
        
        // save data
        foreach ($req->perangkai_id as $item) {
            $data['perangkai_id'] = $item;
            $check = FormPerangkai::create($data);
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        }
        $updateProdukTerjual = Produk_Terjual::find($req->prdTerjual_id);
        if($updateProdukTerjual) {
            $updateProdukTerjual->no_form = $req->no_form;
            $updateProdukTerjual->update();
        }
        if($req->route){
            $route = explode(',', $req->route);
            if(count($route) == 1){
                return redirect()->route($route[0])->with('success', 'Form Perangkai ditambahkan');
            } elseif($route[1] == 'form') {
                return redirect()->route($route[0], [$route[1] => $check->id])->with('success', 'Form Perangkai ditambahkan');
            } else {
                return redirect()->route($route[0], [$route[1] => $route[2]])->with('success', 'Form Perangkai ditambahkan');
            }
        }
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    public function cetak($id)
    {
        $data = FormPerangkai::with('perangkai', 'produk_terjual', 'produk_terjual.sewa', 'produk_terjual.produk', 'produk_terjual.sewa.data_sales', 'produk_terjual.sewa.lokasi', 'produk_terjual.komponen')->find($id)->toArray();
        $pdf = PDF::loadView('form_sewa.pdf', $data);

        return $pdf->stream('Form-Perangkai.pdf');
    }

    public function cetak_penjualan($id)
    {
        $data = FormPerangkai::with('perangkai', 'produk_terjual', 'produk_terjual.penjualan', 'produk_terjual.produk', 'produk_terjual.penjualan.karyawan', 'produk_terjual.penjualan.lokasi', 'produk_terjual.komponen')->find($id)->toArray();
        $pdf = PDF::loadView('penjualan.formpdf', $data);

        return $pdf->stream('Form-Perangkai.pdf');
    }

    public function cetak_mutasigalery($id)
    {
        $data = FormPerangkai::with('perangkai', 'produk_terjual', 'produk_terjual.mutasi', 'produk_terjual.produk', 'produk_terjual.mutasi.dibuat', 'produk_terjual.mutasi.pengirim', 'produk_terjual.komponen')->find($id)->toArray();
        $pdf = PDF::loadView('mutasigalery.formpdf', $data);

        return $pdf->stream('Form-Perangkai.pdf');
    }

    public function mutasi_store(Request $req){
        // validasi
        $validator = Validator::make($req->all(), [
            'no_form' => 'required',
            'jenis_rangkaian' => 'required',
            'tanggal' => 'required',
            'perangkai_id' => 'required',
            'produk_id' => 'required',
            'prdTerjual_id' => 'required'
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'route', 'produk_id', 'perangkai_id', 'prdTerjual_id']);
        // dd($req->prdTerjual_i);
        // delete data
        $getPerangkai = FormPerangkai::where('no_form', $data['no_form'])->get();
        if($getPerangkai){
            $getPerangkai->each->forceDelete();
        }

        //cek produk
        $updateProdukTerjual = Produk_Terjual::with('komponen')->find($req->prdTerjual_id);

        // dd($updateProdukTerjual);
        $lokasi = Lokasi::where('id', $req->lokasi_id)->first();
        // dd($lokasi);
        $allStockAvailable = true;

        foreach ($updateProdukTerjual->komponen as $komponen ) {
            $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)
                                    ->where('kode_produk', $komponen->kode_produk)
                                    ->where('kondisi_id', $komponen->kondisi)
                                    ->first();
            if (!$stok) {
                $allStockAvailable = false;
                break;
            }
        }

        if (!$allStockAvailable) {
            return redirect(route('inven_galeri.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
        }

        if($updateProdukTerjual->no_form != null){
            //update penambahan jumlah
            foreach ($updateProdukTerjual->komponen as $komponen ) {
                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)
                                            ->where('kode_produk', $komponen->kode_produk)
                                            ->where('kondisi_id', $komponen->kondisi)
                                            ->first();
                $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($req->jml_produk));
                $stok->update();
            }
        }

        //update pengurangan jumlah
        foreach ($updateProdukTerjual->komponen as $komponen ) {
            $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)
                                        ->where('kode_produk', $komponen->kode_produk)
                                        ->where('kondisi_id', $komponen->kondisi)
                                        ->first();
            $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($req->jml_produk));
            $stok->update();
        }
        // save data
        foreach ($req->perangkai_id as $item) {
            $data['perangkai_id'] = $item;
            $check = FormPerangkai::create($data);
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        }

        if($updateProdukTerjual) {
            $updateProdukTerjual->no_form = $req->no_form;
            $updateProdukTerjual->update();
        }
        
        return redirect()->back()->with('success', 'Data Perangkai tersimpan');
    }

    public function penjualan_store(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'no_form' => 'required',
            'jenis_rangkaian' => 'required',
            'tanggal' => 'required',
            'perangkai_id' => 'required',
            'produk_id' => 'required',
            'prdTerjual_id' => 'required'
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'route', 'produk_id', 'perangkai_id', 'prdTerjual_id']);

        $updateProdukTerjual = Produk_Terjual::with('komponen')->find($req->prdTerjual_id);
        $lokasi = Lokasi::where('id', $req->lokasi_id)->first();
        
        //pengurangan inventory gallery dan outlet dari admin
        if($req->distribusi == 'Diambil' && $req->status == 'DIKONFIRMASI' && $req->jenis_rangkaian == 'Penjualan' || $req->jenis_rangkaian == 'Retur Penjualan' || $req->jenis_rangkaian == 'MUTASIGO')
        {
            foreach($updateProdukTerjual->komponen as $komponen) 
            {
                $allStockAvailable = true;
                $cekstokproduk = true;
        
                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)
                                            ->where('kode_produk', $komponen->kode_produk)
                                            ->where('kondisi_id', $komponen->kondisi)
                                            ->first();
                if (!$stok) {
                    $allStockAvailable = false;
                    break;
                }
                
                $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($req->jml_produk));
                if ($stok->jumlah < 0) {
                    $cekstokproduk = false;
                    break;
                }
                $stok->update();
            }
            if (!$allStockAvailable) {
                return redirect(route('inven_galeri.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
            }
            if (!$cekstokproduk) {
                return redirect(route('inven_galeri.create'))->with('fail', 'Jumlah Produk Kurang Dari Stok');
            }
        }

        // delete data
        $getPerangkai = FormPerangkai::where('no_form', $data['no_form'])->get();
        if($getPerangkai){
            $getPerangkai->each->forceDelete();
        }

        // // dd($updateProdukTerjual);
        // save data
        foreach ($req->perangkai_id as $item) {
            $data['perangkai_id'] = $item;
            $check = FormPerangkai::create($data);
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        }

        if($updateProdukTerjual) {
            $updateProdukTerjual->no_form = $req->no_form;
            $updateProdukTerjual->update();
        }
        
        if($req->route){
            $route = explode(',', $req->route);
            if(count($route) == 1){
                return redirect()->route($route[0])->with('success', 'Form Perangkai ditambahkan');
            } elseif($route[1] == 'form') {
                return redirect()->route($route[0], [$route[1] => $check->id])->with('success', 'Form Perangkai ditambahkan');
            } else if($req->jenis_rangkaian == 'Retur Penjualan') {
                return redirect()->back()->with('success','Produk Ganti dan Perangkai Telah Tersimpan');
            }elseif($req->jenis_rangkaian == 'MUTASIGO'){
                return redirect()->back()->with('success', 'Berhasil Menyimpan Perangkai dan Gift');
            }else {
                return redirect()->route($route[0], [$route[1] => $route[2]])->with('success', 'Form Perangkai ditambahkan');
            }
        }
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\FormPerangkai  $formPerangkai
     * @return \Illuminate\Http\Response
     */
    public function show($formPerangkai)
    {
        $data = FormPerangkai::with('produk_terjual')->find($formPerangkai);
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        return view('form_sewa.show', compact('perangkai', 'data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\FormPerangkai  $formPerangkai
     * @return \Illuminate\Http\Response
     */
    public function edit(FormPerangkai $formPerangkai)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\FormPerangkai  $formPerangkai
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, FormPerangkai $formPerangkai)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\FormPerangkai  $formPerangkai
     * @return \Illuminate\Http\Response
     */
    public function destroy(FormPerangkai $formPerangkai)
    {
        //
    }

    public function penjualan_index(Request $req)
    {
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->first();
        $query = FormPerangkai::with('perangkai');

        // Handle the different types of 'jenis_rangkaian'
        if ($req->jenis_rangkaian) {
            if ($req->jenis_rangkaian == 'Penjualan') {
                if ($lokasi->lokasi->tipe_lokasi == 1) {
                    $penjualan = Penjualan::where('lokasi_id', $lokasi->lokasi_id)
                                        ->pluck('no_invoice')
                                        ->filter() 
                                        ->toArray();
                    $pj = Produk_Terjual::whereIn('no_invoice', $penjualan)
                                        ->pluck('no_form')
                                        ->filter() 
                                        ->toArray();
                    $query->whereIn('no_form', $pj);
                }
                $query->where('jenis_rangkaian', 'Penjualan');
            } elseif ($req->jenis_rangkaian == 'MUTASIGO') {
                $penjualan = Mutasi::where('pengirim', $lokasi->lokasi_id)
                                ->pluck('no_mutasi')
                                ->filter() 
                                ->toArray();
                $pj = Produk_Terjual::whereNotNull('no_form')
                                    ->whereIn('no_mutasigo', $penjualan)
                                    ->pluck('no_form')
                                    ->filter() 
                                    ->toArray();
                $query->whereIn('no_form', $pj);
                $query->where('jenis_rangkaian', 'MUTASIGO');
            }
        }


        $query->when($req->perangkai, function ($query, $perangkai) {
            $query->where('perangkai_id', $perangkai);
        });
        $query->when($req->dateStart, function ($query, $dateStart) {
            $query->where('tanggal', '>=', $dateStart);
        });
        $query->when($req->dateEnd, function ($query, $dateEnd) {
            $query->where('tanggal', '<=', $dateEnd);
        });


        if ($req->ajax()) {
            $orderColumn = $req->input('columns.' . $req->input('order.0.column') . '.data');
            $orderDirection = $req->input('order.0.dir');

            $totalRecords = $query->count();
            $data = $query->orderByDesc('id')
                        ->skip($req->input('start'))
                        ->take($req->input('length'))
                        ->get();

            return response()->json([
                'draw' => intval($req->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        }

        $perangkai = Karyawan::select('id', 'nama')->where('jabatan', 'Perangkai')->orderBy('nama')->get();

        return view('form_jual.index', compact('perangkai'));
    }








    public function penjualan_show($formpenjualan)
    {
        $data = FormPerangkai::with('produk_terjual')->find($formpenjualan);
        // dd($data);
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        return view('form_jual.show', compact('perangkai', 'data'));
    }
}
