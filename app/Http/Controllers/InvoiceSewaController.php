<?php

namespace App\Http\Controllers;

use App\Models\InvoiceSewa;
use App\Models\Karyawan;
use App\Models\KembaliSewa;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kontrak;
use App\Models\Ongkir;
use App\Models\Pembayaran;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        if(Auth::user()->hasRole('AdminGallery')){
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
        if(Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Auditor')){
            $query->where('status', 'DIKONFIRMASI');
        }
        $data = $query->orderByDesc('id')->get();
        $customer = Kontrak::whereHas('invoice')->select('customer_id')
        ->distinct()
        ->join('customers', 'kontraks.customer_id', '=', 'customers.id')
        ->when(Auth::user()->hasRole('AdminGallery'), function ($query) {
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
        $data->map(function($kontrak){
            $kontrak->hasKembali = KembaliSewa::where('no_sewa', $kontrak->kontrak->no_kontrak)->where('status', 'DIKONFIRMASI')->exists();
        });
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
        $ongkirs = Ongkir::where('lokasi_id', $kontrak->lokasi_id)->get();
        $rekening = Rekening::where('lokasi_id', $kontrak->lokasi_id)->get();
        $produkjuals = Produk_Jual::all();
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
        return view('invoice_sewa.create', compact('getKode', 'kontrak', 'sales', 'produkSewa', 'ongkirs', 'rekening', 'produkjuals'));
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

        // Data preparation
        $data = $req->except(['_token', '_method']);
        $data['lokasi_id'] = Auth::user()->karyawans ? Auth::user()->karyawans->lokasi_id : 1;
        $data['pembuat'] = Auth::user()->id;
        $data['tanggal_pembuat'] = now();
        $data['status'] = 'TUNDA';
        $data['sisa_bayar'] = $data['total_tagihan'] - $data['dp'];

        // Start transaction
        DB::beginTransaction();

        try {
            // Save invoice data
            $check = InvoiceSewa::create($data);
            if (!$check) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }

            // Update product data in the invoice
            $kontrak = Kontrak::where('no_kontrak', $data['no_sewa'])->first();

            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_invoice' => $check->no_invoice,
                    'harga' => $data['harga_satuan'][$i],
                    'jumlah' => $data['jumlah'][$i],
                    'harga_jual' => $data['harga_total'][$i]
                ]);

                if (!$produk_terjual) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }

                foreach ($getProdukJual->komponen as $komponen) {
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

                    if (!$komponen_produk_terjual) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    }
                }
            }

            // Save additional product data
            if (isset($data['nama_produk2'][0])) {
                for ($i = 0; $i < count($data['nama_produk2']); $i++) {
                    $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                    $produk_terjual = Produk_Terjual::create([
                        'produk_jual_id' => $getProdukJual->id,
                        'no_invoice' => $check->no_invoice,
                        'harga' => $data['harga_satuan2'][$i],
                        'jumlah' => $data['jumlah2'][$i],
                        'jenis' => 'TAMBAHAN',
                        'harga_jual' => $data['harga_total2'][$i]
                    ]);

                    if (!$produk_terjual) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    }

                    foreach ($getProdukJual->komponen as $komponen) {
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

                        if (!$komponen_produk_terjual) {
                            DB::rollBack();
                            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                        }
                    }
                }
            }

            // Commit transaction
            DB::commit();
            return redirect(route('invoice_sewa.index'))->with('success', 'Data tersimpan');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
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
        $produkjuals = Produk_Jual::all();
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
        $firstInvoice = InvoiceSewa::where('no_sewa', $kontrak->no_kontrak)->first();
        $isFirst = $firstInvoice->id == $invoiceSewa;
        return view('invoice_sewa.show', compact('data', 'kontrak', 'sales', 'ongkirs', 'rekening', 'produkSewa', 'riwayat', 'pembayaran', 'bankpens', 'invoice_bayar', 'isFirst', 'produkjuals'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InvoiceSewa  $invoiceSewa
     * @return \Illuminate\Http\Response
     */
    public function edit($invoiceSewa)
    {
        $data = InvoiceSewa::with('pembayaran')->find($invoiceSewa);
        $kontrak = Kontrak::with('produk')->where('no_kontrak', $data->no_sewa)->first();
        $sales = Karyawan::where('jabatan', 'sales')->get();
        $ongkirs = Ongkir::all();
        $rekening = Rekening::all();
        $produkjuals = Produk_Jual::all();
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
        $firstInvoice = InvoiceSewa::where('no_sewa', $kontrak->no_kontrak)->first();
        $isFirst = $firstInvoice->id == $invoiceSewa;
        return view('invoice_sewa.edit', compact('data', 'kontrak', 'sales', 'ongkirs', 'rekening', 'produkSewa', 'riwayat', 'pembayaran', 'bankpens', 'invoice_bayar', 'isFirst', 'produkjuals'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InvoiceSewa  $invoiceSewa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $invoiceSewa)
    {
        if($req->konfirmasi){
            $invoice = InvoiceSewa::find($invoiceSewa);
            if($req->konfirmasi == 'confirm'){
                $invoice->status = 'DIKONFIRMASI';
                $msg = 'Dikonfirmasi';
            } else if($req->konfirmasi == 'cancel'){
                $invoice->status = 'BATAL';
                $msg = 'Dibatalkan';
            } else {
                return redirect()->back()->withInput()->with('fail', 'Status tidak sesuai');
            }
            if(Auth::user()->hasRole('Auditor')){
                $invoice->penyetuju = Auth::user()->id;
                $invoice->tanggal_penyetuju = $req->tanggal_penyetuju;
            }
            if(Auth::user()->hasRole('Finance')){
                $invoice->pemeriksa = Auth::user()->id;
                $invoice->tanggal_pemeriksa = $req->tanggal_pemeriksa;
            }
            $check = $invoice->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal mengubah status');
            return redirect()->back()->withInput()->with('success', 'Data Berhasil ' . $msg);
        } else {
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
                // 'tanggal_sales' => 'required',
            ]);
            $error = $validator->errors()->all();
            if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
            $data = $req->except(['_token', '_method']);

            // save data invoice
            $check = InvoiceSewa::find($invoiceSewa)->update($data);
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            // update data produk invoice
            $datainvoice = InvoiceSewa::find($invoiceSewa);
            $kontrak = Kontrak::where('no_kontrak', $data['no_sewa'])->first();

            $dataProduk = Produk_Terjual::where('no_invoice', $datainvoice->no_invoice)->get();
            // delete data
            foreach ($dataProduk as $item) {
                $komponen = Komponen_Produk_Terjual::where('produk_terjual_id', $item->id)->forceDelete();
                $produkTerjual = Produk_terjual::find($item->id)->forceDelete();
            }
            
            for ($i=0; $i < count($data['nama_produk']); $i++) { 
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_invoice' => $datainvoice->no_invoice,
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

            // Save additional product data
            if (isset($data['nama_produk2'][0])) {
                for ($i = 0; $i < count($data['nama_produk2']); $i++) {
                    $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk2'][$i])->first();
                    $produk_terjual = Produk_Terjual::create([
                        'produk_jual_id' => $getProdukJual->id,
                        'no_invoice' => $datainvoice->no_invoice,
                        'harga' => $data['harga_satuan2'][$i],
                        'jumlah' => $data['jumlah2'][$i],
                        'jenis' => 'TAMBAHAN',
                        'harga_jual' => $data['harga_total2'][$i]
                    ]);

                    if (!$produk_terjual) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    }

                    foreach ($getProdukJual->komponen as $komponen) {
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

                        if (!$komponen_produk_terjual) {
                            DB::rollBack();
                            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                        }
                    }
                }
            }
        }

        return redirect(route('invoice_sewa.index'))->with('success', 'Data tersimpan');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InvoiceSewa  $invoiceSewa
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoiceSewa)
    {
        $data = InvoiceSewa::find($invoiceSewa);
        if(!$data) return response()->json(['msg' => 'Invoice tidak ditemukan']);
        $data->status = 'BATAL';
        $check = $data->update();
        if(!$check) return response()->json(['msg' => 'Gagal membatalkan invoice']);
        return response()->json(['msg' => 'Berhasil membatalkan invoice']);
    }

    public function cetak($id)
    {
        $data = InvoiceSewa::with('kontrak', 'produk', 'produk.produk', 'data_sales', 'data_pembuat', 'data_pemeriksa', 'data_penyetuju', 'kontrak.lokasi', 'kontrak.customer', 'rekening')->find($id)->toArray();
        // dd($data);
        $pdf = PDF::loadView('invoice_sewa.invoicepdf', $data);

        return $pdf->stream('Invoice.pdf');
    }
}
