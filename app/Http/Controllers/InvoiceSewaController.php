<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSewa;
use App\Models\Karyawan;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kontrak;
use App\Models\Ongkir;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InvoiceSewaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = InvoiceSewa::all();
        return view('invoice_sewa.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kontrak' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);

        $kontrak = Kontrak::with('produk')->find($req->kontrak);
        $sales = Karyawan::where('jabatan', 'sales')->get();
        $ongkirs = Ongkir::all();
        $rekening = Rekening::all();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();

        $latestInvSewa = InvoiceSewa::withTrashed()->orderByDesc('id')->first();

        if (!$latestInvSewa) {
            $getKode = 'INS' . date('Ymd') . '00001';
        } else {
            $lastInvDate = substr($latestInvSewa->no_invoice, 3, 8);
            $todayDate = date('Ymd');
            if ($lastInvDate != $todayDate) {
                $getKode = 'INS' . date('Ymd') . '00001';
            } else {
                $lastInvNumber = substr($latestInvSewa->no_invoice, -5);
                $nextInvNumber = str_pad((int)$lastInvNumber + 1, 5, '0', STR_PAD_LEFT);
                $getKode = 'INS' . date('Ymd') . $nextInvNumber;
            }
        }
        return view('invoice_sewa.create', compact('getKode', 'kontrak', 'sales', 'produkSewa', 'ongkirs', 'rekening'));
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
            'no_invoice' => 'required',
            'no_sewa' => 'required',
            'tanggal_invoice' => 'required',
            'jatuh_tempo' => 'required',
            'rekening_id' => 'required',
            'total_tagihan' => 'required',
            'sisa_bayar' => 'required',
            'sales' => 'required',
            'rekening_id' => 'required',
            'tanggal_sales' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        $data['lokasi_id'] = 1;
        $data['pembuat'] = Auth::user()->id;
        $data['tanggal_pembuat'] = now();
        $data['status'] = 'DRAFT';

        // save data invoice
        $check = InvoiceSewa::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        
        // update data produk invoice
        $kontrak = Kontrak::where('no_kontrak', $data['no_sewa'])->first();
        // $produkKontrak = $kontrak->produk()->get();
        // $produkKontrak->transform(function ($item) use ($check) {
        //     $item->no_invoice = $check->no_invoice;
        //     return $item;
        // });

        for ($i=0; $i < count($data['nama_produk']); $i++) { 
            $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->id,
                'no_invoice' => $check->no_invoice,
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
     * @param  \App\Models\InvoiceSewa  $invoiceSewa
     * @return \Illuminate\Http\Response
     */
    public function show($invoiceSewa)
    {
        $data = InvoiceSewa::find($invoiceSewa);
        $kontrak = Kontrak::with('produk')->where('no_kontrak', $data->no_sewa)->first();
        $sales = Karyawan::where('jabatan', 'sales')->get();
        $ongkirs = Ongkir::all();
        $rekening = Rekening::all();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        return view('invoice_sewa.show', compact('data', 'kontrak', 'sales', 'ongkirs', 'rekening', 'produkSewa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InvoiceSewa  $invoiceSewa
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoiceSewa $invoiceSewa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InvoiceSewa  $invoiceSewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InvoiceSewa $invoiceSewa)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InvoiceSewa  $invoiceSewa
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoiceSewa $invoiceSewa)
    {
        //
    }
}
