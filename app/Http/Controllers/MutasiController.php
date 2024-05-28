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
        // dd($data);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_mutasi', $fileName, 'public');
            // dd($filePath);
            $data['bukti'] = $filePath;
        }
        
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

        foreach ($returs->produk_retur as $produk) {
            foreach ($produkjuals as $index => $pj) {
                if($pj->produk && $produk->produk->kode)
                {
                    $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_retur == $returs->no_retur && $pj->jenis != 'GANTI');
                    if ($isSelectedGFT) {
                        foreach ($pj->komponen as $komponen) {
                            foreach ($kondisis as $kondisi) {
                                if ($kondisi->id == $komponen->kondisi) {
                                    // Check if the komponen is already added
                                    $komponenExists = false;
                                    if (isset($selectedGFTKomponen[$komponen->produk_terjual_id])) {
                                        foreach ($selectedGFTKomponen[$komponen->produk_terjual_id] as $existingKomponen) {
                                            if ($existingKomponen['nama'] == $komponen->nama_produk &&
                                                $existingKomponen['kondisi'] == $kondisi->nama &&
                                                $existingKomponen['jumlah'] == $komponen->jumlah &&
                                                $existingKomponen['produk'] == $komponen->produk_terjual_id) {
                                                $komponenExists = true;
                                                break;
                                            }
                                        }
                                    }

                                    // Add komponen if it doesn't already exist
                                    if (!$komponenExists) {
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
        }

        if (!empty($selectedGFTKomponen)) {
            $perPendapatan += $selectedGFTKomponen;
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

        // cek produk inventory
        $allStockAvailable = true;

        if($lokasi->tipe_lokasi == 2){
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $getProdukJual = Produk_Terjual::with('komponen')->where('id', $data['nama_produk'][$i])->first();

                foreach ($getProdukJual->komponen as $komponen ) {
                    $stok = InventoryOutlet::where('lokasi_id', $req->pengirim)
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
            }
        }
        
        $mutasi = Mutasi::create($data);

        $cek = [];
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->id,
                'no_mutasiog' => $mutasi->no_mutasi,
                'jumlah' => $data['jumlah_dikirim'][$i]
            ]);
            
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
                    $getProduk = Produk::where('kode', $data[$kode_key])->first();
                    // dd($getProdukJual->komponen);
                    for ($index = 0; $index < count($data[$komponen_key]); $index++) {
                        $kondisi = isset($data[$kondisi_key][$index]) ? Kondisi::where('nama', $data[$kondisi_key][$index])->value('id') : null;
                        $jumlah = isset($data[$jumlah_key][$index]) ? $data[$jumlah_key][$index] : 0;

                        // Buat objek Komponen_Produk_Terjual
                        $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                            'produk_terjual_id' => $produk_terjual->id,
                            'kode_produk' => $getProduk->kode,
                            'nama_produk' => $getProduk->nama,
                            'tipe_produk' => $getProduk->tipe_produk,
                            'kondisi' => $kondisi,
                            'deskripsi' => $getProduk->deskripsi,
                            'jumlah' => $jumlah,
                            'harga_satuan' => 0,
                            'harga_total' => 0
                        ]);
                    


                        if (!$komponen_produk_terjual) {
                            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data komponen produk terjual');
                        }

                        $stok = $lokasi->tipe_lokasi == 1
                            ? InventoryGallery::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen_produk_terjual->kode_produk)->where('kondisi_id', $komponen_produk_terjual->kondisi)->first()
                            : InventoryOutlet::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen_produk_terjual->kode_produk)->where('kondisi_id', $komponen_produk_terjual->kondisi)->first();

                        if ($stok) {
                            $stok->jumlah = intval($stok->jumlah) + (intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah));
                            $stok->update();
                        }
                    }
                                            // dd($komponen_produk_terjual);
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

                            $stok = $lokasi->tipe_lokasi == 1
                                ? InventoryGallery::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen_produk_terjual->kode_produk)->where('kondisi_id', $komponen_produk_terjual->kondisi)->first()
                                : InventoryOutlet::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen_produk_terjual->kode_produk)->where('kondisi_id', $komponen_produk_terjual->kondisi)->first();

                            if ($stok) {
                                $stok->jumlah = intval($stok->jumlah) + (intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah));
                                $stok->update();
                            }
                        }
                    }
                }
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
            $selectedGFTKomponen = [];
            
            foreach ($produkjuals as $index => $pj) {
                // dd($produkjuals);
                if($pj->produk && $produk->produk->kode)
                {
                    $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_do ==  $deliveryOrder->no_do && $pj->jenis != 'TAMBAHAN');
                
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

            if (!empty($selectedGFTKomponen)) {
                $perPendapatan += $selectedGFTKomponen;
            }
        }

        // dd($pj->produk->kode);
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
            // $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            
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
        }
        
        return redirect(route('mutasioutlet.index'))->with('success', 'Data Berhasil Disimpan');
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
        $query = Mutasi::where('no_mutasi', 'like', 'MGAG%')->orderBy('created_at', 'desc');

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

    public function create_ghgalery()
    {
        // $roles = Auth::user()->roles()->value('name');
        // $user = Auth::user()->value('id');
        // $lokasi = Karyawan::where('user_id', $user)->value('lokasi_id');
        // dd($karyawans);
        // $customers = Customer::where('lokasi_id', $lokasi)->get();
        $lokasipengirim = Lokasi::where('tipe_lokasi', 3)->get();
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

        return view('mutasighgalery.create', compact('customers', 'lokasipengirim','lokasipenerima',  'karyawans', 'promos', 'produks', 'ongkirs', 'bankpens', 'cekInvoice', 'kondisis', 'invoices', 'cekInvoiceBayar'));
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
            $stok = InventoryGreenHouse::where('lokasi_id', 3)
                                    ->where('kode_produk', $getProdukJual->kode_produk)
                                    ->where('kondisi_id', $getProdukJual->kondisi_id)
                                    ->first();

            if (!$stok || $stok->jumlah < intval($req->jumlahproduk[$i]) * intval($req->jml_produk) || $stok->jumlah < $stok->min_stok) {
                $allStockAvailable = false;
                break;
            }
        }

        // dd($stok);

        if (!$allStockAvailable) {
            return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory atau stok tidak mencukupi');
        }
        
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

                $stok = InventoryGreenHouse::where('lokasi_id', $lokasi->tipe_lokasi)
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
        // dd($data);
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            // $getProdukJual = InventoryGreenHouse::where('id', $data['nama_produk'][$i])->first();
            // dd($getProdukJual);
            // $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            
            $produkmutasi = Produk_Terjual::with('komponen')->where('no_mutasigg', $req->no_mutasi)
                                        ->where('id', $data['nama_produk'][$i])
                                        ->first();
            
            foreach($produkmutasi->komponen as $komponen)
            {
                // dd($lokasi->tipe_lokasi);
                $stok = InventoryGallery::where('lokasi_id', $lokasi->tipe_lokasi)
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
        
        return redirect(route('mutasioutlet.index'))->with('success', 'Data Berhasil Disimpan');
    }


}
