<?php

namespace App\Http\Controllers;

use App\Models\Komponen_Produk_Jual;
use App\Models\Kondisi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Tipe_Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use App\Models\Customer;
use App\Models\Lokasi;
use App\Models\Karyawan;
use App\Models\Rekening;
use App\Models\Promo;
use App\Models\Ongkir;
use App\Models\Penjualan;
use App\Models\InventoryGallery;
use App\Models\InventoryGreenHouse;
use App\Models\InventoryOutlet;
use App\Models\Pembayaran;
use App\Models\User;
use Carbon\Carbon;
use App\Models\FormPerangkai;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PergantianExport;
use PDF;

class PenjualanController extends Controller
{

    public function index(Request $req)
    {
        $user = Auth::user();
        // dd($user);
        $lokasi = Karyawan::where('user_id', $user->id)->first();
        $user = Auth::user()->roles()->value('name');
        // dd($user);
        if($lokasi->lokasi->tipe_lokasi == 2){
            $query = Penjualan::with('karyawan')->where('lokasi_id', $lokasi->lokasi_id)->where('no_invoice', 'LIKE', 'IPO%');
        }elseif($lokasi->lokasi->tipe_lokasi == 1){
            $query = Penjualan::with('karyawan')->where('lokasi_id', $lokasi->lokasi_id)->where('no_invoice', 'LIKE', 'INV%');
        }else{
            $query = Penjualan::with('karyawan')->whereNotNull('no_invoice');
        }
            
        $payments = Pembayaran::with('penjualan')->get();
        $sales = Karyawan::all();
        $customers = Customer::all();

        $latestPayments = [];

        foreach ($payments as $payment) {
            $penjualanId = $payment->invoice_penjualan_id;

            if (!isset($latestPayments[$penjualanId])) {
                $latestPayments[$penjualanId] = $payment;
            } else {
                if ($payment->id > $latestPayments[$penjualanId]->id) {
                    $latestPayments[$penjualanId] = $payment;
                }
            }
        }
        
        if($req->customer) {
            $query->where('id_customer', $req->input('customer'));
        }
        if ($req->sales) {
            $query->where('employee_id', $req->input('sales'));
        }
        if ($req->metode) {
            $query->where('cara_bayar', $req->input('metode'));
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }
        $penjualans = $query->orderByDesc('id')->get();

        // dd($latestPayments);

        return view('penjualan.index', compact('customers','sales','penjualans', 'latestPayments'));
    }

