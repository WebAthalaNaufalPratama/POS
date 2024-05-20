<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Komponen_Produk_Jual;
use App\Models\Kondisi;
use App\Models\Produk;
use App\Models\Produk_Jual;
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
use App\Models\Produk_Terjual;
use App\Models\DeliveryOrder;
use App\Models\ProdukReturJual;
use App\Models\ReturPenjualan;
use App\Models\Supplier;
use App\Models\InventoryGallery;
use App\Models\InventoryOutlet;
use App\Models\Komponen_Produk_Terjual;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ReturpenjualanController extends Controller
{
    public function index()
    {
        $returs = ReturPenjualan::orderBy('created_at', 'desc')->get();
        // dd($returs);
        return view('returpenjualan.index', compact('returs'));
    }

    public function create($penjualan)
    {
        $penjualans = Penjualan::with('produk', 'deliveryorder')->find($penjualan);
        // dd($penjualans);
        $user = Auth::user();
        // dd($user);
        $karyawan = Karyawan::where('user_id', $user->id)->value('lokasi_id');
        // dd($karyawan);
        $lokasis = Lokasi::where('id', $karyawan)->get();
        $karyawans = Karyawan::all();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Terjual::all();
        $perPendapatan = [];

        foreach ($penjualans->deliveryorder as $deliveryOrder) {
            $selectedGFTKomponen = [];
            
            foreach ($deliveryOrder->produk as $produk) {
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
                                        $selectedGFTKomponen[$deliveryOrder->no_do][] = [
                                            'nama' => $komponen->nama_produk,
                                            'kondisi' => $kondisi->nama,
                                            'jumlah' => $komponen->jumlah,
                                            'produk' => $komponen->produk_terjual_id,
                                            'do' => $deliveryOrder->no_do
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
        }


        // dd($perPendapatan);

        // dd($perPendapatan[$deliveryOrder->no_do]);
        
        $juals = Produk_Jual::all();
        
        // $customers = Customer::where('id', $penjualans->id_customer)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $produks = Produk_Jual::all();
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        $returInvoice = ReturPenjualan::latest()->first();
        if ($returInvoice != null) {
            $substring = substr($returInvoice->no_retur, 11);
            $cekretur = substr($substring, 0, 3);
        } else {
            $cekretur = 0;
        }
        $suppliers = Supplier::all();
        $dopenjualans = DeliveryOrder::where('no_do', $penjualans->no_invoice)->get();
        // $produkjuals = Produk_Jual::all();
        $customers = Customer::all();
        $karyawans = Karyawan::all();
        $ongkirs = Ongkir::get();
        
        $drivers = Karyawan::where('jabatan', 'driver')->get();

        return view('returpenjualan.create', compact('ongkirs','perPendapatan','juals','penjualans','suppliers','cekretur','drivers','dopenjualans', 'kondisis', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals', 'cekInvoice'));
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'no_retur' => 'required',
            'no_invoice' => 'required',
            'lokasi_id' => 'required',
            'bukti' => 'required',
            'tanggal_invoice' => 'required',
            'tanggal_retur' => 'required',
            'customer_id' => 'required',
            'supplier_id' => 'required',
            'no_do' => 'required',
            'komplain' => 'required',
            'catatan_komplain' => 'required',
            'sub_total' => 'required',
            'total' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        $data = $req->except(['_token', '_method']);
        $jumlahTotal = 0;

        for ($i = 0; $i < 100; $i++) {
            $jumlahGift = isset($data['jumlahgiftproduk_' . $i]) ? array_sum($data['jumlahgiftproduk_' . $i]) : 0;
            $jumlahTrad = isset($data['jumlahtradproduk_' . $i]) ? array_sum($data['jumlahtradproduk_' . $i]) : 0;

            $jumlahTotal += $jumlahGift + $jumlahTrad;
        }
        // dd($data);
        
        // dd($cek);
        
        // dd($jumlahTotal);

        if ($req->hasFile('bukti')) {
            $filePath = $this->uploadFile($req->file('bukti'));
            $data['bukti'] = $filePath;
        }
        if ($req->hasFile('file')) {
            $filePath = $this->uploadFileDO($req->file('file'));
            $data['file'] = $filePath;
        }

        $data['jenis_do'] = 'RETUR';
        $data['status'] = 'DIKIRIM';
        $data['pembuat'] = Auth::user()->id;
        $data['no_referensi'] = $req->no_retur;
        $data['tanggal_pembuat'] = now();
        $data['handphone'] = Customer::where('id', $req->customer_id)->value('handphone');
        // dd($data);
        // if($req->komplain == 'retur'){
        //     $deliveryOrder = DeliveryOrder::create($data);
        // } 

        $lokasi = Lokasi::where('id', $req->lokasi_id)->first();

        // cek produk inventory
        $allStockAvailable = true;

        if ($lokasi->tipe_lokasi == 1) {
            // Function to accumulate required quantities for given product names and model
            $accumulateRequiredQuantities = function($productNames, $req, $model, $field) {
                $requiredQuantities = [];

                for ($i = 0; $i < count($productNames); $i++) {
                    $getProdukJual = $model::with('komponen')->where($field, $productNames[$i])->first();

                    foreach ($getProdukJual->komponen as $komponen) {
                        $komponenKey = $komponen->kode_produk . '_' . $komponen->kondisi;

                        if (!isset($requiredQuantities[$komponenKey])) {
                            $requiredQuantities[$komponenKey] = 0;
                        }

                        $requiredQuantities[$komponenKey] += intval($req->jumlah[$i]) * intval($komponen->jumlah);
                    }
                }

                return $requiredQuantities;
            };

            // Helper function to merge required quantities
            $mergeRequiredQuantities = function($quantities1, $quantities2) {
                foreach ($quantities2 as $key => $quantity) {
                    if (!isset($quantities1[$key])) {
                        $quantities1[$key] = 0;
                    }
                    $quantities1[$key] += $quantity;
                }
                return $quantities1;
            };

            // Helper function to check stock availability for given required quantities
            $checkStockAvailability = function($requiredQuantities, $lokasi_id) {
                foreach ($requiredQuantities as $key => $requiredQuantity) {
                    list($kode_produk, $kondisi) = explode('_', $key);

                    $stok = InventoryGallery::where('lokasi_id', $lokasi_id)
                                            ->where('kode_produk', $kode_produk)
                                            ->where('kondisi_id', $kondisi)
                                            ->first();

                    if (!$stok || $stok->jumlah < $requiredQuantity) {
                        return false;
                    }
                }

                return true;
            };

            // Accumulate required quantities for the first set of products (Produk_Terjual)
            $requiredQuantities1 = $accumulateRequiredQuantities($req->nama_produk, $req, Produk_Terjual::class, 'id');

            // Accumulate required quantities for the second set of products (Produk_Jual)
            $requiredQuantities2 = $accumulateRequiredQuantities($req->nama_produk2, $req, Produk_Jual::class, 'kode');

            // Merge the required quantities
            $requiredQuantities = $mergeRequiredQuantities($requiredQuantities1, $requiredQuantities2);
            // dd($requiredQuantities);
            // Check stock availability
            $allStockAvailable = $checkStockAvailability($requiredQuantities, $req->lokasi_id);
            // dd($allStockAvailable);

            // Redirect if any component is out of stock
            if (!$allStockAvailable) {
                return redirect()->route('inven_galeri.create')->with('fail', 'Data Produk Belum Ada Di Inventory Atau Stok Kurang');
            }
        }elseif($lokasi->tipe_lokasi == 2){
            $accumulateRequiredQuantities = function($productNames, $req, $model, $field) {
                $requiredQuantities = [];

                for ($i = 0; $i < count($productNames); $i++) {
                    $getProdukJual = $model::with('komponen')->where($field, $productNames[$i])->first();

                    foreach ($getProdukJual->komponen as $komponen) {
                        $komponenKey = $komponen->kode_produk . '_' . $komponen->kondisi;

                        if (!isset($requiredQuantities[$komponenKey])) {
                            $requiredQuantities[$komponenKey] = 0;
                        }

                        $requiredQuantities[$komponenKey] += intval($req->jumlah[$i]) * intval($komponen->jumlah);
                    }
                }

                return $requiredQuantities;
            };

            // Helper function to merge required quantities
            $mergeRequiredQuantities = function($quantities1, $quantities2) {
                foreach ($quantities2 as $key => $quantity) {
                    if (!isset($quantities1[$key])) {
                        $quantities1[$key] = 0;
                    }
                    $quantities1[$key] += $quantity;
                }
                return $quantities1;
            };

            // Helper function to check stock availability for given required quantities
            $checkStockAvailability = function($requiredQuantities, $lokasi_id) {
                foreach ($requiredQuantities as $key => $requiredQuantity) {
                    list($kode_produk, $kondisi) = explode('_', $key);

                    $stok = InventoryOutlet::where('lokasi_id', $lokasi_id)
                                            ->where('kode_produk', $kode_produk)
                                            ->where('kondisi_id', $kondisi)
                                            ->first();

                    if (!$stok || $stok->jumlah < $requiredQuantity) {
                        return false;
                    }
                }

                return true;
            };

            // Accumulate required quantities for the first set of products (Produk_Terjual)
            $requiredQuantities1 = $accumulateRequiredQuantities($req->nama_produk, $req, Produk_Terjual::class, 'id');

            // Accumulate required quantities for the second set of products (Produk_Jual)
            $requiredQuantities2 = $accumulateRequiredQuantities($req->nama_produk2, $req, Produk_Jual::class, 'kode');

            // Merge the required quantities
            $requiredQuantities = $mergeRequiredQuantities($requiredQuantities1, $requiredQuantities2);

            // Check stock availability
            $allStockAvailable = $checkStockAvailability($requiredQuantities, $req->lokasi_id );
            // dd($allStockAvailable);

            // Redirect if any component is out of stock
            if (!$allStockAvailable) {
                return redirect()->route('inven_outlet.create')->with('fail', 'Data Produk Belum Ada Di Inventory Atau Stok Kurang');
            }
        }
        // dd($deliveryOrder);
        $returPenjualan = ReturPenjualan::create($data);

        if (!$returPenjualan) {
            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        }
        if($req->komplain == 'retur'){
            for ($i = 0; $i < count($data['nama_produk2']); $i++) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk2'][$i])->first();
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_retur' => $returPenjualan->no_retur,
                    'jumlah' => $data['jumlah2'][$i],
                    'satuan' => $data['satuan2'][$i],
                    'jenis' => 'GANTI',
                    'keterangan' => $data['keterangan2'][$i]
                ]);
    
                if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                if($lokasi->tipe_lokasi == 1)
                {
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
                        if (!$komponen_produk_terjual) {
                            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data komponen produk terjual');
                        }
                        $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                        if(!$stok){
                            return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                        }elseif($stok){
                            $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($data['jumlah2'][$i]));
                            $stok->update();
                        }
                    }
                }elseif($lokasi->tipe_lokasi == 2)
                {
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
                        if (!$komponen_produk_terjual) {
                            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data komponen produk terjual');
                        }
                        $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                        if(!$stok){
                            // return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                        }elseif($stok){
                            $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($data['jumlah2'][$i]));
                            $stok->update();
                        }
                    }
                }
            }
        }

        $cek = [];
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = Produk_Terjual::with('komponen')->where('id', $data['nama_produk'][$i])->first();
            
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->produk_jual_id,
                'no_retur' => $returPenjualan->no_retur,
                'jenis' => 'RETUR',
                'alasan' => $data['alasan'][$i],
                'jumlah' => $data['jumlah'][$i],
                'jenis_diskon' => $data['jenis_diskon'][$i],
                'diskon' => $data['diskon'][$i],
                'harga' => $data['harga'][$i],
                'harga_jual' => $data['totalharga'][$i]
            ]);
            
            if (!$produk_terjual) {
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
            
            $cekgfttrd = substr($getProdukJual->produk->kode, 0, 3);
            // dd($cekgfttrd);
            if ($cekgfttrd == 'GFT') {
                for ($index = 0; $index < count($data['komponengiftproduk_' . $i]); $index++) {
                    $kondisi = isset($data['kondisigiftproduk_' . $i][$index]) ? Kondisi::where('nama', $data['kondisigiftproduk_' . $i][$index])->value('id') : null;
                    $jumlah = isset($data['jumlahgiftproduk_' . $i][$index]) ? $data['jumlahgiftproduk_' . $i][$index] : 0;
                    if($lokasi->tipe_lokasi == 1)
                    {
                        foreach ($getProdukJual->komponen as $komponen ) {
                            // dd($getProdukJual->komponen);
                            if($komponen->tipe_produk == 2 || $komponen->tipe_produk == 1) 
                            {
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
                                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                                if(!$stok){
                                    // return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                                }elseif($stok){
                                    $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($data['jumlah'][$i]));
                                    $stok->update();
                                }
                            }
                        }
                    }elseif($lokasi->tipe_lokasi == 2)
                    {
                        foreach ($getProdukJual->komponen as $komponen ) {
                            if($komponen->tipe_produk == 2 || $komponen->tipe_produk == 1) 
                            {
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
                                $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                                if(!$stok){
                                    // return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                                }elseif($stok){
                                    $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($data['jumlah'][$i]));
                                    $stok->update();
                                }
                            }
                        }
                    }
                }
            } elseif ($cekgfttrd == 'TRD') {
                for ($index = 0; $index < count($data['kondisitradproduk_' . $i]); $index++) {
                    $kondisi = isset($data['kondisitradproduk_' . $i][$index]) ? Kondisi::where('nama', $data['kondisitradproduk_' . $i][$index])->value('id') : null;
                    $jumlah = isset($data['jumlahtradproduk_' . $i][$index]) ? $data['jumlahtradproduk_' . $i][$index] : 0;
                    
                    if($lokasi->tipe_lokasi == 1)
                    {
                        foreach ($getProdukJual->komponen as $komponen ) {
                            if($komponen->tipe_produk == 2 || $komponen->tipe_produk == 1) {
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
                                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                                if(!$stok){
                                    // return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                                }elseif($stok){
                                    $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($data['jumlah'][$i]));
                                    $stok->update();
                                }
                            }       
                        }
                    }elseif($lokasi->tipe_lokasi == 2)
                    {
                        foreach ($getProdukJual->komponen as $komponen ) {
                            if($komponen->tipe_produk == 2 || $komponen->tipe_produk == 1) {
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
                                $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                                if(!$stok){
                                    // return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                                }elseif($stok){
                                    $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($data['jumlah'][$i]));
                                    $stok->update();
                                }
                            }
                        }   
                    }
                }                       
            }
            
            if (!$komponen_produk_terjual) {
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
        }

        
        return redirect(route('penjualan.index'))->with('success', 'Data tersimpan');
    }

