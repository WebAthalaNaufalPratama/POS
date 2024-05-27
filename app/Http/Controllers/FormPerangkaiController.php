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
use Illuminate\Support\Facades\Validator;

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
        ->orderBy('karyawans.nama')
        ->get();

        $query = FormPerangkai::whereHas('produk_terjual');
        if($req->jenis_rangkaian){
            $query->where('jenis_rangkaian', $req->jenis_rangkaian);
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
        $data = $query->orderByDesc('id')->get();
        return view('form_sewa.index', compact('data', 'perangkai'));
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
            'perangkai_id' => 'required',
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
        // dd($req->prdTerjual_i);
        // delete data
        $getPerangkai = FormPerangkai::where('no_form', $data['no_form'])->get();
        if($getPerangkai){
            $getPerangkai->each->forceDelete();
        }
        
        $updateProdukTerjual = Produk_Terjual::with('komponen')->find($req->prdTerjual_id);
        // dd($updateProdukTerjual);
        $lokasi = Lokasi::where('id', $req->lokasi_id)->first();
        // dd($lokasi);
        if($lokasi->tipe_lokasi == 1 && $req->distribusi == 'Diambil')
        {
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

            foreach ($updateProdukTerjual->komponen as $komponen ) {
                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)
                                         ->where('kode_produk', $komponen->kode_produk)
                                         ->where('kondisi_id', $komponen->kondisi)
                                         ->first();
                $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($req->jml_produk));
                $stok->update();
            }
        }elseif($lokasi->tipe_lokasi == 2 && $req->distribusi == 'Diambil')
        {
            $allStockAvailable = true;

            foreach ($updateProdukTerjual->komponen as $komponen ) {
                $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)
                                        ->where('kode_produk', $komponen->kode_produk)
                                        ->where('kondisi_id', $komponen->kondisi)
                                        ->first();
                if (!$stok) {
                    $allStockAvailable = false;
                    break;
                }
            }

            if (!$allStockAvailable) {
                return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
            }

            foreach ($updateProdukTerjual->komponen as $komponen ) {
                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)
                                         ->where('kode_produk', $komponen->kode_produk)
                                         ->where('kondisi_id', $komponen->kondisi)
                                         ->first();
                $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($req->jml_produk));
                $stok->update();
            }
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
        // $query = FormPerangkai::whereHas('produk_terjual');
        // if($req->jenis_rangkaian){
        //     $data = $query->where('jenis_rangkaian', $req->jenis_rangkaian)->orderBy('created_at', 'desc')->get();
        //     // dd($query->get());
        // } else {
        //     $data = $query->get();
        // }
        $perangkai = FormPerangkai::select('perangkai_id')
        ->distinct()
        ->join('karyawans', 'form_perangkais.perangkai_id', '=', 'karyawans.id')
        ->orderBy('karyawans.nama')
        ->get();
        $query = FormPerangkai::whereHas('produk_terjual');
        if($req->jenis_rangkaian){
            $query->where('jenis_rangkaian', $req->jenis_rangkaian);
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
        $data = $query->get();
        return view('form_jual.index', compact('data', 'perangkai'));
    }

    public function penjualan_show($formpenjualan)
    {
        $data = FormPerangkai::with('produk_terjual')->find($formpenjualan);
        // dd($data);
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        return view('form_jual.show', compact('perangkai', 'data'));
    }
}
