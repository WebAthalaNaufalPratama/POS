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
use App\Models\ReturPenjualan;
use Illuminate\Support\Facades\Storage;
use App\Models\FormPerangkai;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\DeliveryOrder;
use App\Exports\PergantianExport;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use PDF;

class PenjualanController extends Controller
{

    public function index(Request $req)
    {
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->first();

        $query = Penjualan::with('karyawan', 'lokasi', 'customer');

        if ($lokasi) {
            if ($lokasi->lokasi->tipe_lokasi == 2 && $user->hasRole(['KasirOutlet', 'KasirGallery', 'AdminGallery'])) {
                $query->where('lokasi_id', $lokasi->lokasi_id)->where('no_invoice', 'LIKE', 'IPO%');
            } elseif ($lokasi->lokasi->tipe_lokasi == 1 && $user->hasRole(['KasirOutlet', 'KasirGallery', 'AdminGallery'])) {
                $query->where('lokasi_id', $lokasi->lokasi_id)->where('no_invoice', 'LIKE', 'INV%');
            } elseif ($user->hasRole(['Finance', 'Auditor'])) {
                $query->where('lokasi_id', $lokasi->lokasi_id)->where('status', 'DIKONFIRMASI');
            } else {
                $query->whereNotNull('no_invoice');
            }
        }


        // Apply search filter
        if ($search = $req->input('search.value')) {
            $columns = ['penjualans.no_invoice', 'penjualans.tanggal_invoice', 'penjualans.jatuh_tempo', 'penjualans.total_tagihan', 'penjualans.sisa_bayar', 'penjualans.status'];
            $query->where(function($q) use ($search, $columns) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', "%{$search}%");
                }

                $q->orWhereHas('karyawan', function($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%");
                });
                $q->orWhereHas('customer', function($query) use ($search) {
                    $query->where('nama', 'like', "%{$search}%");
                });
            });
        }
        
        if ($order = $req->input('order.0.column')) {
            $columns = ['no_invoice', 'tanggal_invoice', 'jatuh_tempo', 'total_tagihan', 'sisa_bayar', 'status'];
            $query->orderBy($columns[$order], $req->input('order.0.dir'));
        }
        $query->when($req->customer, function ($query, $customer) {
            $query->where('id_customer', $customer);
        });
        $query->when($req->sales, function ($query, $sales) {
            $query->where('employee_id', $sales);
        });
        $query->when($req->metode, function ($query, $metode) {
            $query->where('cara_bayar', $metode);
        });
        $query->when($req->dateStart, function ($query, $dateStart) {
            $query->where('tanggal_invoice', '>=', $dateStart);
        });
        $query->when($req->dateEnd, function ($query, $dateEnd) {
            $query->where('tanggal_invoice', '<=', $dateEnd);
        });

        if ($req->ajax()) {
            $totalRecords = $query->count();
            $data = $query->orderByDesc('id')
                        ->skip($req->input('start'))
                        ->take($req->input('length'))
                        ->get();
        
            $returData = ReturPenjualan::whereIn('no_invoice', $data->pluck('no_invoice'))
                                                    ->where('status', 'DIKONFIRMASI')
                                                    ->get()
                                                    ->keyBy('no_invoice');

            $doData = DeliveryOrder::whereIn('no_referensi', $data->pluck('no_invoice'))
                                                    ->where('status', 'DIKONFIRMASI')
                                                    ->get()
                                                    ->keyBy('no_referensi');
                                                    
            $latestPayments = Pembayaran::with('penjualan')->get()->reduce(function ($carry, $payment) {
                if (!isset($carry[$payment->invoice_penjualan_id]) || $payment->id > $carry[$payment->invoice_penjualan_id]->id) {
                    $carry[$payment->invoice_penjualan_id] = $payment;
                }
                return $carry;
            }, []);
        
            $data->map(function ($penjualan) use ($latestPayments, $returData, $doData) {
                $penjualan->latest_payment = $latestPayments[$penjualan->id] ?? null;
                $allNoForm = false;
                $jmldikirim = false;
                if ($penjualan->produk && $penjualan->produk->isNotEmpty()) {
                    // Check if all produkMutasi have a non-null no_form
                    $allNoForm = $penjualan->produk->every(function($produk) {
                        return $produk->no_form == null;
                    });
                    $jmldikirim = $penjualan->produk->every(function($produk){
                        return $produk->jumlah_dikirim == 0;
                    });
                }
                $penjualan->jmldikirim = $jmldikirim;
                $penjualan->do = $doData->get($penjualan->no_invoice) ? true : false;
                $penjualan->all_no_form = $allNoForm;
                $penjualan->retur = $returData->get($penjualan->no_invoice) ? true : false;
                return $penjualan;
            });
        
            return response()->json([
                'draw' => intval($req->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);
        }
        

        $sales = Karyawan::where('jabatan', 'Perangkai')->get();
        $customers = Customer::where('lokasi_id', $lokasi->lokasi_id)->get();

        return view('penjualan.index', compact('customers', 'sales'));
    }




    public function create()
    {
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->get();
        $customers = Customer::where('tipe', 'tradisional')->where('lokasi_id', $lokasi[0]->lokasi_id)->get();
        $lokasis = Lokasi::where('id', $lokasi[0]->lokasi_id)->get();
        $lokasigalery = Lokasi::where('tipe_lokasi', 1)->get();
        $ongkirs = Ongkir::where('lokasi_id', $lokasi[0]->lokasi_id)->get();
        $karyawans = Karyawan::where('lokasi_id', $lokasi[0]->lokasi_id)->where('jabatan', 'Sales')->get();
        $promos = Promo::where('lokasi_id', $lokasi[0]->lokasi_id)->orWhere('lokasi_id', 'Semua')->get();
        $produks = Produk_Jual::with('komponen.kondisi')->get();
        $komponenproduks = Komponen_Produk_Jual::all();
        $produkkompos = Produk_Jual::with('komponen.kondisi')
                        ->where(function($query) {
                            $query->where('kode', 'like', 'TRD%')
                                ->orWhere('kode', 'like', 'POT%');
                        })->get();

        $bankpens = Rekening::where('lokasi_id', $lokasi[0]->lokasi_id)->get();
        if($lokasi[0]->lokasi->tipe_lokasi == 2){
            $Invoice = Penjualan::where('no_invoice', 'LIKE', 'IPO%')->latest()->first();
        }elseif($lokasi[0]->lokasi->tipe_lokasi == 1){
            $Invoice = Penjualan::where('no_invoice', 'LIKE', 'INV%')->latest()->first();
        }
        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        $InvoiceBayar = Pembayaran::latest()->first();
        if ($InvoiceBayar != null) {
            $substringBayar = substr($InvoiceBayar->no_invoice_bayar, 11);
            $cekInvoiceBayar = substr($substringBayar, 0, 3);
        } else {
            $cekInvoiceBayar = 0;
        }
        $kondisis = Kondisi::all();
        $invoices = Penjualan::get();
        foreach($lokasis as $lokasi){
            $ceklokasi = $lokasi->tipe_lokasi;
        }


        return view('penjualan.create', compact('lokasigalery','ceklokasi','produkkompos', 'komponenproduks','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
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
            // 'bukti_file' => 'required|image|mimes:jpeg,png|max:2048',
            'notes' => 'required',
            'cara_bayar' => 'required',
            // 'pilih_pengiriman' => 'required',
            'biaya_ongkir' => 'required',
            'sub_total' => 'required',
            'jenis_ppn' => 'required',
            'jumlah_ppn' => 'required',
            'dp' => 'required',
            'total_tagihan' => 'required',
            'sisa_bayar' => 'required'
        ]);

        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'bukti_file', 'bukti', 'status_bayar']);

        $cust = Customer::where('id', $data['id_customer'])->first();
        if($cust->status_piutang == 'BELUM LUNAS' ){
            if($cust->status_buka == 'TUTUP'){
                return redirect()->back()->with('fail', 'Customer Belum Bisa Melakukan Transaksi');                                                                                                                                                                                                                                                                                                                                                    
            }
        }

        $user = Auth::user();
          
        $data['dibuat_id'] = Auth::user()->id;
        if ($req->hasFile('bukti_file')) {
            // Simpan file baru
            $file = $req->file('bukti_file');
            $fileName = $req->no_invoice . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_invoice_penjualan/' . $fileName;
        
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
                $data['bukti_file'] = $filePath;
            } else {
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        }
        if($req->cara_bayar == 'cash')
        {
            $data['jumlahCash'] = $req->nominal;
        }

        //cek no_invoice
        $lokasi = Lokasi::where('id', $data['lokasi_id'])->first();

        if ($lokasi->tipe_lokasi != 2) {
            $prefix = 'INV' . date('Ymd');
            $cekInvoice = Penjualan::where('no_invoice', 'LIKE', $prefix . '%')
                ->orderBy('no_invoice', 'desc')
                ->get();

            if ($cekInvoice->isEmpty()) {
                $increment = 1;
            } else {
                $lastInvoice = $cekInvoice->first()->no_invoice;
                $lastIncrement = (int)substr($lastInvoice, strlen($prefix));
                $increment = $lastIncrement + 1;
            }

            $data['no_invoice'] = $prefix . str_pad($increment, 3, '0', STR_PAD_LEFT);

        } elseif ($lokasi->tipe_lokasi != 1) {
            $prefix = 'IPO' . date('Ymd');
            $cekInvoice = Penjualan::where('no_invoice', 'LIKE', $prefix . '%')
                ->orderBy('no_invoice', 'desc')
                ->get();

            if ($cekInvoice->isEmpty()) {
                $increment = 1;
            } else {
                $lastInvoice = $cekInvoice->first()->no_invoice;
                $lastIncrement = (int)substr($lastInvoice, strlen($prefix));
                $increment = $lastIncrement + 1;
            }

            $data['no_invoice'] = $prefix . str_pad($increment, 3, '0', STR_PAD_LEFT);
        }

        $penjualan = Penjualan::create($data);
        if ($req->dp > 0 && $req->status == 'DIKONFIRMASI' && !$user->hasRole(['Auditor', 'Finance'])) {
            if ($req->hasFile('bukti')) {
                $file = $req->file('bukti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
                $datu['bukti'] = $filePath;
            }
            if ($req->sisa_bayar == 0) {
                $datu['invoice_penjualan_id'] = $penjualan->id;
                if($lokasi->tipe_lokasi == 1) {
                        $number = 1;
                        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
                        $datu['no_invoice_bayar'] = 'BYR' . date('Ymd') . $paddedNumber;
                }else if($lokasi->tipe_lokasi == 2) {
                        $number = 1;
                        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
                        $datu['no_invoice_bayar'] = 'BOT' . date('Ymd') . $paddedNumber;
                }
                $datu['cara_bayar'] = $req->cara_bayar;
                $datu['nominal'] = $req->nominal;
                $datu['rekening_id'] = $req->rekening_id;
                $datu['tanggal_bayar'] = $req->tanggal_invoice;
                $datu['status_bayar'] = 'LUNAS';
                $status = $datu['status_bayar'];
        
                // Update the customer's status correctly
                $updatecust = Customer::where('id', $req->id_customer)->update(['status_piutang' => $status]);
                $pembayaran = Pembayaran::create($datu);
            } else {
                $datu['invoice_penjualan_id'] = $penjualan->id;
                if($lokasi->tipe_lokasi == 1) {
                        $number = 1;
                        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
                        $datu['no_invoice_bayar'] = 'BYR' . date('Ymd') . $paddedNumber;
                }else if($lokasi->tipe_lokasi == 2) {
                        $number = 1;
                        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
                        $datu['no_invoice_bayar'] = 'BOT' . date('Ymd') . $paddedNumber;
                }
                $datu['cara_bayar'] = $req->cara_bayar;
                $datu['nominal'] = $req->nominal;
                $datu['rekening_id'] = $req->rekening_id;
                $datu['tanggal_bayar'] = $req->tanggal_invoice;
                $datu['status_bayar'] = 'BELUM LUNAS';
                $status = $datu['status_bayar'];
        
                // Update the customer's status correctly
                $updatecust = Customer::where('id', $req->id_customer)->update(['status_piutang' => $status]);
                $pembayaran = Pembayaran::create($datu);
            }
        }  
        $updatecust = Customer::where('id', $data['id_customer'])->update([
            'status_piutang' => 'BELUM LUNAS',
            'status_buka' => 'TUTUP',
        ]);
        $promo = Promo::where('id', $data['promo_id'])->first();
        if ($req->status == 'DIKONFIRMASI' && $promo->diskon_free_produk != null) {
            $produk = explode('-', $data['total_promo']);
            $kata_pertama = $produk[0];
            $data['nama_produk'] = array_merge([$kata_pertama], $data['nama_produk']);
        }
        // nambah produk jika dapet promo produk
        $promo = Promo::where('id', $data['promo_id'])->first();
        if ($req->status == 'DIKONFIRMASI' && $promo->diskon_free_produk != null) {
            $produk = explode('-', $data['total_promo']);
            $kata_pertama = $produk[0];
            $data['nama_produk'] = array_merge([$kata_pertama], $data['nama_produk']);
        }
        $lokasi = Lokasi::where('id', $req->lokasi_id)->first();
        if($lokasi->tipe_lokasi == 1){
            if ($penjualan) {
                for ($i = 0; $i < count($data['nama_produk']); $i++) {
                    $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                    if($data['diskon'][$i] == 'NaN'){
                        $data['diskon'][$i] = 0;
                    }

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

                // if ($req->hasFile('bukti')) {
                //     $file = $req->file('bukti');
                //     $fileName = time() . '_' . $file->getClientOriginalName();
                //     $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
                //     $data['bukti'] = $filePath;
                // }

                // dd($cek);
                // if ($req->dp > 0) {
                //     $data = [
                //         'invoice_penjualan_id' => $penjualan->id,
                //         'tanggal_bayar' => $req->tanggal_invoice,
                //         'status_bayar' => $req->sisa_bayar == 0 ? 'LUNAS' : 'BELUM LUNAS',
                //         'id_customer' => $req->id_customer, // Assuming this is part of $req
                //     ];
                //     $updatecust = Customer::where('id', $req->id_customer)->update(['status_piutang' => $data['status_bayar']]);
                //     $pembayaran = Pembayaran::create($data);
                //     if ($req->sisa_bayar == 0) {
                //         return redirect()->back()->with('success', 'Tagihan sudah Lunas');
                //     }
                // } else {
                //     // Handle logic when dp <= 0
                //     return redirect(route('penjualan.index'))->with('success', 'Data Berhasil Disimpan');
                // }
                

                // if(!empty($newProdukTerjual)){
                //     return redirect(route('auditpenjualan.edit', ['penjualan' => $penjualan->id]))->with('success', 'Silakan update invoice ke Dikonfirmasi');
                // }
                return redirect(route('penjualan.index'))->with('success', $penjualan->no_invoice . ' Berhasil Disimpan');
            } else {
                return redirect()->back()->with('fail', 'Gagal menyimpan data');
            }
        }elseif($lokasi->tipe_lokasi == 2){
            //buat produk penjualan dan komponen
            if ($penjualan) {
                for ($i = 0; $i < count($data['nama_produk']); $i++) {
                    $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                    // dd($getProdukJual);
                    if($data['diskon'][$i] == 'NaN'){
                        $data['diskon'][$i] = 0;
                    }
                    // dd($data);
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

                    if($req->distribusi == 'Diambil' && $req->status == 'DIKONFIRMASI'){
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

                // if ($req->hasFile('bukti')) {
                //     $file = $req->file('bukti');
                //     $fileName = time() . '_' . $file->getClientOriginalName();
                //     $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
                //     $data['bukti'] = $filePath;
                // }

                // dd($cek);
                // if ($req->dp > 0) {
                //     if ($req->sisa_bayar == 0) {
                //         $data['invoice_penjualan_id'] = $penjualan->id;
                //         $data['tanggal_bayar'] = $req->tanggal_invoice;
                //         $data['status_bayar'] = 'LUNAS';
                //         $pembayaran = Pembayaran::create($data);
                //         return redirect()->back()->with('success', 'Tagihan sudah Lunas');
                //     } else {
                //         $data['invoice_penjualan_id'] = $penjualan->id;
                //         $data['tanggal_bayar'] = $req->tanggal_invoice;
                //         $data['status_bayar'] = 'BELUM LUNAS';
                //         $pembayaran = Pembayaran::create($data);
                //     }
                // } else {
                //     return redirect(route('penjualan.index'))->with('success', 'Data Berhasil Disimpan');
                // }
                return redirect(route('penjualan.index'))->with('success', $penjualan->no_invoice . ' Berhasil Disimpan');
            } else {
                return redirect()->back()->with('fail', 'Gagal menyimpan data');
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
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->first();
        if($lokasi->lokasi->tipe_lokasi == 1){
            $Invoice = Pembayaran::where('no_invoice_bayar', 'LIKE', 'BYR' .date('Ymd').'%')->latest()->first();
        }elseif($lokasi->lokasi->tipe_lokasi == 2){
            $Invoice = Pembayaran::where('no_invoice_bayar', 'LIKE', 'BOT' .date('Ymd').'%')->latest()->first();
        }
        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 000;
        }
        $pembayarans = Pembayaran::with('rekening')->where('invoice_penjualan_id', $penjualan)->orderBy('created_at', 'desc')->get();
        $pembayaran = Pembayaran::where('invoice_penjualan_id', $penjualans->id)->first();
        $lokasigalery = Lokasi::where('tipe_lokasi', 1)->get();
        $roles = Auth::user()->roles()->value('name');
        $lokasis = Lokasi::where('id', $lokasi->lokasi_id)->get();
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
        return view('penjualan.payment', compact('lokasigalery','pembayaran','ceklokasi','pegawais','riwayat','customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'kondisis', 'invoices', 'penjualans', 'produkjuals', 'perangkai', 'cekInvoice', 'pembayarans'));
    }

    public function show(Request $req, $penjualan)
    {
        $produkjuals = Produk_Jual::all();
        // dd($produkjuals);
        $penjualans = Penjualan::with('dibuat')->where('id', $penjualan )->find($penjualan);
        $pembayaran = Pembayaran::where('invoice_penjualan_id', $penjualans->id)->first();
        // dd($penjualans);
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        $karyawans = Karyawan::where('id', $penjualans->employee_id)->get();
        $pegawais = Karyawan::all(); 
        $promos = Promo::where('id', $penjualans->promo_id)->get();
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $lokasigalery = Lokasi::where('tipe_lokasi', 1)->get();
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
        return view('penjualan.show', compact('lokasigalery','pembayaran','pegawais','riwayat','produkKomponens','customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'kondisis', 'invoices', 'penjualans', 'produkjuals', 'perangkai'));
    }

    public function store_komponen(Request $req)
    {

        //hanya update komponen terjual
        $validator = Validator::make($req->all(), [
            'komponen_id' => 'required',
            'kondisi_id' => 'required',
            'jumlahproduk' => 'required',
            'prdTerjual_id' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        $data = $req->except(['_token', '_method', 'route', 'produk_id', 'perangkai_id', 'prdTerjual_id']);
     
        $exsist = Komponen_Produk_Terjual::where('produk_terjual_id', $req->prdTerjual_id)->get();
        
        $jumlahItem = count($req->komponen_id);
        if($req->status == 'DIKONFIRMASI')
        {
            if ($exsist) {
                $exsist->each->forceDelete(); 
            }
            
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
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        $data = $req->except(['_token', '_method', 'route', 'produk_id', 'perangkai_id', 'prdTerjual_id']);
        $lokasi = Lokasi::where('id', $req->pengirim)->first();
        $allStockAvailable = true;

        $jumlahItem = count($req->komponen_id);
        
        // // Check stock availability and decrement stock
        // for ($i = 0; $i < $jumlahItem; $i++) {
        //     $produk = Produk::findOrFail($req->komponen_id[$i]);
        //     $stok = null;

        //     if ($lokasi->tipe_lokasi == 1) {
        //         $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
        //                                 ->where('kode_produk', $produk->kode)
        //                                 ->where('kondisi_id', $req->kondisi_id[$i])
        //                                 ->first();
        //     } elseif ($lokasi->tipe_lokasi == 3) {
        //         $stok = InventoryGreenHouse::where('lokasi_id', $req->pengirim)
        //                                 ->where('kode_produk', $produk->kode)
        //                                 ->where('kondisi_id', $req->kondisi_id[$i])
        //                                 ->first();
        //     }

        //     if (!$stok || $stok->jumlah < intval($req->jumlahproduk[$i]) * intval($req->jml_produk) || $stok->jumlah < $stok->min_stok) {
        //         $allStockAvailable = false;
        //         break;
        //     }
        // }

        // if (!$allStockAvailable) {
        //     return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory atau stok tidak mencukupi');
        // }

        // // Handle existing komponen produk terjual
        $exsist = Komponen_Produk_Terjual::where('produk_terjual_id', $req->prdTerjual_id)->get();
        // $jumlah = Produk_Terjual::where('id', $req->prdTerjual_id)->value('jumlah');
        // // dd($req->jml_produk);

        // foreach ($exsist as $item) {
        //     $produk = Produk::where('kode', $item->kode_produk)->first();
        //     $stok = null;

        //     if ($lokasi->tipe_lokasi == 1) {
        //         $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
        //                                 ->where('kode_produk', $produk->kode)
        //                                 ->where('kondisi_id', $item->kondisi)
        //                                 ->first();
        //     } elseif ($lokasi->tipe_lokasi == 3) {
        //         $stok = InventoryGreenHouse::where('lokasi_id', $req->pengirim)
        //                                 ->where('kode_produk', $produk->kode)
        //                                 ->where('kondisi_id', $item->kondisi)
        //                                 ->first();
        //     }

        //     if ($stok) {
        //         $stok->jumlah += intval($item->jumlah) * intval($jumlah);
        //         $stok->update();
        //     }
        // }
        // // dd($stok);
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

            // $stok = null;
            // if ($lokasi->tipe_lokasi == 1) {
            //     $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
            //                             ->where('kode_produk', $produk->kode)
            //                             ->where('kondisi_id', $req->kondisi_id[$i])
            //                             ->first();
            // } elseif ($lokasi->tipe_lokasi == 3) {
            //     $stok = InventoryGreenHouse::where('lokasi_id', $req->pengirim)
            //                             ->where('kode_produk', $produk->kode)
            //                             ->where('kondisi_id', $req->kondisi_id[$i])
            //                             ->first();
            // }

            // if ($stok) {
            //     $stok->jumlah -= intval($req->jumlahproduk[$i]) * intval($req->jml_produk);
            //     $stok->update();
            // }
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
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        $data = $req->except(['_token', '_method', 'route', 'produk_id', 'perangkai_id', 'prdTerjual_id']);
        $lokasi = Lokasi::where('id', $req->pengirim)->first();
        if($lokasi->tipe_lokasi == 1){
            $allStockAvailable = true;

            $jumlahItem = count($req->komponen_id);
            
            // Check stock availability and decrement stock
            // for ($i = 0; $i < $jumlahItem; $i++) {
            //     $produk = Produk::findOrFail($req->komponen_id[$i]);
            //     $stok = null;

            //     if ($lokasi->tipe_lokasi == 1) {
            //         $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
            //                                 ->where('kode_produk', $produk->kode)
            //                                 ->where('kondisi_id', $req->kondisi_id[$i])
            //                                 ->first();
            //     }

            //     if (!$stok || $stok->jumlah < intval($req->jumlahproduk[$i]) * intval($req->jml_produk) || $stok->jumlah < $stok->min_stok) {
            //         $allStockAvailable = false;
            //         break;
            //     }
            // }

            // if (!$allStockAvailable) {
            //     return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory atau stok tidak mencukupi');
            // }

            // Handle existing komponen produk terjual
            $exsist = Komponen_Produk_Terjual::where('produk_terjual_id', $req->prdTerjual_id)->get();
            // $jumlah = Produk_Terjual::where('id', $req->prdTerjual_id)->value('jumlah');
            // dd($req->jml_produk);

            // foreach ($exsist as $item) {
            //     $produk = Produk::where('kode', $item->kode_produk)->first();
            //     $stok = null;

            //     if ($lokasi->tipe_lokasi == 1) {
            //         $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
            //                                 ->where('kode_produk', $produk->kode)
            //                                 ->where('kondisi_id', $item->kondisi)
            //                                 ->first();
            //     }

            //     if ($stok) {
            //         $stok->jumlah += intval($item->jumlah) * intval($jumlah);
            //         $stok->update();
            //     }
            // }
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

                // $stok = null;
                // if ($lokasi->tipe_lokasi == 1) {
                //     $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                //                             ->where('kode_produk', $produk->kode)
                //                             ->where('kondisi_id', $req->kondisi_id[$i])
                //                             ->first();
                // }

                // if ($stok) {
                //     $stok->jumlah -= intval($req->jumlahproduk[$i]) * intval($req->jml_produk);
                //     $stok->update();
                // }
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
        if($data['cara_bayar'] == 'transfer'){
            $rek = Rekening::where('id', $data['rekening_id'])->first();
            // dd($data['rekening_id']);
            $data['bank'] = $rek->bank ;
            $data['norek'] = $rek->nomor_rekening;
            $data['akun'] = $rek->nama_akun;
        }
        
        // dd($data);
        $pdf = PDF::loadView('penjualan.view', $data);
    
        return $pdf->stream($data['no_invoice'] . '_INVOICE PENJUALAN.pdf');
    }

    public function excelPergantian($id)
    {
        return Excel::download(new PergantianExport, 'users.xlsx');
    }

    public function audit($penjualan)
    {
        $produkjuals = Produk_Jual::all();
        $produkjuals = Produk_Jual::all();
        $penjualans = Penjualan::with('dibuat')->where('id', $penjualan )->find($penjualan);
        $karyawans = Karyawan::where('id', $penjualans->employee_id)->get();
        $pegawais = Karyawan::all(); 
        $promos = Promo::where('id', $penjualans->promo_id)->get();
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $roles = Auth::user()->roles()->value('name');
        $user = Auth::user()->value('id');
        $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        $lokasis = Lokasi::where('id', $lokasi)->get();
        $rekenings = Rekening::get();
        $ongkirs = Ongkir::get();
        $bankpens = Rekening::get();
        $Invoice = Penjualan::latest()->first();
        $kondisis = Kondisi::all();
        $invoices = Penjualan::get();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();
        $penjualans = Penjualan::find($penjualan);
        $karyawans = Karyawan::where('id', $penjualans->employee_id)->get();
        $promos = Promo::where('id', $penjualans->promo_id)->get();
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $Invoice = Pembayaran::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 000;
        }
        $pembayarans = Pembayaran::with('rekening')->where('invoice_penjualan_id', $penjualan)->orderBy('created_at', 'desc')->get();
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
        $lokasigalery = Lokasi::where('tipe_lokasi', 1)->get();

        $riwayat = Activity::where('subject_type', Penjualan::class)->where('subject_id', $penjualan)->orderBy('id', 'desc')->get();
        foreach($lokasis as $lokasi){
            $ceklokasi = $lokasi->tipe_lokasi;
        }
        $pembayaran = Pembayaran::where('invoice_penjualan_id', $penjualans->id)->first();
        // dd($pembayaran);
        return view('penjualan.audit', compact('pembayaran','lokasigalery','produkKomponens','ceklokasi','pegawais','riwayat','customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'kondisis', 'invoices', 'penjualans', 'produkjuals', 'perangkai', 'cekInvoice', 'pembayarans'));
    }

    public function audit_update(Request $req)
    {
        // dd($req);
        $penjualanId = $req->input('penjualan');
        $nama_produk = $req->nama_produk;
        $diskon = $req->diskon;
        $harga = $req->harga_satuan;
        $user = Auth::user();
        $jabatan = Karyawan::where('user_id', $user->id)->first();
        $jabatanpegawai = $jabatan->jabatan;
        $data = $req->except(['_token', '_method', 'bukti', 'status_bayar', 'nominal', 'no_invoice_bayar', 'harga_total', 'diskon','jenis_diskon',  'jumlah', 'harga_satuan', 'nama_produk', 'DataTables_Table_0_length', 'DataTables_Table_1_length' , 'penjualan', 'file', 'ubahapa' ]);

        $penjualan = Penjualan::where('id', $penjualanId)->first();

        $lokasi = Lokasi::where('id', $req->lokasi_id)->first();

        //cek produk outlet
        if($lokasi->tipe_lokasi == 2 && $user->hasRole(['KasirOutlet']) || $user->hasRole(['Auditor']) && $req->ubahapa == 'ubahsemua' || $user->hasRole(['Finance']) && $req->ubahapa == 'ubahsemua') {
            $produkJumlah = [];

            for ($i = 0; $i < count($nama_produk); $i++) {
                if (isset($produkJumlah[$nama_produk[$i]])) {
                    $produkJumlah[$nama_produk[$i]] += intval($req->jumlah[$i]);
                } else {
                    $produkJumlah[$nama_produk[$i]] = intval($req->jumlah[$i]);
                }
            }

            foreach ($produkJumlah as $kode_produk => $total_jumlah) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $kode_produk)->first();

                if($req->distribusi == 'Diambil' && $req->status == 'DIKONFIRMASI' && $lokasi->tipe_lokasi == 2){
                    $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)
                                ->where('kode_produk', $getProdukJual->kode)
                                ->first();
                    if (!$stok) {
                        return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                    }

                    if (intval($stok->jumlah) < $total_jumlah) {
                        return redirect(route('inven_outlet.create'))->with('fail', 'Stok Produk Tidak Mencukupi');
                    }
                }
            }
        }

        if (($penjualan->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']) && $req->ubahapa == 'ubahsemua') ||
            ($user->hasRole(['Finance']) && $penjualan->status == 'DIKONFIRMASI' && $req->ubahapa == 'ubahsemua')) {
            $deletepj = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualan->no_invoice)->get();
            //penambahan komponen produk terjual yang di edit
            $lokasi = Lokasi::where('id', $req->lokasi_id)->first();
            if($lokasi->tipe_lokasi == 1) {
                foreach($deletepj as $deletepjList) 
                {
                    foreach($deletepjList->komponen as $komponen) 
                    {
                        $allStockAvailable = true;

                        $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)
                                                ->where('kode_produk', $komponen->kode_produk)
                                                ->where('kondisi_id', $komponen->kondisi)
                                                ->first();
                        if (!$stok) {
                            $allStockAvailable = false;
                            break 2; // Keluar dari kedua loop
                        }

                        $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($deletepjList->jumlah));
                        $stok->update();
                    }
                }

                if (!$allStockAvailable) {
                    return redirect(route('inven_galeri.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                }
            }else if($lokasi->tipe_lokasi == 2) {
                foreach($deletepj as $deletepjList) 
                {
                    $allStockAvailable = true;

                    $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)
                                            ->where('kode_produk', $deletepjList->produk->kode)
                                            ->first();
    
                    if (!$stok) {
                        $allStockAvailable = false;
                        break 1;
                    }

                    $stok->jumlah = intval($stok->jumlah) + intval($deletepjList->jumlah);
                    $stok->update();
                    
                }

                if (!$allStockAvailable) {
                    return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                }
            }
            
            //
            if ($deletepj->isNotEmpty()) {
                $array = $deletepj->pluck('id')->toArray();
                Komponen_Produk_Terjual::whereIn('produk_terjual_id', $array)->forceDelete();
                Produk_Terjual::whereIn('id', $array)->forceDelete();
            }
        }else if(!$user->hasRole(['Auditor', 'Finance'])) {

            $deletepj = Produk_Terjual::with('komponen')->where('no_invoice', $penjualan->no_invoice)->get();
            if ($deletepj->isNotEmpty()) {
                $array = $deletepj->pluck('id')->toArray();
                Komponen_Produk_Terjual::whereIn('produk_terjual_id', $array)->forceDelete();
                Produk_Terjual::whereIn('id', $array)->forceDelete();
            }
        }
        
        //update poin
        if($penjualan->status == 'DIKONFIRMASI'){
            function extractNumber($string) {
                if (empty($string)) {
                    return 0;
                }
                $string = preg_replace('/[^\d,.]/', '', $string);
                $string = str_replace(',', '.', $string);
                return floatval($string);
            }
            if(!empty($data['promo_id'])) {
                $penjualan = Penjualan::where('no_invoice', $req->no_invoice)->first();
                $promo = Promo::where('id', $penjualan->promo_id)->first();
                $checkpromo = Promo::where('id', $data['promo_id'])->first();
                if ($checkpromo->diskon === 'poin') {
                    $totalPromo = extractNumber($data['total_promo']);
                    $customer = Customer::find($data['id_customer']);
                    if($user->hasRole(['Auditor', 'Finance'])) {
                        if($promo->diskon == 'poin') {
                            $totalpromo = intval($promo->diskon_poin);
                        }
                        $currentPoints = floatval($customer->poin_loyalty);
                        $updatedPoin = $totalpromo - $currentPoints;
                        $customer->update([
                            'poin_loyalty' => $updatedPoin,
                            'status_buka' => 'TUTUP'
                        ]);
                    }
                
                    if ($customer) {
                        $currentPoints = floatval($customer->poin_loyalty);
                        $updatedPoints = $totalPromo + $currentPoints;
                
                        $customer->update([
                            'poin_loyalty' => $updatedPoints,
                            'status_buka' => 'TUTUP'
                        ]);
                    }
                }
            }
        }
        if($penjualan->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor'])){
            $data['auditor_id'] = Auth::user()->id;
        }elseif($penjualan->status == 'DIKONFIRMASI' && $user->hasRole(['Finance'])){
            $data['dibukukan_id'] = Auth::user()->id;
        }

        // if ($penjualan) {
        //     $delete = $penjualan->bukti_file;
        //     $deletefile = Storage::disk('public')->delete('bukti_invoice_penjualan/' . $delete);
        // }

        // if ($req->hasFile('file')) {
        //     $file = $req->file('file');
        //     $fileName = time() . '_' . $file->getClientOriginalName();
        //     $filePath = $file->storeAs('bukti_invoice_penjualan', $fileName, 'public');
        //     // dd($filePath);
        //     $data['bukti_file'] = $filePath;
        // }

        if ($req->hasFile('file')) {
            // Simpan file baru
            $file = $req->file('file');
            $fileName = $req->no_invoice . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_invoice_penjualan/' . $fileName;
        
            // Optimize dan simpan file baru
            Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                ->save(storage_path('app/public/' . $filePath));
        
            // Hapus file lama
            if (!empty($penjualan->bukti_file)) {
                $oldFilePath = storage_path('app/public/' . $penjualan->bukti_file);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }
        
            // Verifikasi penyimpanan file baru
            if (File::exists(storage_path('app/public/' . $filePath))) {
                $data['bukti_file'] = $filePath;
            } else {
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        }

        if ($req->dp > 0 && $req->status == 'DIKONFIRMASI' && !$user->hasRole(['Auditor', 'Finance'])) {
            if ($req->hasFile('bukti')) {
                $file = $req->file('bukti');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
                $datu['bukti'] = $filePath;
            }
            if ($req->sisa_bayar == 0) {
                $datu['invoice_penjualan_id'] = $penjualanId;
                if($lokasi->tipe_lokasi == 1) {
                        $number = 1;
                        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
                        $datu['no_invoice_bayar'] = 'BYR' . date('Ymd') . $paddedNumber;
                }else if($lokasi->tipe_lokasi == 2) {
                        $number = 1;
                        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
                        $datu['no_invoice_bayar'] = 'BOT' . date('Ymd') . $paddedNumber;
                }
                $datu['cara_bayar'] = $req->cara_bayar;
                $datu['nominal'] = $req->nominal;
                $datu['rekening_id'] = $req->rekening_id;
                $datu['tanggal_bayar'] = $req->tanggal_invoice;
                $datu['status_bayar'] = 'LUNAS';
                $status = $datu['status_bayar'];
        
                // Update the customer's status correctly
                $updatecust = Customer::where('id', $req->id_customer)->update(['status_piutang' => $status]);
                $pembayaran = Pembayaran::create($datu);
            } else {
                $datu['invoice_penjualan_id'] = $penjualanId;
                if($lokasi->tipe_lokasi == 1) {
                        $number = 1;
                        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
                        $datu['no_invoice_bayar'] = 'BYR' . date('Ymd') . $paddedNumber;
                }else if($lokasi->tipe_lokasi == 2) {
                        $number = 1;
                        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
                        $datu['no_invoice_bayar'] = 'BOT' . date('Ymd') . $paddedNumber;
                }
                $datu['cara_bayar'] = $req->cara_bayar;
                $datu['nominal'] = $req->nominal;
                $datu['rekening_id'] = $req->rekening_id;
                $datu['tanggal_bayar'] = $req->tanggal_invoice;
                $datu['status_bayar'] = 'BELUM LUNAS';
                $status = $datu['status_bayar'];
        
                // Update the customer's status correctly
                $updatecust = Customer::where('id', $req->id_customer)->update(['status_piutang' => $status]);
                $pembayaran = Pembayaran::create($datu);
            }
        }  

        $bayarList = Pembayaran::where('invoice_penjualan_id', $penjualan->id)->get();
        // dd($bayarList);
        if(!empty($bayarList)) {
            $totalBayar = 0;
            foreach($bayarList as $byr){
                $totalBayar += $byr->nominal;
            }
            $data['sisa_bayar'] = intval($data['total_tagihan']) - intval($totalBayar);
        }

        $update = Penjualan::where('id', $penjualanId)->update($data);
        
        if (($penjualan->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor']) && $req->ubahapa == 'ubahsemua') ||
            ($user->hasRole(['Finance']) && $penjualan->status == 'DIKONFIRMASI' && $req->ubahapa == 'ubahsemua') ||
            (!$user->hasRole(['Auditor', 'Finance']))) {
            for ($i = 0; $i < count($nama_produk); $i++) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $nama_produk[$i])->first();
                // dd($getProdukJual);
                if($diskon[$i] == 'NaN'){
                    $diskon[$i] = 0;
                }
                // dd($data);
                $produkTerjualData = [
                    'produk_jual_id' => $getProdukJual->id,
                    'no_invoice' => $req->no_invoice,
                    'harga' => $harga[$i],
                    'jumlah' => $req->jumlah[$i],
                    'jenis_diskon' => $req->jenis_diskon[$i],
                    'diskon' => $req->diskon[$i],
                    'harga_jual' => $req->harga_total[$i]
                ];
                
                if ($req->distribusi == 'Dikirim') {
                    $produkTerjualData['jumlah_dikirim'] = $req->jumlah[$i];
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

                //pengurangan jika lokasinya outlet

                if($req->distribusi == 'Diambil' && $req->status == 'DIKONFIRMASI' && $lokasi->tipe_lokasi == 2){
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
        }
        
        
        if($req->status == 'DIKONFIRMASI' && $lokasi->tipe_lokasi != 2){
            return redirect(route('penjualan.show', ['penjualan' => $penjualan->id]))->with('success', 'Silakan set Komponen Gift');
        }elseif($req->status == 'DIKONFIRMASI' && $lokasi->tipe_lokasi != 1){
            return redirect(route('penjualan.index'))->with('success', 'Berhasil Mengupdate data');
        }elseif($req->status == 'TUNDA'){
            return redirect(route('penjualan.index'))->with('success', 'Berhasil Mengupdate data');
        }else{
            return redirect()->back()->with('fail', 'Gagal Mengupdate data');
        }
    }

    public function audit_show($penjualan)
    {
        $produkjuals = Produk_Jual::all();
        // dd($produkjuals);
        $produkjuals = Produk_Jual::all();
        // dd($produkjuals);
        $penjualans = Penjualan::with('dibuat')->where('id', $penjualan )->find($penjualan);
        // dd($penjualans);
        
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
        // dd($lokasi);
        $customers = Customer::where('lokasi_id', $lokasi)->get();
        // dd($customers);
        $lokasis = Lokasi::where('id', $lokasi)->get();
        $rekenings = Rekening::get();
        $ongkirs = Ongkir::get();
        $bankpens = Rekening::get();
        $Invoice = Penjualan::latest()->first();
        $kondisis = Kondisi::all();
        $invoices = Penjualan::get();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();
        $penjualans = Penjualan::find($penjualan);
        // $customers = Customer::where('id', $penjualans->id_customer)->get();
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
        $lokasigalery = Lokasi::where('tipe_lokasi', 1)->get();

        $riwayat = Activity::where('subject_type', Penjualan::class)->where('subject_id', $penjualan)->orderBy('id', 'desc')->get();
        foreach($lokasis as $lokasi){
            $ceklokasi = $lokasi->tipe_lokasi;
        }

        // return view('penjualan.create', compact('customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis','invoices'));
    return view('penjualan.showaudit', compact('lokasigalery','produkKomponens','ceklokasi','pegawais','riwayat','customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'kondisis', 'invoices', 'penjualans', 'produkjuals', 'perangkai', 'cekInvoice', 'pembayarans'));
    }

    public function view_penjualan ($penjualan)
    {
        $produkjuals = Produk_Jual::all();
        $penjualans = Penjualan::with('dibuat')->where('id', $penjualan )->find($penjualan);
        $pembayaran = Pembayaran::where('invoice_penjualan_id', $penjualans->id)->first();
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        $karyawans = Karyawan::where('id', $penjualans->employee_id)->get();
        $pegawais = Karyawan::all(); 
        $promos = Promo::where('id', $penjualans->promo_id)->get();
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $lokasigalery = Lokasi::where('tipe_lokasi', 1)->get();
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
        $produkterjuals = Produk_Terjual::all();
        //retur penjualan
        $perPendapatan = [];
        $retur = ReturPenjualan::where('no_invoice', $penjualans->no_invoice)->where('status', 'DIKONFIRMASI')->first();

        if($penjualans->distribusi == 'Dikirim') {
            foreach ($retur->produk_retur as $produk) {
                $selectedGFTKomponen = [];
    
                foreach ($produkterjuals as $index => $pj) {
                    if($pj->produk && $produk->produk->kode)
                    {
                        $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_retur ==  $produk->no_retur && $pj->jenis != 'GANTI');
                        if ($isSelectedGFT) {
                            foreach ($pj->komponen as $komponen) {
                                if ($pj->id == $komponen->produk_terjual_id) {
                                    foreach ($kondisis as $kondisi) {
                                        if ($kondisi->id == $komponen->kondisi) {
                                            $selectedGFTKomponen[$produk->no_retur][] = [
                                                'kode' => $komponen->kode_produk,
                                                'nama' => $komponen->nama_produk,
                                                'kondisi' => $kondisi->nama,
                                                'jumlah' => $komponen->jumlah,
                                                'produk' => $komponen->produk_terjual_id
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($selectedGFTKomponen)) {
                        $perPendapatan += $selectedGFTKomponen;
                    }
                } 
            }
        }else if($penjualans->distribusi == 'Diambil') {
            foreach ($retur->produk_retur as $produk) {
                $selectedGFTKomponen = [];
    
                foreach ($produkterjuals as $index => $pj) {
                    if($pj->produk && $produk->produk->kode)
                    {
                        $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_retur ==  $produk->no_retur && $pj->jenis != 'GANTI');
                        if ($isSelectedGFT) {
                            foreach ($pj->komponen as $komponen) {
                                if ($pj->id == $komponen->produk_terjual_id) {
                                    foreach ($kondisis as $kondisi) {
                                        if ($kondisi->id == $komponen->kondisi) {
                                            $selectedGFTKomponen[$produk->no_retur][] = [
                                                'kode' => $komponen->kode_produk,
                                                'nama' => $komponen->nama_produk,
                                                'kondisi' => $kondisi->nama,
                                                'jumlah' => $komponen->jumlah,
                                                'produk' => $komponen->produk_terjual_id
                                            ];
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if (!empty($selectedGFTKomponen)) {
                        $perPendapatan += $selectedGFTKomponen;
                    }
                } 
            }
            // foreach ($retur as $returpenjualans) {
            //     foreach ($returpenjualans->produk_retur as $produk) {
            //         $selectedGFTKomponen = [];
                    
            //         foreach ($produkterjuals as $index => $pj) {
            //             if($pj->produk && $produk->produk->kode)
            //             {
            //                 $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_retur ==  $produk->no_retur && $pj->jenis != 'GANTI');
            //                 if ($isSelectedGFT) {
            //                     foreach ($pj->komponen as $komponen) {
            //                         if ($pj->id == $komponen->produk_terjual_id) {
            //                             foreach ($kondisis as $kondisi) {
            //                                 if ($kondisi->id == $komponen->kondisi) {
            //                                     $selectedGFTKomponen[$produk->no_retur][] = [
            //                                         'kode' => $komponen->kode_produk,
            //                                         'nama' => $komponen->nama_produk,
            //                                         'kondisi' => $kondisi->nama,
            //                                         'jumlah' => $komponen->jumlah,
            //                                         'produk' => $komponen->produk_terjual_id
            //                                     ];
            //                                 }
            //                             }
            //                         }
            //                     }
            //                 }
            //             }
            //             if (!empty($selectedGFTKomponen)) {
            //                 $perPendapatan += $selectedGFTKomponen;
            //             }
            //         }
            //     }
            // }
        }
        // dd($perPendapatan);
        
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
        return view('penjualan.viewpenjualan', compact('produkterjuals','perPendapatan','retur','lokasigalery','pembayaran','pegawais','riwayat','produkKomponens','customers', 'lokasis', 'karyawans', 'rekenings', 'promos', 'produks', 'ongkirs', 'bankpens', 'kondisis', 'invoices', 'penjualans', 'produkjuals', 'perangkai'));
    }
}