private function uploadFile($file)
{
    $fileName = time() . '_' . $file->getClientOriginalName();
    return $file->storeAs('bukti_retur_penjualan', $fileName, 'public');
}
private function uploadFileDO($file)
{
    $fileName = time() . '_' . $file->getClientOriginalName();
    return $file->storeAs('bukti_DO_Retur', $fileName, 'public');
}

public function show($returpenjualan)
{
        $penjualans = ReturPenjualan::with('produk_retur', 'deliveryorder')->find($returpenjualan);
        $returpenjualans = ReturPenjualan::with('deliveryorder')->find($returpenjualan);
        // dd($returpenjualans);
        // foreach($returpenjualans->deliveryorder as $delivery){
        //     dd($delivery->penerima);
        // }
        // dd($returpenjualans->deliveryorder);
        $user = Auth::user();
        $lokasis = Lokasi::find($user);
        $karyawans = Karyawan::all();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_retur', $returpenjualans->no_retur)->get();
        // dd($produks);
        // $customers = Customer::where('id', $penjualans->id_customer)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $produkjuals = Produk_Jual::all();
        $produkreturjuals = Produk_Jual::all();
        // dd($produkjuals);
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        $Invoice = ReturPenjualan::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekretur = substr($substring, 0, 3);
        } else {
            $cekretur = 0;
        }
        $suppliers = Supplier::all();
        $dopenjualans = DeliveryOrder::where('no_do', $returpenjualans->no_invoice)->get();
        // $produkjuals = Produk_Jual::all();
        $customers = Customer::all();
        $karyawans = Karyawan::all();
        $kondisis = Kondisi::all();
        $drivers = Karyawan::where('jabatan', 'driver')->get();

        return view('returpenjualan.show', compact('produkreturjuals','penjualans','returpenjualans','suppliers','cekretur','drivers','dopenjualans', 'kondisis', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals', 'cekInvoice'));
}

public function update(Request $req, $returpenjualan)
{
    if ($req->hasFile('bukti')) {
        $file = $req->file('bukti');
        $fileName = $req->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('bukti_retur_penjualan', $fileName, 'public');
        $data['bukti'] = $filePath;

        $retur = ReturPenjualan::find($returpenjualan);
        $retur->bukti = $data['bukti'];
        $retur->update();
        return redirect()->back()->with('success', 'File tersimpan');
    } else {
        return redirect()->back()->with('fail', 'Gagal menyimpan file');
    }
}

}
