<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSewa;
use App\Models\Karyawan;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kontrak;
use App\Models\Ongkir;
use App\Models\Pembayaran;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use PDF;

class InvoiceSewaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $query = InvoiceSewa::query();
        if(Auth::user()->roles()->value('name') != 'admin'){
            $query->whereHas('kontrak', function($q) {
                $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
            });
        }
        if ($req->customer) {
            $query->whereHas('sewa',function($q) use($req){
                $q->where('customer_id', $req->input('customer'));
            });
        }
        if ($req->dateStart) {
            $query->where('jatuh_tempo', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('jatuh_tempo', '<=', $req->input('dateEnd'));
        }
        $data = $query->orderByDesc('id')->get();
        $customer = Kontrak::whereHas('invoice')->select('customer_id')
        ->distinct()
        ->join('customers', 'kontraks.customer_id', '=', 'customers.id')
        ->when(Auth::user()->roles()->value('name') != 'admin', function ($query) {
            return $query->where('customers.lokasi_id', Auth::user()->karyawans->lokasi_id);
        })
        ->orderBy('customers.nama')
        ->get();
        $Invoice = Pembayaran::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $invoice_bayar = substr($substring, 0, 3);
        } else {
            $invoice_bayar = 0;
        }
        $bankpens = Rekening::get();
        return view('invoice_sewa.index', compact('data', 'invoice_bayar', 'bankpens', 'customer'));
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
        $sisaBayar = $kontrak->total_harga;
        foreach ($kontrak->invoice as $invoice) {
            $sisaBayar -= $invoice->dp;
            foreach ($invoice->pembayaran as $pembayaran) {
                $sisaBayar -= $pembayaran->nominal;
            }
        }
        return view('invoice_sewa.create', compact('getKode', 'kontrak', 'sales', 'produkSewa', 'ongkirs', 'rekening', 'sisaBayar'));
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
        $data['lokasi_id'] = Auth::user()->karyawans ? Auth::user()->karyawans->lokasi_id : 1;
        $data['pembuat'] = Auth::user()->id;
        $data['tanggal_pembuat'] = now();
        $data['status'] = 'DRAFT';

        // save data invoice
        $check = InvoiceSewa::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        
        // update data produk invoice
        $kontrak = Kontrak::where('no_kontrak', $data['no_sewa'])->first();

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
        $data = InvoiceSewa::with('pembayaran')->find($invoiceSewa);
        $kontrak = Kontrak::with('produk')->where('no_kontrak', $data->no_sewa)->first();
        $sales = Karyawan::where('jabatan', 'sales')->get();
        $ongkirs = Ongkir::all();
        $rekening = Rekening::all();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $riwayat = Activity::where('subject_type', InvoiceSewa::class)->where('subject_id', $invoiceSewa)->orderBy('id', 'desc')->get();
        $pembayaran = $data->pembayaran()->orderByDesc('id')->get();
        $bankpens = Rekening::get();
        $Invoice = Pembayaran::latest()->first();
        if (!$Invoice) {
            $invoice_bayar = 'BYR' . date('Ymd') . '00001';
        } else {
            $lastDate = substr($Invoice->no_invoice_bayar, 3, 8);
            $todayDate = date('Ymd');
            if ($lastDate != $todayDate) {
                $invoice_bayar = 'BYR' . date('Ymd') . '00001';
            } else {
                $lastNumber = substr($Invoice->no_invoice_bayar, -5);
                $nextNumber = str_pad((int)$lastNumber + 1, 5, '0', STR_PAD_LEFT);
                $invoice_bayar = 'BYR' . date('Ymd') . $nextNumber;
            }
        }
        return view('invoice_sewa.show', compact('data', 'kontrak', 'sales', 'ongkirs', 'rekening', 'produkSewa', 'riwayat', 'pembayaran', 'bankpens', 'invoice_bayar'));
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

    public function cetak($id)
    {
        $data = InvoiceSewa::with('kontrak', 'produk', 'produk.produk', 'data_sales', 'data_pembuat', 'data_pemeriksa', 'data_penyetuju', 'kontrak.lokasi', 'kontrak.customer')->find($id)->toArray();
        // dd($data);
        $pdf = PDF::loadView('invoice_sewa.invoicepdf', $data);

        return $pdf->stream('Invoice.pdf');
    }
}