    public function create()
    {
        // $lokasi = Lokasi::where('tipe_lokasi', 2)->get();
        // $lokasiIds = $lokasi->pluck('id')->toArray();
        $user = Auth::user();
        // $ceklokasi = Karyawan::where('user_id', $user)->first();
        $lokasi = Karyawan::where('user_id', $user->id)->get();
        // dd($lokasi->lokasi_id->tipe_lokasi);
        $customers = Customer::where('lokasi_id', $lokasi[0]->lokasi_id)->get();
        // dd($customers);
        $lokasis = Lokasi::where('id', $lokasi[0]->lokasi_id)->get();
        $ongkirs = Ongkir::where('id', $lokasi[0]->lokasi_id)->get();
        $karyawans = Karyawan::where('lokasi_id', $lokasi[0]->lokasi_id)->where('jabatan', 'Sales')->get();
        $promos = Promo::where('lokasi_id', $lokasi[0]->lokasi_id)->orWhere('lokasi_id', 'Semua')->get();
        // dd($promos);
        $produks = Produk_Jual::with('komponen.kondisi')->get();
        $komponenproduks = Komponen_Produk_Jual::all();
        $produkkompos = Produk_Jual::with('komponen.kondisi')
                        ->where(function($query) {
                            $query->where('kode', 'like', 'TRD%')
                                ->orWhere('kode', 'like', 'POT%');
                        })->get();

        // dd($produks);
        $bankpens = Rekening::where('lokasi_id', $lokasi[0]->lokasi_id)->get();
        if($lokasi[0]->lokasi->tipe_lokasi == 2){
            $Invoice = Penjualan::where('no_invoice', 'LIKE', 'IPO%')->latest()->first();
        }elseif($lokasi[0]->lokasi->tipe_lokasi == 1){
            $Invoice = Penjualan::where('no_invoice', 'LIKE', 'INV%')->latest()->first();
        }
        // dd($bankpens);
        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 0;
        }
        $InvoiceBayar = Pembayaran::latest()->first();
        // dd($Invoice);
        if ($InvoiceBayar != null) {
            $substringBayar = substr($InvoiceBayar->no_invoice_bayar, 11);
            $cekInvoiceBayar = substr($substringBayar, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoiceBayar = 0;
        }
            // $komponen = Kondisi::with('komponen')->get();
            // dd($komponen);
        $kondisis = Kondisi::all();
        $invoices = Penjualan::get();
        foreach($lokasis as $lokasi){
            $ceklokasi = $lokasi->tipe_lokasi;
        }
        // }

        return view('penjualan.create', compact('ceklokasi','produkkompos', 'komponenproduks','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
    }


    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'id_customer' => 'required',
            'point_dipakai' => 'required',
            'lokasi_id' => 'required',
            'distribusi' => 'required',
            'no_invoice' => 'required',
            'tanggal_invoice' => 'required',
            'jatuh_tempo' => 'required',
            'employee_id' => 'required',
            'status' => 'required',
            'bukti_file' => 'required|image|mimes:jpeg,png|max:2048',
            'notes' => 'required',
            'cara_bayar' => 'required',
            'pilih_pengiriman' => 'required',
            'biaya_ongkir' => 'required',
            'sub_total' => 'required',
            'jenis_ppn' => 'required',
            'jumlah_ppn' => 'required',
            'dp' => 'required',
            'total_tagihan' => 'required',
            'sisa_bayar' => 'required'
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->with('fail',$validator)->withInput();
        }
        $data = $req->except(['_token', '_method', 'bukti_file', 'bukti', 'status_bayar']);
        // dd($data);
        // dd($req->distribusi);
        $data['dibuat_id'] = Auth::user()->id;
        $data['tanggal_dibuat'] = now();
        // dd($data);
        if ($req->hasFile('bukti_file')) {
            $file = $req->file('bukti_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_invoice_penjualan', $fileName, 'public');
            // dd($filePath);
            $data['bukti_file'] = $filePath;
        }
        // dd($req->cara_bayar);
        if($req->cara_bayar == 'cash')
        {
            $data['jumlahCash'] = $req->nominal;
        }
        
        //buat penjualan
        $penjualan = Penjualan::create($data);

        $lokasi = Lokasi::where('id', $req->lokasi_id)->first();

        if($lokasi->tipe_lokasi == 1){
            //buat produk penjualan dan komponen
            if ($penjualan) {
                for ($i = 0; $i < count($data['nama_produk']); $i++) {
                    $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                    // dd($getProdukJual);
                    $produkTerjualData = [
                        'produk_jual_id' => $getProdukJual->id,
                        'no_invoice' => $penjualan->no_invoice,
                        'harga' => $data['harga_satuan'][$i],
                        'jumlah' => $data['jumlah'][$i],
                        'jenis_diskon' => $data['jenis_diskon'][$i],
                        'diskon' => $data['diskon'][$i],
                        'harga_jual' => $data['harga_total'][$i]
                    ];
                    
                    if ($req->distribusi == 'Dikirim') {
                        $produkTerjualData['jumlah_dikirim'] = $data['jumlah'][$i];
                    }

                    $produk_terjual = Produk_Terjual::create($produkTerjualData);

                    if($getProdukJual->tipe_produk == 6){
                        // dd($produk_terjual);
                        $newProdukTerjual[] = $produk_terjual;
                    }

                    if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
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
                        if (!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    }
                }

                if ($req->hasFile('bukti')) {
                    $file = $req->file('bukti');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
                    $data['bukti'] = $filePath;
                }

                // dd($cek);
                if ($req->dp > 0) {
                    if ($req->sisa_bayar == 0) {
                        $data['invoice_penjualan_id'] = $penjualan->id;
                        $data['tanggal_bayar'] = $req->tanggal_invoice;
                        $data['status_bayar'] = 'LUNAS';
                        $pembayaran = Pembayaran::create($data);
                        return redirect()->back()->with('success', 'Tagihan sudah Lunas');
                    } else {
                        $data['invoice_penjualan_id'] = $penjualan->id;
                        $data['tanggal_bayar'] = $req->tanggal_invoice;
                        $data['status_bayar'] = 'BELUM LUNAS';
                        $pembayaran = Pembayaran::create($data);
                    }
                } else {
                    return redirect(route('penjualan.index'))->with('success', 'Data Berhasil Disimpan');
                }

                if(!empty($newProdukTerjual)){
                    return redirect(route('penjualan.show', ['penjualan' => $penjualan->id]))->with('success', 'Silakan set komponen gift');
                }
                return redirect(route('penjualan.index'))->with('success', 'Data Berhasil Disimpan');
            } else {
                return redirect()->back()->with('error', 'Gagal menyimpan data');
            }
        }elseif($lokasi->tipe_lokasi == 2){
            //buat produk penjualan dan komponen
            if ($penjualan) {
                for ($i = 0; $i < count($data['nama_produk']); $i++) {
                    $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                    // dd($getProdukJual);
                    $produkTerjualData = [
                        'produk_jual_id' => $getProdukJual->id,
                        'no_invoice' => $penjualan->no_invoice,
                        'harga' => $data['harga_satuan'][$i],
                        'jumlah' => $data['jumlah'][$i],
                        'jenis_diskon' => $data['jenis_diskon'][$i],
                        'diskon' => $data['diskon'][$i],
                        'harga_jual' => $data['harga_total'][$i]
                    ];
                    
                    if ($req->distribusi == 'Dikirim') {
                        $produkTerjualData['jumlah_dikirim'] = $data['jumlah'][$i];
                    }

                    $produk_terjual = Produk_Terjual::create($produkTerjualData);
                    // dd($getProdukJual);
                    if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
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
                        if (!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    }

                    if($req->distribusi == 'Diambil'){
                        $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)
                                    ->where('kode_produk', $produk_terjual->produk->kode)
                                    ->first();
                        // dd($stok);
                    
                        if (!$stok) {
                            return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                        }

                        $stok->jumlah = intval($stok->jumlah) - intval($produk_terjual->jumlah);
                        $stok->save();

                    }
                }

                if ($req->hasFile('bukti')) {
                    $file = $req->file('bukti');
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
                    $data['bukti'] = $filePath;
                }

                // dd($cek);
                if ($req->dp > 0) {
                    if ($req->sisa_bayar == 0) {
                        $data['invoice_penjualan_id'] = $penjualan->id;
                        $data['tanggal_bayar'] = $req->tanggal_invoice;
                        $data['status_bayar'] = 'LUNAS';
                        $pembayaran = Pembayaran::create($data);
                        return redirect()->back()->with('success', 'Tagihan sudah Lunas');
                    } else {
                        $data['invoice_penjualan_id'] = $penjualan->id;
                        $data['tanggal_bayar'] = $req->tanggal_invoice;
                        $data['status_bayar'] = 'BELUM LUNAS';
                        $pembayaran = Pembayaran::create($data);
                    }
                } else {
                    return redirect(route('penjualan.index'))->with('success', 'Data Berhasil Disimpan');
                }
                return redirect(route('penjualan.index'))->with('success', 'Data Berhasil Disimpan');
            } else {
                return redirect()->back()->with('error', 'Gagal menyimpan data');
            }
        }
        
    }

    public function destroy($penjualan)
    {
        $data = Penjualan::find($penjualan);
        // dd($data);
        if (!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $getProduks = Produk_Terjual::where('no_invoice', $data->no_invoice)->get();
        // dd($getProduks);
        $check = $data->delete();
        if (!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        if ($getProduks) {
            $getProduks->each->delete();
        }
        foreach ($getProduks as $item) {
            $getKomponenProduks = Komponen_Produk_Terjual::where('produk_terjual_id', $item->id)->get();
            if ($getKomponenProduks) {
                $getKomponenProduks->each->delete();
            }
        }
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function payment(Request $req, $penjualan)
    {
        $produkjuals = Produk_Jual::all();
        // dd($produkjuals);
        $penjualans = Penjualan::find($penjualan);
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        $karyawans = Karyawan::where('id', $penjualans->employee_id)->get();
        $promos = Promo::where('id', $penjualans->promo_id)->get();
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $Invoice = Pembayaran::latest()->first();
        // dd($Invoice);
        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 000;
        }
        $pembayarans = Pembayaran::with('rekening')->where('invoice_penjualan_id', $penjualan)->orderBy('created_at', 'desc')->get();
        // dd($produks);
        // dd($promos);
        // $getProdukJual = Produk_Jual::find($penjualan);
        // $getKomponen = Komponen_Produk_Jual::where('produk_jual_id', $getProdukJual->id)->get();
        $roles = Auth::user()->roles()->value('name');
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->value('lokasi_id');
        $lokasis = Lokasi::where('id', $lokasi)->get();
        $rekenings = Rekening::get();
        $ongkirs = Ongkir::get();
        $bankpens = Rekening::get();
        $Invoice = Penjualan::latest()->first();
        $kondisis = Kondisi::all();
        $invoices = Penjualan::get();
        $pegawais = Karyawan::all();

        $riwayat = Activity::where('subject_type', Penjualan::class)->where('subject_id', $penjualan)->orderBy('id', 'desc')->get();
        foreach($lokasis as $lokasi){
            $ceklokasi = $lokasi->tipe_lokasi;
        }

        // return view('penjualan.create', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis','invoices'));
        return view('penjualan.payment', compact('ceklokasi','pegawais','riwayat','customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'kondisis', 'invoices', 'penjualans', 'produkjuals', 'perangkai', 'cekInvoice', 'pembayarans'));
    }

    public function show(Request $req, $penjualan)
    {
        $produkjuals = Produk_Jual::all();
        // dd($produkjuals);
        $penjualans = Penjualan::with('dibuat')->where('id', $penjualan )->find($penjualan);
        // dd($penjualans);
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        $karyawans = Karyawan::where('id', $penjualans->employee_id)->get();
        $pegawais = Karyawan::all(); 
        $promos = Promo::where('id', $penjualans->promo_id)->get();
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        // dd($produks);
        // dd($promos);
        // $getProdukJual = Produk_Jual::find($penjualan);
        // $getKomponen = Komponen_Produk_Jual::where('produk_jual_id', $getProdukJual->id)->get();
        $roles = Auth::user()->roles()->value('name');
        $user = Auth::user()->value('id');
        $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
        $lokasis = Lokasi::where('id', $lokasi)->get();
        $rekenings = Rekening::get();
        $ongkirs = Ongkir::get();
        $bankpens = Rekening::get();
        $Invoice = Penjualan::latest()->first();
        $kondisis = Kondisi::all();
        $invoices = Penjualan::get();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();
        //log activity
        $riwayatPenjualan = Activity::where('subject_type', Penjualan::class)->where('subject_id', $penjualan)->orderBy('id', 'desc')->get();
        $riwayatProdukTerjual = Activity::where('subject_type', Produk_Terjual::class)->where('subject_id', $produks[0]->id)->orderBy('id', 'desc')->get();
        $riwayatPerangkai = Activity::where('subject_type', FormPerangkai::class)->orderBy('id', 'desc')->get();
        $komponenIds = $produks[0]->komponen->pluck('id')->toArray();
        $riwayatKomponen = Activity::where('subject_type', Komponen_Produk_Terjual::class)->orderBy('id', 'desc')->get();
        $produkIds = $produks->pluck('id')->toArray();
        $filteredRiwayat = $riwayatKomponen->filter(function (Activity $activity) use ($produkIds) {
            $properties = json_decode($activity->properties, true);
            return isset($properties['attributes']['produk_terjual_id']) && in_array($properties['attributes']['produk_terjual_id'], $produkIds);
        });
        
        $latestCreatedAt = $filteredRiwayat->max('created_at');
        
        $latestRiwayat = $filteredRiwayat->filter(function (Activity $activity) use ($latestCreatedAt) {
            return $activity->created_at == $latestCreatedAt;
        });

        $formIds = $produks->pluck('no_form')->toArray();
        $filteredFormRiwayat = $riwayatPerangkai->filter(function (Activity $activity) use ($formIds) {
            $properties = json_decode($activity->properties, true);
            return isset($properties['attributes']['no_form']) && in_array($properties['attributes']['no_form'], $formIds);
        });
        
        $latestFormCreatedAt = $filteredFormRiwayat->max('created_at');
        
        $latestFormRiwayat = $filteredFormRiwayat->filter(function (Activity $activity) use ($latestFormCreatedAt) {
            return $activity->created_at == $latestFormCreatedAt;
        });
        
        // dd($latestFormRiwayat);
        
        $mergedriwayat = [
            'penjualan' => $riwayatPenjualan,
            'komponen_produk_terjual' => $latestRiwayat,
            'form_perangkai' => $latestFormRiwayat
        ];
        
        $riwayat = collect($mergedriwayat)
            ->flatMap(function ($riwayatItem, $jenis) {
                return $riwayatItem->map(function ($item) use ($jenis) {
                    $item->jenis = $jenis;
                    return $item;
                });
            })
            ->sortByDesc('id')
            ->values()
            ->all();
        
        // dd($riwayat);
        // return view('penjualan.create', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis','invoices'));
        return view('penjualan.show', compact('pegawais','riwayat','produkKomponens','customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'kondisis', 'invoices', 'penjualans', 'produkjuals', 'perangkai'));
    }

    public function store_komponen(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'komponen_id' => 'required',
            'kondisi_id' => 'required',
            'jumlahproduk' => 'required',
            'prdTerjual_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = $req->except(['_token', '_method', 'route', 'produk_id', 'perangkai_id', 'prdTerjual_id']);
        $exsist = Komponen_Produk_Terjual::where('produk_terjual_id', $req->prdTerjual_id)->get();
        // dd($req->prdTerjual_id);
        $jumlahItem = count($req->komponen_id);

        if ($exsist) {
            $exsist->each->forceDelete();
        }
        // Create new komponen produk terjual and decrement stock
        for ($i = 0; $i < $jumlahItem; $i++) {
            $data['produk_terjual_id'] = $req->prdTerjual_id;
            $data['kondisi'] = $req->kondisi_id[$i];
            $data['jumlah'] = $req->jumlahproduk[$i];

            $produk = Produk::findOrFail($req->komponen_id[$i]);

            $data['kode_produk'] = $produk->kode;
            $data['nama_produk'] = $produk->nama;
            $data['tipe_produk'] = $produk->tipe_produk;
            $data['deskripsi'] = $produk->deskripsi;
            $data['harga_satuan'] = 0;
            $data['harga_total'] = 0;

            $check = Komponen_Produk_Terjual::create($data);

            if (!$check) {
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
        }

        return redirect()->back()->with('success', 'Data tersimpan');
    }

    public function store_komponen_mutasi(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'komponen_id' => 'required',
            'kondisi_id' => 'required',
            'jumlahproduk' => 'required',
            'prdTerjual_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = $req->except(['_token', '_method', 'route', 'produk_id', 'perangkai_id', 'prdTerjual_id']);
        $lokasi = Lokasi::where('id', $req->pengirim)->first();
        $allStockAvailable = true;

        $jumlahItem = count($req->komponen_id);
        
        // Check stock availability and decrement stock
        for ($i = 0; $i < $jumlahItem; $i++) {
            $produk = Produk::findOrFail($req->komponen_id[$i]);
            $stok = null;

            if ($lokasi->tipe_lokasi == 1) {
                $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                                        ->where('kode_produk', $produk->kode)
                                        ->where('kondisi_id', $req->kondisi_id[$i])
                                        ->first();
            } elseif ($lokasi->tipe_lokasi == 3) {
                $stok = InventoryGreenHouse::where('lokasi_id', $req->pengirim)
                                        ->where('kode_produk', $produk->kode)
                                        ->where('kondisi_id', $req->kondisi_id[$i])
                                        ->first();
            }

            if (!$stok || $stok->jumlah < intval($req->jumlahproduk[$i]) * intval($req->jml_produk) || $stok->jumlah < $stok->min_stok) {
                $allStockAvailable = false;
                break;
            }
        }

        if (!$allStockAvailable) {
            return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory atau stok tidak mencukupi');
        }

        // Handle existing komponen produk terjual
        $exsist = Komponen_Produk_Terjual::where('produk_terjual_id', $req->prdTerjual_id)->get();
        $jumlah = Produk_Terjual::where('id', $req->prdTerjual_id)->value('jumlah');
        // dd($req->jml_produk);

        foreach ($exsist as $item) {
            $produk = Produk::where('kode', $item->kode_produk)->first();
            $stok = null;

            if ($lokasi->tipe_lokasi == 1) {
                $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                                        ->where('kode_produk', $produk->kode)
                                        ->where('kondisi_id', $item->kondisi)
                                        ->first();
            } elseif ($lokasi->tipe_lokasi == 3) {
                $stok = InventoryGreenHouse::where('lokasi_id', $req->pengirim)
                                        ->where('kode_produk', $produk->kode)
                                        ->where('kondisi_id', $item->kondisi)
                                        ->first();
            }

            if ($stok) {
                $stok->jumlah += intval($item->jumlah) * intval($jumlah);
                $stok->update();
            }
        }
        // dd($stok);
        if ($exsist) {
            $exsist->each->forceDelete();
        }

        // Create new komponen produk terjual and decrement stock
        for ($i = 0; $i < $jumlahItem; $i++) {
            $data['produk_terjual_id'] = $req->prdTerjual_id;
            $data['kondisi'] = $req->kondisi_id[$i];
            $data['jumlah'] = $req->jumlahproduk[$i];

            $produk = Produk::findOrFail($req->komponen_id[$i]);

            $data['kode_produk'] = $produk->kode;
            $data['nama_produk'] = $produk->nama;
            $data['tipe_produk'] = $produk->tipe_produk;
            $data['deskripsi'] = $produk->deskripsi;
            $data['harga_satuan'] = 0;
            $data['harga_total'] = 0;

            $check = Komponen_Produk_Terjual::create($data);

            if (!$check) {
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }

            $stok = null;
            if ($lokasi->tipe_lokasi == 1) {
                $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                                        ->where('kode_produk', $produk->kode)
                                        ->where('kondisi_id', $req->kondisi_id[$i])
                                        ->first();
            } elseif ($lokasi->tipe_lokasi == 3) {
                $stok = InventoryGreenHouse::where('lokasi_id', $req->pengirim)
                                        ->where('kode_produk', $produk->kode)
                                        ->where('kondisi_id', $req->kondisi_id[$i])
                                        ->first();
            }

            if ($stok) {
                $stok->jumlah -= intval($req->jumlahproduk[$i]) * intval($req->jml_produk);
                $stok->update();
            }
        }

        return redirect()->back()->with('success', 'Data tersimpan');
    }

    public function store_komponen_retur(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'komponen_id' => 'required',
            'kondisi_id' => 'required',
            'jumlahproduk' => 'required',
            'prdTerjual_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $data = $req->except(['_token', '_method', 'route', 'produk_id', 'perangkai_id', 'prdTerjual_id']);
        $lokasi = Lokasi::where('id', $req->pengirim)->first();
        if($lokasi->tipe_lokasi == 1){
            $allStockAvailable = true;

            $jumlahItem = count($req->komponen_id);
            
            // Check stock availability and decrement stock
            for ($i = 0; $i < $jumlahItem; $i++) {
                $produk = Produk::findOrFail($req->komponen_id[$i]);
                $stok = null;

                if ($lokasi->tipe_lokasi == 1) {
                    $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                                            ->where('kode_produk', $produk->kode)
                                            ->where('kondisi_id', $req->kondisi_id[$i])
                                            ->first();
                }

                if (!$stok || $stok->jumlah < intval($req->jumlahproduk[$i]) * intval($req->jml_produk) || $stok->jumlah < $stok->min_stok) {
                    $allStockAvailable = false;
                    break;
                }
            }

            if (!$allStockAvailable) {
                return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory atau stok tidak mencukupi');
            }

            // Handle existing komponen produk terjual
            $exsist = Komponen_Produk_Terjual::where('produk_terjual_id', $req->prdTerjual_id)->get();
            $jumlah = Produk_Terjual::where('id', $req->prdTerjual_id)->value('jumlah');
            // dd($req->jml_produk);

            foreach ($exsist as $item) {
                $produk = Produk::where('kode', $item->kode_produk)->first();
                $stok = null;

                if ($lokasi->tipe_lokasi == 1) {
                    $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                                            ->where('kode_produk', $produk->kode)
                                            ->where('kondisi_id', $item->kondisi)
                                            ->first();
                }

                if ($stok) {
                    $stok->jumlah += intval($item->jumlah) * intval($jumlah);
                    $stok->update();
                }
            }
            // dd($stok);
            if ($exsist) {
                $exsist->each->forceDelete();
            }

            // Create new komponen produk terjual and decrement stock
            for ($i = 0; $i < $jumlahItem; $i++) {
                $data['produk_terjual_id'] = $req->prdTerjual_id;
                $data['kondisi'] = $req->kondisi_id[$i];
                $data['jumlah'] = $req->jumlahproduk[$i];

                $produk = Produk::findOrFail($req->komponen_id[$i]);

                $data['kode_produk'] = $produk->kode;
                $data['nama_produk'] = $produk->nama;
                $data['tipe_produk'] = $produk->tipe_produk;
                $data['deskripsi'] = $produk->deskripsi;
                $data['harga_satuan'] = 0;
                $data['harga_total'] = 0;

                $check = Komponen_Produk_Terjual::create($data);

                if (!$check) {
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }

                $stok = null;
                if ($lokasi->tipe_lokasi == 1) {
                    $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                                            ->where('kode_produk', $produk->kode)
                                            ->where('kondisi_id', $req->kondisi_id[$i])
                                            ->first();
                }

                if ($stok) {
                    $stok->jumlah -= intval($req->jumlahproduk[$i]) * intval($req->jml_produk);
                    $stok->update();
                }
            }

            return redirect()->back()->with('success', 'Data tersimpan');
        } else{
            return redirect()->back()->with('fail', 'retur penjualan hanya untuk galery');
        }
    }

    public function pdfinvoicepenjualan($penjualan)
    {
        $data = Penjualan::find($penjualan)->toArray();
        $data['lokasi'] = Lokasi::where('id', $data['lokasi_id'])->value('nama');
        $customer = Customer::where('id', $data['id_customer'])->first();
        $data['customer'] = $customer->nama;
        $data['no_handphone'] = $customer->handphone;
        $data['sales'] = Karyawan::where('id', $data['employee_id'])->value('nama');
        $data['produks'] = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $data['no_invoice'])->get();
        $data['dibuat'] = User::where('id', $data['dibuat_id'])->value('name');
        $data['dibukukan'] =User::where('id', $data['dibukukan_id'])->value('name');
        $data['auditor'] = User::where('id', $data['auditor_id'])->value('name');
        // dd($data);
        $pdf = PDF::loadView('penjualan.view', $data);
    
        return $pdf->stream($data['no_invoice'] . '_INVOICE PENJUALAN.pdf');
    }

    public function excelPergantian($id)
    {
        return Excel::download(new PergantianExport, 'users.xlsx');
    }
}
