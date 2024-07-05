<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Komponen_Produk_Jual;
use App\Models\Kondisi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Tipe_Produk;
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
use App\Models\Pembayaran;
use App\Models\InventoryGallery;
use App\Models\InventoryOutlet;
use App\Models\InventoryGreenHouse;
use App\Models\Mutasi;
use App\Models\ProdukMutasi;
use App\Models\ReturPenjualan;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class MutasiController extends Controller
{
    public function index_outlet(Request $req)
    {
        $query = Mutasi::where('no_mutasi', 'like', 'MGO%')->orderBy('created_at', 'desc');

        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }
        $mutasis = $query->get();
        // $mutasis = Mutasi::where('no_mutasi', 'like', 'MGO%')->orderBy('created_at', 'desc')->get();
        return view('mutasigalery.index', compact('mutasis'));
    }

    public function create_outlet()
    {
        $roles = Auth::user()->roles()->value('name');
        $user = Auth::user()->value('id');
        $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
        // dd($karyawans);
        $customers = Customer::where('lokasi_id', $lokasi)->get();
        $lokasis = Lokasi::where('id', $lokasi)->get();
        $ongkirs = Ongkir::get();
        $karyawans = Karyawan::where('lokasi_id', $lokasi)->get();
        $promos = Promo::where(function ($query) use ($lokasi) {
            $query->where('lokasi_id', $lokasi)
                ->orWhere('lokasi_id', 'Semua');
        })->get();
        $produks = Produk_Jual::with('komponen.kondisi')->get();
        // dd($produks);
        $bankpens = Rekening::get();
        $Invoice = Mutasi::latest()->first();
        $lokasipengirim = Lokasi::where('tipe_lokasi', 1)->get();
        $lokasipenerima = Lokasi::where('tipe_lokasi', 2)->get();
        // dd($bankpens);
        if ($Invoice != null) {
            $substring = substr($Invoice->no_mutasi, 11);
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

        return view('mutasigalery.create', compact('lokasipengirim','lokasipenerima','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
    }

    public function store_outlet(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'pengirim' => 'required',
            'penerima' => 'required',
            'no_mutasi' => 'required',
            'tanggal_kirim' =>'required',
            'tanggal_diterima' => 'required',
            'status' => 'required',
            'pilih_pengiriman' => 'required',
            'biaya_pengiriman' =>'required',
            'total_biaya' => 'required'
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $req->except(['_token', '_method', 'bukti_file', 'bukti', 'status_bayar']);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_mutasi', $fileName, 'public');
            // dd($filePath);
            $data['bukti'] = $filePath;
        }

        $data['pembuat_id'] = Auth::user()->id;
        $data['tanggal_pembuat'] = now();
        
        $mutasi = Mutasi::create($data);

        if ($mutasi) {
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
                // dd($getProdukJual);
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_mutasigo' => $mutasi->no_mutasi,
                    'jumlah' => $data['jumlah_dikirim'][$i],
                ]);

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
            return redirect(route('mutasigalery.index'))->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }
    public function show_outlet($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigo', $mutasis->no_mutasi)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Jual::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasigalery.show', compact('perangkai','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function update_outlet(Request $req, $mutasi)
    {

        $validator = Validator::make($req->all(), [
            'jumlah_diterima' => 'required',
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $req->except(['_token', '_method']);
        // dd($data);
        //cek produk
        $updateProdukTerjual = Produk_Terjual::with('komponen')->find($req->prdTerjual_id);

        // dd($updateProdukTerjual);
        $lokasi = Lokasi::where('id', $req->penerima)->first();

        
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            // Fetch the product mutation record
            $produkMutasi = Produk_Terjual::where('no_mutasigo', $req->no_mutasi)
                                          ->where('id', $data['nama_produk'][$i])
                                          ->first();
        
            if (!$produkMutasi) {
                return redirect()->back()->withInput()->with('fail', 'Produk Mutasi tidak ditemukan');
            }
        
            // Fetch the stock record from the inventory gallery
            $stok = InventoryOutlet::where('lokasi_id', $req->penerima)
                                    ->where('kode_produk', $produkMutasi->produk->kode)
                                    ->first();
            // dd($stok);
        
            if (!$stok) {
                return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
            }

            if($produkMutasi->jumlah_diterima != null){
                // Update the stock quantity
                $stok->jumlah = intval($stok->jumlah) - intval($produkMutasi->jumlah_diterima);
                $stok->save();
            }

            $stok->jumlah = intval($stok->jumlah) + intval($data['jumlah_diterima'][$i]);
            $stok->save();
            
            // Update the received quantity in the product mutation
            $produkMutasi->update([
                'jumlah_diterima' => $data['jumlah_diterima'][$i],
            ]);
        }        
        
        return redirect(route('mutasigalery.index'))->with('success', 'Data Berhasil Disimpan');
    }

    public function payment_outlet($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigo', $mutasis->no_mutasi)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Jual::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        $Invoice = Pembayaran::where('mutasi_id', $mutasis->id)->where('no_invoice_bayar', 'LIKE', 'BGO%')->latest()->first();

        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 000;
        }

        // // Mengambil daftar nomor invoice dari data retur
        // $noInvoices = $mutasis->pluck('no_invoice')->toArray();

        // // Mengambil data penjualan yang memiliki nomor invoice yang sama dengan retur
        // $cekbayar = Penjualan::with('karyawan')->whereIn('no_invoice', $noInvoices)->first();
        $pembayarans = Pembayaran::with('rekening')->where('mutasi_id', $mutasis->id)->where('no_invoice_bayar', 'LIKE', 'BGO%')->orderBy('created_at', 'desc')->get();
        $totalbiaya = 0;
        foreach($pembayarans as $tagihanbayar){
            $totalbiaya += $tagihanbayar->nominal;
        }
        $totaltagihan = $mutasis->total_biaya - $totalbiaya;

        // dd($totaltagihan);
        return view('mutasigalery.payment', compact('totaltagihan','cekInvoice','pembayarans','perangkai','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function view_outlet($mutasi){
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigo', $mutasis->no_mutasi)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Jual::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasigalery.view', compact('perangkai','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function paymentmutasi(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'mutasi_id' => 'required',
            'no_invoice_bayar' => 'required',
            'nominal' => 'required',
            'tanggal_bayar' => 'required',
            'bukti' => 'required|file',
        ]);

        // dd($validator);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $req->except(['_token', '_method', 'bukti', 'status_bayar']);

        // dd($data);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
            $data['bukti'] = $filePath;
        }

        $penjualan = Mutasi::find($req->mutasi_id);

        // dd($penjualan);
        if ($penjualan) {
            if(substr($penjualan->no_mutasi, 0, 3) == 'MGO'){
                $cekTotalTagihan = Pembayaran::where('mutasi_id', $penjualan->mutasi_id)->where('no_invoice_bayar', 'LIKE', 'BGO%')->get();
            }elseif(substr($penjualan->no_mutasi, 0, 3) == 'MOG'){
                $cekTotalTagihan = Pembayaran::where('mutasi_id', $penjualan->mutasi_id)->where('no_invoice_bayar', 'LIKE', 'BOG%')->get();
            }elseif(substr($penjualan->no_mutasi, 0, 3) == 'MGG'){
                $cekTotalTagihan = Pembayaran::where('mutasi_id', $penjualan->mutasi_id)->where('no_invoice_bayar', 'LIKE', 'BGG%')->get();
            }elseif(substr($penjualan->no_mutasi, 0, 3) == 'MGA'){
                $cekTotalTagihan = Pembayaran::where('mutasi_id', $penjualan->mutasi_id)->where('no_invoice_bayar', 'LIKE', 'BGA%')->get();
            }
            $totalbiaya = 0;
            foreach($cekTotalTagihan as $cekTotal){
                $totalbiaya += $cekTotal->nominal; 
            }
            // dd($totalbiaya);
            // $penjualan->update([
            //     'sisa_bayar' => $cekTotalTagihan,
            // ]);
            $cek = $penjualan->total_biaya - ($totalbiaya + $req->nominal);
            // dd($cek);
            if ($cek <= 0) {
                $data['status_bayar'] = 'LUNAS';
                $pembayaran = Pembayaran::create($data);
                return redirect()->back()->with('success', 'Tagihan sudah Lunas');
            } else {
                $data['status_bayar'] = 'BELUM LUNAS';
                $pembayaran = Pembayaran::create($data);
            }
        } else {
            return redirect()->back()->with('fail', 'Tagihan tidak ditemukan.');
        }

        if ($pembayaran) {
            return redirect()->back()->with('success', 'Berhasil menyimpan data');
        } else {
            return redirect()->back()->with('fail', 'Gagal menyimpan data');
        }
    }

    public function index_outletgalery(Request $req)
    {
        $query = Mutasi::where('no_mutasi', 'like', 'MOG%')->orderBy('created_at', 'desc');

        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }
        $mutasis = $query->get();

        // $mutasis = Mutasi::where('no_mutasi', 'like', 'MOG%')->orderBy('created_at', 'desc')->get();
        return view('mutasioutlet.index', compact('mutasis'));
    }

    public function create_outletgalery($returpenjualan)
    {
        // $roles = Auth::user()->roles()->value('name');
        // if ($roles == 'admin' || $roles == 'kasir') {
            $user = Auth::user()->value('id');
            $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
            // dd($karyawans);
            $customers = Customer::where('lokasi_id', $lokasi)->get();
            $lokasis = Lokasi::where('id', $lokasi)->get();
            $ongkirs = Ongkir::get();
            $karyawans = Karyawan::where('lokasi_id', $lokasi)->get();
            $promos = Promo::where(function ($query) use ($lokasi) {
                $query->where('lokasi_id', $lokasi)
                    ->orWhere('lokasi_id', 'Semua');
            })->get();
            
            $bankpens = Rekening::get();
            $Invoice = Mutasi::where('no_mutasi', 'LIKE', 'MOG%')->latest()->first();
            // dd($bankpens);
            if ($Invoice != null) {
                $substring = substr($Invoice->no_mutasi, 11);
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
            $lokasipengirim = Lokasi::where('tipe_lokasi', 2)->get();
            $lokasipenerima = Lokasi::where('tipe_lokasi', 1)->get();
            $kondisis = Kondisi::all();
            $invoices = Penjualan::get();
        // }

        $returs = ReturPenjualan::with('produk')->find($returpenjualan);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_retur', $returs->no_retur)->where('jenis', 'LIKE', 'RETUR')->get();
        // $produks = Produk_Terjual::with('komponen.kondisi')->get();
        // dd($produks);
        // dd($returs);
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Terjual::all();

        $selectedGFTKomponen = [];
        $perPendapatan = [];

        // foreach ($returs->produk_retur as $produk) {
        //     $selectedGFTKomponen = [];
            
        //     // foreach ($deliveryOrder->produk as $produk) {
        //         foreach ($produkjuals as $index => $pj) {
        //             // dd($produkjuals);
        //             if($pj->produk && $produk->produk->kode)
        //             {
        //                 $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_retur ==  $produk->no_retur && $pj->jenis != 'GANTI');
                    
        //             if ($isSelectedGFT) {
        //                 foreach ($pj->komponen as $komponen) {
        //                     if ($pj->id == $komponen->produk_terjual_id) {
        //                         foreach ($kondisis as $kondisi) {
        //                             if ($kondisi->id == $komponen->kondisi) {
        //                                 $selectedGFTKomponen[$produk->no_retur][] = [
        //                                     'kode' => $komponen->kode_produk,
        //                                     'nama' => $komponen->nama_produk,
        //                                     'kondisi' => $kondisi->nama,
        //                                     'jumlah' => $komponen->jumlah,
        //                                     'produk' => $komponen->produk_terjual_id
        //                                 ];
        //                             }
        //                         }
        //                     }
        //                 }
        //             }
        //             }
        //             if (!empty($selectedGFTKomponen)) {
        //                 $perPendapatan += $selectedGFTKomponen;
        //             }
        //         }
                
        //     // }

            
        // }

        foreach ($returs->produk_retur as $produk) {
            foreach ($produkjuals as $index => $pj) {
                if($pj->produk && $produk->produk->kode)
                {
                    $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_retur == $returs->no_retur && $pj->jenis != 'GANTI');
                    if ($isSelectedGFT) {
                        foreach ($pj->komponen as $komponen) {
                            foreach ($kondisis as $kondisi) {
                                if ($kondisi->id == $komponen->kondisi) {
                                    $selectedGFTKomponen[$komponen->produk_terjual_id][] = [
                                        'kode' => $komponen->kode_produk,
                                        'nama' => $komponen->nama_produk,
                                        'kondisi' => $kondisi->nama,
                                        'jumlah' => $komponen->jumlah,
                                        'produk' => $komponen->produk_terjual_id,
                                    ];
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
        
        


        // dd($perPendapatan);
        $returpenjualans = ReturPenjualan::with('deliveryorder')->find($returpenjualan);

        return view('mutasioutlet.create', compact('lokasipengirim','lokasipenerima','perPendapatan','produkjuals','returpenjualans','customers', 'lokasis', 'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
    }

    public function acc_outlet($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigo', $mutasis->no_mutasi)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }

        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Terjual::where('no_mutasigo', $mutasis->no_mutasi)->with('produk')->get();

        $selectedGFTKomponen = [];
        $perPendapatan = [];
        
        foreach ($mutasis->produkMutasi as $produk) {
            foreach ($produkjuals as $index => $pj) {
                if ($pj->produk && $produk->produk) {
                    // dd($produk->produk, $pj->produk);
                    $isSelectedGFT = (
                        $pj->produk->kode == $produk->produk->kode &&
                        substr($produk->produk->kode, 0, 3) === 'GFT' &&
                        $pj->no_mutasigo == $mutasis->no_mutasi &&
                        $pj->jenis != 'TAMBAHAN'
                    );
    
                    if ($isSelectedGFT) {
                        foreach ($pj->komponen as $komponen) {
                            if ($pj->id == $komponen->produk_terjual_id) {
                                foreach ($kondisis as $kondisi) {
                                    if ($kondisi->id == $komponen->kondisi) {
                                        $selectedGFTKomponen[$komponen->produk_terjual_id][] = [
                                            'nama' => $komponen->nama_produk,
                                            'kondisi' => $kondisi->nama,
                                            'jumlah' => $komponen->jumlah,
                                            'produk' => $komponen->produk_terjual_id,
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            }        
        }
            
            

            if (!empty($selectedGFTKomponen)) {
                $perPendapatan += $selectedGFTKomponen;
            }

            // dd($perPendapatan);

            

        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        return view('mutasigalery.acc', compact('perPendapatan', 'produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function store_outletgalery(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'pengirim' => 'required',
            'penerima' => 'required',
            'no_mutasi' => 'required',
            'tanggal_kirim' =>'required',
            'tanggal_diterima' => 'required',
            'status' => 'required',
            'pilih_pengiriman' => 'required',
            'biaya_pengiriman' =>'required',
            'total_biaya' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        $data = $req->except(['_token', '_method']);
        // dd($data);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_mutasi', $fileName, 'public');
            // dd($filePath);
            $data['bukti'] = $filePath;
        }

        $lokasi = Lokasi::where('id', $req->pengirim)->first();
        $data['pembuat_id'] = Auth::user()->id;
        $data['tanggal_pembuat'] = now();

        // // cek produk inventory
        // $allStockAvailable = true;

        // for ($i = 0; $i < count($data['nama_produk']); $i++) {
        //     $getProdukJual = Produk_Terjual::with('komponen')->where('id', $data['nama_produk'][$i])->first();

        //     foreach ($getProdukJual->komponen as $komponen ) {
        //         $stok = InventoryOutlet::where('lokasi_id', $req->pengirim)
        //                                 ->where('kode_produk', $komponen->kode_produk)
        //                                 ->where('kondisi_id', $komponen->kondisi)
        //                                 ->first();
        //         if (!$stok) {
        //             $allStockAvailable = false;
        //             break;
        //         }
        //     }

        //     if (!$allStockAvailable) {
        //         return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
        //     }
        // }
        
        $mutasi = Mutasi::create($data);

        $cek = [];
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = Produk_Terjual::with('komponen')->where('id', $data['nama_produk'][$i])->first();
            
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->produk_jual_id,
                'no_mutasiog' => $mutasi->no_mutasi,
                'jumlah' => $data['jumlah_dikirim'][$i]
            ]);

            if($getProdukJual){
                $getProdukJual->jumlah_dikirim = intval($getProdukJual->jumlah_dikirim) - intval($produk_terjual->jumlah);
                $getProdukJual->update();
            }
            
            if (!$produk_terjual) {
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }

            $cekgfttrd = substr($getProdukJual->produk->kode, 0, 3);

            if ($cekgfttrd == 'GFT') {
                $kode_key = 'kodegiftproduk_' . $i;
                $komponen_key = 'komponengiftproduk_' . $i;
                $kondisi_key = 'kondisigiftproduk_' . $i;
                $jumlah_key = 'jumlahgiftproduk_' . $i;

                if (isset($data[$komponen_key]) && is_array($data[$komponen_key])) {
                    $cekcount = count($data[$komponen_key]);
                    $produkCollection = collect();
                    
                    // Membuat koleksi produk dari kode produk yang diberikan
                    foreach ($data[$kode_key] as $index => $kodeProduk) {
                        $produk = Produk::where('kode', $kodeProduk)->first();
                        if ($produk) {
                            $produkCollection->push([
                                'produk' => $produk,
                                'kondisi' => isset($data[$kondisi_key][$index]) ? Kondisi::where('nama', $data[$kondisi_key][$index])->value('id') : null,
                                'jumlah' => isset($data[$jumlah_key][$index]) ? $data[$jumlah_key][$index] : 0,
                            ]);
                        }
                    }


                    // Iterasi melalui setiap produk dan setiap komponen
                    foreach ($produkCollection as $getProduk) {
                            // Buat objek Komponen_Produk_Terjual
                            $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                                'produk_terjual_id' => $produk_terjual->id,
                                'kode_produk' => $getProduk['produk']->kode, 
                                'nama_produk' => $getProduk['produk']->nama, 
                                'tipe_produk' => $getProduk['produk']->tipe_produk, 
                                'kondisi' => $getProduk['kondisi'], 
                                'deskripsi' => $getProduk['produk']->deskripsi, 
                                'jumlah' => $getProduk['jumlah'], 
                                'harga_satuan' => 0,
                                'harga_total' => 0
                            ]);

                            if (!$komponen_produk_terjual) {
                                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data komponen produk terjual');
                            }

                            // if ($lokasi->tipe_lokasi == 1) {
                            //     $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                            //         ->where('kode_produk', $komponen_produk_terjual->kode_produk)
                            //         ->where('kondisi_id', $komponen_produk_terjual->kondisi)
                            //         ->first();

                            //     if ($stok) {
                            //         $stok->jumlah += intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah);
                            //         $stok->update();
                            //     }
                            // }
                        // }
                    }

                    // Pengurangan inven outlet
                    // $stok = InventoryOutlet::where('lokasi_id', $lokasi->id)
                    //     ->where('kode_produk', $produk_terjual->produk->kode)
                    //     ->first();

                    // if (!$stok) {
                    //     return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                    // }

                    // $stok->jumlah -= intval($produk_terjual->jumlah);
                    // $stok->save();
                }
            } elseif ($cekgfttrd == 'TRD') {
                $kondisi_key = 'kondisitradproduk_' . $i;
                $jumlah_key = 'jumlahtradproduk_' . $i;

                if (isset($data[$kondisi_key]) && is_array($data[$kondisi_key])) {
                    for ($index = 0; $index < count($data[$kondisi_key]); $index++) {
                        $kondisi = Kondisi::where('nama', $data[$kondisi_key][$index])->value('id');
                        $jumlah = $data[$jumlah_key][$index];

                        foreach ($getProdukJual->komponen as $komponen) {
                            $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                                'produk_terjual_id' => $produk_terjual->id,
                                'kode_produk' => $komponen->kode_produk,
                                'nama_produk' => $komponen->nama_produk,
                                'tipe_produk' => $komponen->tipe_produk,
                                'kondisi' => $kondisi,
                                'deskripsi' => $komponen->deskripsi,
                                'jumlah' => $jumlah,
                                'harga_satuan' => $komponen->harga_satuan,
                                'harga_total' => $komponen->harga_total
                            ]);

                            if (!$komponen_produk_terjual) {
                                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data komponen produk terjual');
                            }
                        }
                    }
                }

                //pengurangan inven outlet
                // $stok = InventoryOutlet::where('lokasi_id', $lokasi->id)
                //                     ->where('kode_produk', $produk_terjual->produk->kode)
                //                     ->first();
                //         // dd($stok);
                    
                // if (!$stok) {
                //     return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                // }

                // $stok->jumlah = intval($stok->jumlah) - intval($produk_terjual->jumlah);
                // $stok->save();
            }
        }

        
        return redirect(route('penjualan.index'))->with('success', 'Data tersimpan');
    }

    public function show_outletgalery($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasiog', $mutasis->no_mutasi)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Terjual::all();
        // dd($produkjuals);
        $selectedGFTKomponen = [];
        $perPendapatan = [];
        
        foreach ($mutasis->produkMutasiOutlet as $produk) {
            // dd($produk);
            $selectedGFTKomponen = [];
            
            foreach ($produkjuals as $index => $pj) {
                // dd($produkjuals);
                if($pj->produk && $produk->produk->kode)
                {
                    $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_mutasiog ==  $produk->no_mutasiog && $pj->jenis != 'TAMBAHAN');
                
                if ($isSelectedGFT) {
                    foreach ($pj->komponen as $komponen) {
                        if ($pj->id == $komponen->produk_terjual_id) {
                            foreach ($kondisis as $kondisi) {
                                if ($kondisi->id == $komponen->kondisi) {
                                    $selectedGFTKomponen[$komponen->produk_terjual_id][] = [
                                        'kode' => $komponen->kode_produk,
                                        'nama' => $komponen->nama_produk,
                                        'kondisi' => $kondisi->nama,
                                        'jumlah' => $komponen->jumlah,
                                        'produk' => $komponen->produk_terjual_id,
                                    ];
                                }
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

        // dd($perPendapatan);
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasioutlet.show', compact('perPendapatan','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function update_outletgalery(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'jumlah_diterima' => 'required',
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $req->except(['_token', '_method']);
        // dd($data);
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = Produk_Terjual::with('komponen')->where('id', $data['nama_produk'][$i])->first();
            
            $produkmutasi = Produk_Terjual::where('no_mutasiog', $req->no_mutasi)
                                        ->where('id', $data['nama_produk'][$i])
                                        ->first();
            // dd($produkmutasi);

            if ($produkmutasi) {
                $produkmutasi->update([
                    'jumlah_diterima' => $data['jumlah_diterima'][$i],
                ]);
            } else {
                return redirect()->back()->withInput()->with('fail', 'Produk Mutasi tidak ditemukan');
            }

            $cekgfttrd = substr($getProdukJual->produk->kode, 0, 3);

            if ($cekgfttrd == 'GFT') {
                $kode_key = 'kodegiftproduk_' . $i;
                $komponen_key = 'komponengiftproduk_' . $i;
                $kondisi_key = 'kondisigiftproduk_' . $i;
                $jumlah_key = 'jumlahgiftproduk_' . $i;
                // dd($kode_key);

                if (isset($data[$komponen_key]) && is_array($data[$komponen_key])) {
                    $cekcount = count($data[$komponen_key]);
                    $produkCollection = collect();

                    // Membuat koleksi produk dari kode produk yang diberikan
                    foreach ($data[$kode_key] as $index => $kodeProduk) {
                        $produk = Produk::where('kode', $kodeProduk)->first();
                        if ($produk) {
                            $produkCollection->push([
                                'produk' => $produk,
                                'kondisi' => isset($data[$kondisi_key][$index]) ? Kondisi::where('nama', $data[$kondisi_key][$index])->value('id') : null,
                                'jumlah' => isset($data[$jumlah_key][$index]) ? $data[$jumlah_key][$index] : 0,
                            ]);
                        }
                    }


                    // Iterasi melalui setiap produk dan setiap komponen
                    foreach ($produkCollection as $getProduk) {

                            // if ($lokasi->tipe_lokasi == 1) {
                                $stok = InventoryGallery::where('lokasi_id', $req->penerima)
                                    ->where('kode_produk', $getProduk['produk']->kode)
                                    ->where('kondisi_id', $getProduk['kondisi'])
                                    ->first();

                                if ($stok) {
                                    $stok->jumlah += intval($getProduk['jumlah']) * intval($data['jumlah_diterima'][$i]);
                                    $stok->update();
                                }
                            // }
                        // }
                    }

                    // Pengurangan inven outlet
                    // if ($lokasi->tipe_lokasi == 2) {
                    //     $stok = InventoryOutlet::where('lokasi_id', $lokasi->id)
                    //         ->where('kode_produk', $produk_terjual->produk->kode)
                    //         ->first();

                    //     if (!$stok) {
                    //         return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                    //     }

                    //     $stok->jumlah -= intval($produk_terjual->jumlah);
                    //     $stok->save();
                    // }
                }
            }elseif ($cekgfttrd == 'TRD') {
                $kondisi_key = 'kondisitradproduk_' . $i;
                $jumlah_key = 'jumlahtradproduk_' . $i;

                if (isset($data[$kondisi_key]) && is_array($data[$kondisi_key])) {
                    for ($index = 0; $index < count($data[$kondisi_key]); $index++) {
                        $kondisi = Kondisi::where('nama', $data[$kondisi_key][$index])->value('id');
                        $jumlah = $data[$jumlah_key][$index];

                        foreach ($getProdukJual->komponen as $komponen) {
                            // if($lokasi->tipe_lokasi == 1){
                                $stok = InventoryGallery::where('lokasi_id', $req->penerima)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $kondisi)->first();
                                if ($stok) {
                                    $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($data['jumlah_diterima'][$i]));
                                    $stok->update();
                                }
                            // }

                            // $stok = $lokasi->tipe_lokasi == 1
                            //     ? InventoryGallery::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen_produk_terjual->kode_produk)->where('kondisi_id', $komponen_produk_terjual->kondisi)->first()
                            //     : InventoryOutlet::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen_produk_terjual->kode_produk)->where('kondisi_id', $komponen_produk_terjual->kondisi)->first();

                            // if ($stok) {
                            //     $stok->jumlah = intval($stok->jumlah) + (intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah));
                            //     $stok->update();
                            // }
                        }
                    }
                }

                //pengurangan inven outlet
                // if($lokasi->tipe_lokasi == 2){
                //     //pengurangan inven outlet
                //     $stok = InventoryOutlet::where('lokasi_id', $lokasi->id)
                //                         ->where('kode_produk', $produk_terjual->produk->kode)
                //                         ->first();
                //             // dd($stok);
                        
                //     if (!$stok) {
                //         return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                //     }

                //     $stok->jumlah = intval($stok->jumlah) - intval($produk_terjual->jumlah);
                //     $stok->save();
                // }
            }


            // $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
        }
        
        return redirect(route('mutasioutlet.index'))->with('success', 'Data Berhasil Disimpan');
    }

    public function payment_outletgalery($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasiog', $mutasis->no_mutasi)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Terjual::all();
        // dd($produkjuals);
        $selectedGFTKomponen = [];
        $perPendapatan = [];
        
        foreach ($mutasis->produkMutasiOutlet as $produk) {
            // dd($produk);
            $selectedGFTKomponen = [];
            
            foreach ($produkjuals as $index => $pj) {
                // dd($produkjuals);
                if($pj->produk && $produk->produk->kode)
                {
                    $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_mutasiog ==  $produk->no_mutasiog && $pj->jenis != 'TAMBAHAN');
                
                if ($isSelectedGFT) {
                    foreach ($pj->komponen as $komponen) {
                        if ($pj->id == $komponen->produk_terjual_id) {
                            foreach ($kondisis as $kondisi) {
                                if ($kondisi->id == $komponen->kondisi) {
                                    $selectedGFTKomponen[$komponen->produk_terjual_id][] = [
                                        'kode' => $komponen->kode_produk,
                                        'nama' => $komponen->nama_produk,
                                        'kondisi' => $kondisi->nama,
                                        'jumlah' => $komponen->jumlah,
                                        'produk' => $komponen->produk_terjual_id,
                                    ];
                                }
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

        // dd($perPendapatan);
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        $Invoice = Pembayaran::where('mutasi_id', $mutasis->id)->where('no_invoice_bayar', 'LIKE', 'BOG%')->latest()->first();

        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 000;
        }

        $pembayarans = Pembayaran::with('rekening')->where('mutasi_id', $mutasis->id)->where('no_invoice_bayar', 'LIKE', 'BOG%')->orderBy('created_at', 'desc')->get();
        $totalbiaya = 0;
        foreach($pembayarans as $tagihanbayar){
            $totalbiaya += $tagihanbayar->nominal;
        }
        $totaltagihan = $mutasis->total_biaya - $totalbiaya;

        // dd($totaltagihan);
        return view('mutasioutlet.payment', compact('perPendapatan','totaltagihan','cekInvoice','pembayarans','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function view_outletgalery($mutasi){
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasiog', $mutasis->no_mutasi)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Terjual::all();
        // dd($produkjuals);
        $selectedGFTKomponen = [];
        $perPendapatan = [];
        
        foreach ($mutasis->produkMutasiOutlet as $produk) {
            // dd($produk);
            $selectedGFTKomponen = [];
            
            foreach ($produkjuals as $index => $pj) {
                // dd($produkjuals);
                if($pj->produk && $produk->produk->kode)
                {
                    $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_mutasiog ==  $produk->no_mutasiog && $pj->jenis != 'TAMBAHAN');
                
                if ($isSelectedGFT) {
                    foreach ($pj->komponen as $komponen) {
                        if ($pj->id == $komponen->produk_terjual_id) {
                            foreach ($kondisis as $kondisi) {
                                if ($kondisi->id == $komponen->kondisi) {
                                    $selectedGFTKomponen[$komponen->produk_terjual_id][] = [
                                        'kode' => $komponen->kode_produk,
                                        'nama' => $komponen->nama_produk,
                                        'kondisi' => $kondisi->nama,
                                        'jumlah' => $komponen->jumlah,
                                        'produk' => $komponen->produk_terjual_id,
                                    ];
                                }
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

        // dd($perPendapatan);
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasioutlet.view', compact('perPendapatan','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function index_ghgalery(Request $req)
    {
        $query = Mutasi::where('no_mutasi', 'like', 'MGG%')->orderBy('created_at', 'desc');

        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }
        $mutasis = $query->get();

        // $mutasis = Mutasi::where('no_mutasi', 'like', 'MGG%')->orderBy('created_at', 'desc')->get();
        return view('mutasighgalery.index', compact('mutasis'));
    }

    public function index_galerygalery(Request $req)
    {
        $query = Mutasi::where('no_mutasi', 'like', 'MGA%')->orderBy('created_at', 'desc');

        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }
        $mutasis = $query->get();

        // $mutasis = Mutasi::where('no_mutasi', 'like', 'MGG%')->orderBy('created_at', 'desc')->get();
        return view('mutasigalerygalery.index', compact('mutasis'));
    }

    public function create_galerygalery()
    {
        $roles = Auth::user()->roles()->value('name');
        $user = Auth::user()->value('id');
        $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
        // dd($karyawans);
        $customers = Customer::where('lokasi_id', $lokasi)->get();
        $lokasipengirim = Lokasi::where('tipe_lokasi', 1)->get();
        $lokasipenerima = Lokasi::where('tipe_lokasi', 1)->get();
        $ongkirs = Ongkir::get();
        $karyawans = Karyawan::where('lokasi_id', $lokasi)->get();
        $promos = Promo::where(function ($query) use ($lokasi) {
            $query->where('lokasi_id', $lokasi)
                ->orWhere('lokasi_id', 'Semua');
        })->get();
        $produks = InventoryGreenhouse::all();
        // dd($produks);
        $bankpens = Rekening::get();
        $Invoice = Mutasi::where('no_mutasi', 'LIKE', 'MGA%')->latest()->first();
        // dd($bankpens);
        if ($Invoice != null) {
            $substring = substr($Invoice->no_mutasi, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 0;
        }
        // dd($cekInvoice);
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

        return view('mutasigalerygalery.create', compact('customers', 'lokasipengirim','lokasipenerima',  'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
    }

    public function store_galerygalery(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'pengirim' => 'required',
            'penerima' => 'required',
            'no_mutasi' => 'required',
            'tanggal_kirim' =>'required',
            'tanggal_diterima' => 'required',
            'status' => 'required',
            'pilih_pengiriman' => 'required',
            'biaya_pengiriman' =>'required',
            'total_biaya' => 'required'
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $req->except(['_token', '_method', 'bukti_file', 'bukti', 'status_bayar']);
        // dd($data);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_mutasi', $fileName, 'public');
            // dd($filePath);
            $data['bukti'] = $filePath;
        }

        $allStockAvailable = true;
        
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = InventoryGallery::where('id', $data['nama_produk'][$i])->first();
            $stok = null;
            // dd($getProdukJual);
            $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                                    ->where('kode_produk', $getProdukJual->kode_produk)
                                    ->where('kondisi_id', $getProdukJual->kondisi_id)
                                    ->first();
            // dd($stok);

            if (!$stok || $stok->jumlah < intval($req->jumlahproduk[$i]) * intval($req->jml_produk) || $stok->jumlah < $stok->min_stok) {
                $allStockAvailable = false;
                break;
            }
        }

        // dd($stok);

        if (!$allStockAvailable) {
            return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory atau stok tidak mencukupi');
        }

        $data['pembuat_id'] = Auth::user()->id;
        $data['tanggal_pembuat'] = now();
        
        $mutasi = Mutasi::create($data);

        $lokasi = Lokasi::where('id', $req->pengirim)->first();

        if ($mutasi) {
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $getProdukJual = InventoryGallery::where('id', $data['nama_produk'][$i])->first();
                $getProduk = Produk::where('kode', $getProdukJual->kode_produk)->first();
                // dd($getProduk);
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProduk->id,
                    'no_mutasigag' => $mutasi->no_mutasi,
                    'jumlah' => $data['jumlah_dikirim'][$i],
                ]);

                if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                    'produk_terjual_id' => $produk_terjual->id,
                    'kode_produk' => $getProduk->kode,
                    'nama_produk' => $getProduk->nama,
                    'tipe_produk' => $getProduk->tipe_produk,
                    'kondisi' => $getProdukJual->kondisi_id,
                    'deskripsi' => $getProduk->deskripsi,
                    'jumlah' => $data['jumlah_dikirim'][$i],
                    'harga_satuan' => 0,
                    'harga_total' => 0
                ]);
                if (!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

                if($req->status == 'DIKONFIRMASI'){
                    $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                                    ->where('kode_produk', $getProdukJual->kode_produk)
                                    ->where('kondisi_id', $getProdukJual->kondisi_id)
                                    ->first();
                    // dd($stok);
        
                    if ($stok) {
                        $stok->jumlah -= intval($data['jumlah_dikirim'][$i]);
                        $stok->update();
                    }
                }
                
            }
            return redirect(route('mutasigalerygalery.index'))->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function show_galerygalery($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigag', $mutasis->no_mutasi)->get();
        // dd($produks);
        foreach($produks as $produk)
        {
            // dd($produk);
        }
        
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = InventoryGreenHouse::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasigalerygalery.show', compact('produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function update_galerygalery(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'jumlah_diterima' => 'required',
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $req->except(['_token', '_method']);
        // dd($data);
        $lokasi = Lokasi::where('id', $req->penerima)->first();
        // dd($lokasi);
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            // $getProdukJual = InventoryGreenHouse::where('id', $data['nama_produk'][$i])->first();
            // dd($getProdukJual);
            // $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            
            $produkmutasi = Produk_Terjual::with('komponen')->where('no_mutasigag', $req->no_mutasi)
                                        ->where('id', $data['nama_produk'][$i])
                                        ->first();
            // dd($produkmutasi);
            foreach($produkmutasi->komponen as $komponen)
            {
                // dd($lokasi->tipe_lokasi);
                $stok = InventoryGallery::where('lokasi_id', $req->penerima)
                                        ->where('kode_produk', $komponen->kode_produk)
                                        ->where('kondisi_id', $komponen->kondisi)
                                        ->first();
                // dd($stok);
                if ($stok) {
                    $stok->jumlah += intval($data['jumlah_diterima'][$i]);
                    $stok->update();
                }
            }

            if ($produkmutasi) {
                $produkmutasi->update([
                    'jumlah_diterima' => $data['jumlah_diterima'][$i],
                ]);
            } else {
                return redirect()->back()->withInput()->with('fail', 'Produk Mutasi tidak ditemukan');
            }
        }
        
        return redirect(route('mutasigalerygalery.index'))->with('success', 'Data Berhasil Disimpan');
    }

    public function payment_galerygalery($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigag', $mutasis->no_mutasi)->get();
        // dd($produks);
        foreach($produks as $produk)
        {
            // dd($produk);
        }
        
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = InventoryGreenHouse::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();
        $Invoice = Pembayaran::where('mutasi_id', $mutasis->id)->where('no_invoice_bayar', 'LIKE', 'BGA%')->latest()->first();

        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 000;
        }

        $pembayarans = Pembayaran::with('rekening')->where('mutasi_id', $mutasis->id)->where('no_invoice_bayar', 'LIKE', 'BGA%')->orderBy('created_at', 'desc')->get();
        $totalbiaya = 0;
        foreach($pembayarans as $tagihanbayar){
            $totalbiaya += $tagihanbayar->nominal;
        }
        $totaltagihan = $mutasis->total_biaya - $totalbiaya;
        // dd($mutasis);
        return view('mutasigalerygalery.payment', compact('totaltagihan','pembayarans','cekInvoice','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function create_ghgalery()
    {
        // $roles = Auth::user()->roles()->value('name');
        $user = Auth::user()->value('id');
        $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
        // dd($karyawans);
        // $customers = Customer::where('lokasi_id', $lokasi)->get();
        $lokasipengirim = Lokasi::where('tipe_lokasi', 3)->get();
        $lokasipenerima = Lokasi::where('tipe_lokasi', 1)->get();
        $ongkirs = Ongkir::get();
        $karyawans = Karyawan::where('lokasi_id', $lokasi)->get();
        // $promos = Promo::where(function ($query) use ($lokasi) {
        //     $query->where('lokasi_id', $lokasi)
        //         ->orWhere('lokasi_id', 'Semua');
        // })->get();
        $produks = InventoryGreenhouse::all();
        // dd($produks);
        $bankpens = Rekening::get();
        $Invoice = Mutasi::where('no_mutasi', 'LIKE', 'MGG%')->latest()->first();
        // dd($bankpens);
        if ($Invoice != null) {
            $substring = substr($Invoice->no_mutasi, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 0;
        }
        // dd($cekInvoice);
        // $InvoiceBayar = Pembayaran::latest()->first();
        // // dd($Invoice);
        // if ($InvoiceBayar != null) {
        //     $substringBayar = substr($InvoiceBayar->no_invoice_bayar, 11);
        //     $cekInvoiceBayar = substr($substringBayar, 0, 3);
        //     // dd($cekInvoice);
        // } else {
        //     $cekInvoiceBayar = 0;
        // }
            // $komponen = Kondisi::with('komponen')->get();
            // dd($komponen);
        // $kondisis = Kondisi::all();
        // $invoices = Penjualan::get();

        // return view('mutasighgalery.create', compact('customers', 'lokasipengirim','lokasipenerima',  'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
        return view('mutasighgalery.create', compact('lokasipenerima', 'lokasipengirim', 'produks', 'Invoice', 'cekInvoice', 'bankpens', 'ongkirs', 'karyawans'));
    }

    public function view_galerygalery($mutasi){
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigag', $mutasis->no_mutasi)->get();
        // dd($produks);
        foreach($produks as $produk)
        {
            // dd($produk);
        }
        
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = InventoryGreenHouse::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasigalerygalery.view', compact('produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function store_ghgalery(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'pengirim' => 'required',
            'penerima' => 'required',
            'no_mutasi' => 'required',
            'tanggal_kirim' =>'required',
            'tanggal_diterima' => 'required',
            'status' => 'required',
            'pilih_pengiriman' => 'required',
            'biaya_pengiriman' =>'required',
            'total_biaya' => 'required'
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $req->except(['_token', '_method', 'bukti_file', 'bukti', 'status_bayar']);
        // dd($data);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_mutasi', $fileName, 'public');
            // dd($filePath);
            $data['bukti'] = $filePath;
        }

        $allStockAvailable = true;
        
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = InventoryGreenHouse::where('id', $data['nama_produk'][$i])->first();
            $stok = null;
            // dd($getProdukJual);
            $stok = InventoryGreenHouse::where('lokasi_id', $req->pengirim)
                                    ->where('kode_produk', $getProdukJual->kode_produk)
                                    ->where('kondisi_id', $getProdukJual->kondisi_id)
                                    ->first();
            // dd($stok);

            if (!$stok || $stok->jumlah < intval($req->jumlahproduk[$i]) * intval($req->jml_produk) || $stok->jumlah < $stok->min_stok) {
                $allStockAvailable = false;
                break;
            }
        }

        // dd($stok);

        if (!$allStockAvailable) {
            return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory atau stok tidak mencukupi');
        }

        $data['pembuat_id'] = Auth::user()->id;
        $data['tanggal_pembuat'] = now();
        
        $mutasi = Mutasi::create($data);

        $lokasi = Lokasi::where('id', $req->pengirim)->first();

        if ($mutasi) {
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $getProdukJual = InventoryGreenHouse::where('id', $data['nama_produk'][$i])->first();
                $getProduk = Produk::where('kode', $getProdukJual->kode_produk)->first();
                // dd($getProduk);
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProduk->id,
                    'no_mutasigg' => $mutasi->no_mutasi,
                    'jumlah' => $data['jumlah_dikirim'][$i],
                ]);

                if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                    'produk_terjual_id' => $produk_terjual->id,
                    'kode_produk' => $getProduk->kode,
                    'nama_produk' => $getProduk->nama,
                    'tipe_produk' => $getProduk->tipe_produk,
                    'kondisi' => $getProdukJual->kondisi_id,
                    'deskripsi' => $getProduk->deskripsi,
                    'jumlah' => $data['jumlah_dikirim'][$i],
                    'harga_satuan' => 0,
                    'harga_total' => 0
                ]);
                if (!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

                $stok = InventoryGreenHouse::where('lokasi_id', $req->pengirim)
                                    ->where('kode_produk', $getProdukJual->kode_produk)
                                    ->where('kondisi_id', $getProdukJual->kondisi_id)
                                    ->first();
                // dd($stok);
    
                if ($stok) {
                    $stok->jumlah -= intval($data['jumlah_dikirim'][$i]);
                    $stok->update();
                }
            }
            return redirect(route('mutasighgalery.index'))->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    public function show_ghgalery($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigg', $mutasis->no_mutasi)->get();
        // dd($produks);
        foreach($produks as $produk)
        {
            // dd($produk);
        }
        
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = InventoryGreenHouse::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasighgalery.show', compact('produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function update_ghgalery(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'jumlah_diterima' => 'required',
        ]);

        // dd($validator);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $data = $req->except(['_token', '_method']);
        // dd($data);
        $lokasi = Lokasi::where('id', $req->penerima)->first();
        // dd($lokasi);
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            // $getProdukJual = InventoryGreenHouse::where('id', $data['nama_produk'][$i])->first();
            // dd($getProdukJual);
            // $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            
            $produkmutasi = Produk_Terjual::with('komponen')->where('no_mutasigg', $req->no_mutasi)
                                        ->where('id', $data['nama_produk'][$i])
                                        ->first();
            // dd($produkmutasi->komponen);
            foreach($produkmutasi->komponen as $komponen)
            {
                // dd($lokasi->tipe_lokasi);
                $stok = InventoryGallery::where('lokasi_id', $req->penerima)
                                        ->where('kode_produk', $komponen->kode_produk)
                                        ->where('kondisi_id', $komponen->kondisi)
                                        ->first();
                // dd($stok);
                if ($stok) {
                    $stok->jumlah += intval($data['jumlah_diterima'][$i]);
                    $stok->update();
                }
            }

            if ($produkmutasi) {
                $produkmutasi->update([
                    'jumlah_diterima' => $data['jumlah_diterima'][$i],
                ]);
            } else {
                return redirect()->back()->withInput()->with('fail', 'Produk Mutasi tidak ditemukan');
            }
        }
        
        return redirect(route('mutasighgalery.index'))->with('success', 'Data Berhasil Disimpan');
    }

    public function view_ghgalery($mutasi){
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigg', $mutasis->no_mutasi)->get();
        // dd($produks);
        foreach($produks as $produk)
        {
            // dd($produk);
        }
        
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = InventoryGreenHouse::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasighgalery.view', compact('produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function payment_ghgalery($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigg', $mutasis->no_mutasi)->get();
        // dd($produks);
        foreach($produks as $produk)
        {
            // dd($produk);
        }
        
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = InventoryGreenHouse::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();
        $Invoice = Pembayaran::where('mutasi_id', $mutasis->id)->where('no_invoice_bayar', 'LIKE', 'BGG%')->latest()->first();

        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 000;
        }

        $pembayarans = Pembayaran::with('rekening')->where('mutasi_id', $mutasis->id)->where('no_invoice_bayar', 'LIKE', 'BGG%')->orderBy('created_at', 'desc')->get();
        $totalbiaya = 0;
        foreach($pembayarans as $tagihanbayar){
            $totalbiaya += $tagihanbayar->nominal;
        }
        $totaltagihan = $mutasis->total_biaya - $totalbiaya;
        // dd($mutasis);
        return view('mutasighgalery.payment', compact('totaltagihan','pembayarans','cekInvoice','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function audit_GO($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigo', $mutasis->no_mutasi)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        $perangkai = Karyawan::where('jabatan', 'Perangkai')->get();
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Jual::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasigalery.audit', compact('perangkai','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function audit_GOUpdate(Request $req)
    {
        // dd($req);

        $mutasis = $req->input('mutasiGO');
        $data = $req->except(['_method', '_token', 'jumlah_dikirim', 'jumlah_diterima', 'mutasiGO', 'nama_produk', 'kode_produk']);
        // dd($data);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_mutasi', $fileName, 'public');
            // dd($filePath);
            $data['bukti'] = $filePath;
        }

        //update data ttd
        $user = Auth::user();
        $jabatan = Karyawan::where('user_id', $user->id)->first();
        $jabatanpegawai = $jabatan->jabatan;
        $mutasipenjualan = Mutasi::where('id', $mutasis)->first();

        if($mutasipenjualan->status == 'DIKONFIRMASI' && $jabatanpegawai == 'auditor'){
            $data['diperiksa_id'] = Auth::user()->id;
            $data['tanggal_diperiksa'] = now();
        }elseif($mutasipenjualan->status == 'DIKONFIRMASI' && $jabatanpegawai == 'finance'){
            $data['dibukukan_id'] = Auth::user()->id;
            $data['tanggal_dibukukan'] = now();
        }elseif($mutasipenjualan->status != 'DIKONFIRMASI' && $jabatanpegawai == 'admin' || $jabatanpegawai == 'kasir'){
            $data['pembuat_id'] = Auth::user()->id;
            $data['tanggal_pembuat'] = now();
        }

        $update = Mutasi::where('id', $mutasis)->update($data);

        if($req->status == 'DIBATALKAN'){
            return redirect(route('mutasigalery.index'))->with('success', 'Berhasil Mengupdate Data');
        }

        //hapus komponen agar bisa di create ulang
        $produkterjualmutasi = Produk_Terjual::whereIn('id', $req->nama_produk)->get();
        $arrayCombined =  $produkterjualmutasi->pluck('id')->toArray();
        $cek = Produk_Terjual::whereNotIn('id', $arrayCombined)->where('no_mutasigo', $req->no_mutasi)->get();
        $ceken = $cek->pluck('id')->toArray();

        if (!empty($ceken)) {
            Produk_Terjual::whereIn('id', $ceken)->forceDelete();
            Komponen_Produk_Terjual::whereIn('produk_terjual_id', $ceken)->forceDelete();
        }
        Komponen_Produk_Terjual::whereIn('produk_terjual_id', $arrayCombined)->forceDelete();

        for ($i = 0; $i < count($req->nama_produk); $i++) {
            $getProdukJual = Produk_Terjual::with('komponen')->where('id', $req->nama_produk[$i])->first();
            // dd($getProdukJual);
            $getProduk = Produk_Jual::with('komponen')->where('id', $req->kode_produk[$i])->first();
            // dd($getProdukJual);
            $produk_terjual = Produk_Terjual::where('id', $req->nama_produk[$i])->update([
                'produk_jual_id' => $req->kode_produk[$i],
                'no_mutasigo' => $req->no_mutasi,
                'jumlah' => $req->jumlah_dikirim[$i],
            ]);

            if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            foreach ($getProduk->komponen as $komponen) {
                $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                    'produk_terjual_id' => $req->nama_produk[$i],
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
        return redirect(route('mutasigalery.index'))->with('success', 'Data Berhasil Disimpan');

        if($update){
            return redirect()->back()->with('success', 'Berhasil Mengupdate Data');
        }else{
            return redirect()->back()->with('fail', 'Gagal Mengupdate Data');
        }

    }

    public function audit_OG($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasiog', $mutasis->no_mutasi)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Terjual::all();
        // dd($produkjuals);
        $selectedGFTKomponen = [];
        $perPendapatan = [];
        
        foreach ($mutasis->produkMutasiOutlet as $produk) {
            // dd($produk);
            $selectedGFTKomponen = [];
            
            foreach ($produkjuals as $index => $pj) {
                // dd($produkjuals);
                if($pj->produk && $produk->produk->kode)
                {
                    $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_mutasiog ==  $produk->no_mutasiog && $pj->jenis != 'TAMBAHAN');
                
                if ($isSelectedGFT) {
                    foreach ($pj->komponen as $komponen) {
                        if ($pj->id == $komponen->produk_terjual_id) {
                            foreach ($kondisis as $kondisi) {
                                if ($kondisi->id == $komponen->kondisi) {
                                    $selectedGFTKomponen[$komponen->produk_terjual_id][] = [
                                        'kode' => $komponen->kode_produk,
                                        'nama' => $komponen->nama_produk,
                                        'kondisi' => $kondisi->nama,
                                        'jumlah' => $komponen->jumlah,
                                        'produk' => $komponen->produk_terjual_id,
                                    ];
                                }
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

        // dd($perPendapatan);
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasioutlet.audit', compact('perPendapatan','produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function audit_OGUpdate(Request $req)
    {
        // dd($req);
        $mutasis = $req->input('mutasiOG');

        $allkeys = array_keys($req->all());

        $komponens = ['_token', '_method','nama_produk', 'kodegiftproduk', 'komponengiftproduk', 'kondisigiftproduk', 'jumlahgiftproduk', 'kondisitradproduk', 'jumlahtradproduk', 'jumlah_dikirim', 'jumlah_diterima', 'mutasiOG'];

        $filter = array_filter($allkeys, function($key) use ($komponens){
            foreach($komponens as $komponen){
                if(strpos($key, $komponen)  === 0){
                    return true;
                }
            }
        });
        $data = $req->except($filter);
        // dd($data);

        $data['dibukukan_id'] = Auth::user()->id;
        $data['tanggal_dibukukan'] = now();
        // dd($data);

        $update = Mutasi::where('id', $mutasis)->update($data);
        if($update){
            return redirect()->back()->with('success', 'Berhasil Mengupdate Data');
        }else{
            return redirect()->back()->with('fail', 'Gagal Mengupdate Data');
        }
    }

    public function audit_GAG($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigag', $mutasis->no_mutasi)->get();
        // dd($produks);
        foreach($produks as $produk)
        {
            // dd($produk);
        }
        
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = InventoryGreenHouse::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasigalerygalery.audit', compact('produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function audit_GAGUpdate(Request $req)
    {
        // dd($req);
        $mutasis = $req->input('mutasiGAG');
        $data = $req->except(['_method', '_token', 'nama_produk', 'jumlah_dikirim', 'jumlah_diterima', 'mutasiGAG']);
        // dd($data);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_mutasi', $fileName, 'public');
            // dd($filePath);
            $data['bukti'] = $filePath;
        }

        //update data ttd
        $user = Auth::user();
        $jabatan = Karyawan::where('user_id', $user->id)->first();
        $jabatanpegawai = $jabatan->jabatan;
        $mutasipenjualan = Mutasi::where('id', $mutasis)->first();

        if($mutasipenjualan->status == 'DIKONFIRMASI' && $jabatanpegawai == 'auditor'){
            $data['diperiksa_id'] = Auth::user()->id;
            $data['tanggal_diperiksa'] = now();
        }elseif($mutasipenjualan->status == 'DIKONFIRMASI' && $jabatanpegawai == 'finance'){
            $data['dibukukan_id'] = Auth::user()->id;
            $data['tanggal_dibukukan'] = now();
        }elseif($mutasipenjualan->status != 'DIKONFIRMASI' && $jabatanpegawai == 'admin' || $jabatanpegawai == 'kasir'){
            $data['pembuat_id'] = Auth::user()->id;
            $data['tanggal_pembuat'] = now();
        }

        $update = Mutasi::where('id', $mutasis)->update($data);

        if($req->status == 'DIBATALKAN'){
            return redirect(route('mutasigalerygalery.index'))->with('success', 'Berhasil Mengupdate Data');
        }

        //hapus komponen agar bisa di create ulang
        $produkterjualmutasi = Produk_Terjual::whereIn('id', $req->nama_produk)->get();
        $arrayCombined =  $produkterjualmutasi->pluck('id')->toArray();
        $cek = Produk_Terjual::whereNotIn('id', $arrayCombined)->where('no_mutasigag', $req->no_mutasi)->get();
        $ceken = $cek->pluck('id')->toArray();

        if (!empty($ceken)) {
            Produk_Terjual::whereIn('id', $ceken)->forceDelete();
            Komponen_Produk_Terjual::whereIn('produk_terjual_id', $ceken)->forceDelete();
        }
        Komponen_Produk_Terjual::whereIn('produk_terjual_id', $arrayCombined)->forceDelete();

        for ($i = 0; $i < count($req->kode_produk); $i++) {
            $getProdukJual = InventoryGallery::where('id', $req->kode_produk[$i])->first();
            $getProduk = Produk::where('kode', $getProdukJual->kode_produk)->first();
            // dd($getProduk);
            $produk_terjual = Produk_Terjual::where('id', $req->nama_produk[$i])->update([
                'produk_jual_id' => $getProduk->id,
                'no_mutasigag' => $req->no_mutasi,
                'jumlah' => $data['jumlah_dikirim'][$i],
            ]);

            if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                'produk_terjual_id' => $getProduk->id,
                'kode_produk' => $getProduk->kode,
                'nama_produk' => $getProduk->nama,
                'tipe_produk' => $getProduk->tipe_produk,
                'kondisi' => $getProdukJual->kondisi_id,
                'deskripsi' => $getProduk->deskripsi,
                'jumlah' => $data['jumlah_dikirim'][$i],
                'harga_satuan' => 0,
                'harga_total' => 0
            ]);
            if (!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            if($req->status == 'DIKONFIRMASI'){
                $stok = InventoryGallery::where('lokasi_id', $req->pengirim)
                                ->where('kode_produk', $getProdukJual->kode_produk)
                                ->where('kondisi_id', $getProdukJual->kondisi_id)
                                ->first();
                // dd($stok);
    
                if ($stok) {
                    $stok->jumlah -= intval($data['jumlah_dikirim'][$i]);
                    $stok->update();
                }
            }
            
        }

        if($update){
            return redirect()->back()->with('success', 'Berhasil Menyimpan Data');
        }else{
            return redirect()->back()->with('fail', 'Gagal Menyimpan Data');
        }
    }

    public function audit_GG($mutasi)
    {
        $lokasis = Lokasi::all();
        $mutasis = Mutasi::with('produkMutasi')->find($mutasi);
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasigg', $mutasis->no_mutasi)->get();
        // dd($produks);
        foreach($produks as $produk)
        {
            // dd($produk);
        }
        
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_mutasi', $mutasis->no_mutasi)->get();
        foreach($mutasis->produkMutasi as $produk)
        {
            $coba[] = $produk->id;
        }
        // dd($coba);
        // $produks = Produk_Jual::with('komponen.kondisi')->get();
        $kondisis = Kondisi::all();
        $produkjuals = InventoryGreenHouse::all();
        $bankpens = Rekening::all();
        $ongkirs = Ongkir::all();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();

        // dd($mutasis);
        return view('mutasighgalery.audit', compact('produkKomponens','produkjuals','ongkirs','bankpens','kondisis','produks','mutasis', 'lokasis'));
    }

    public function audit_GGUpdate(Request $req)
    {
        // dd($req);

        $mutasis = $req->input('mutasiGG');

        $allkeys = array_keys($req->all());
        $keys = ['_method', '_token', 'nama_produk', 'jumlah_dikirim', 'jumlah_diterima', 'mutasiGG'];
        $filter = array_filter($allkeys, function($key) use ($keys){
            foreach($keys as $ke){
                if(strpos($ke, $key) === 0){
                    return true;
                }
            }
        });

        $data = $req->except($filter);

        // dd($data);
        $data['dibukukan_id'] = Auth::user()->id;
        $data['tanggal_dibukukan'] = now();

        $update = Mutasi::where('id', $mutasis)->update($data);
        if($update){
            return redirect()->back()->with('success', 'Berhasil Mengupdate Data');
        }else{
            return redirect()->back()->with('fail', 'Gagal Menyimpan Data');
        }

    }


}
