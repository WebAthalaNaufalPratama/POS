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
use Spatie\Permission\Models\Role;
use App\Models\InventoryGallery;
use App\Models\InventoryOutlet;
use App\Models\Komponen_Produk_Terjual;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ReturpenjualanController extends Controller
{
    public function index(Request $req, Role $role)
    {
        // $role = Auth::user()->roles()->first();
        // // dd($role);
        // $rolePermissions = $role->permissions->pluck('name')->toArray();
        // if(in_array('penjualan.index',$rolePermissions)){
        //     dd('berhasil');
        // }
        // dd($rolePermissions);
        $query = ReturPenjualan::orderBy('created_at', 'desc');

        if ($req->customer) {
            $query->where('customer_id', $req->input('customer'));
        }
        if ($req->driver) {
            $query->where('supplier_id', $req->input('driver'));
        }
        if ($req->dateStart) {
            $query->where('tanggal_retur', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_retur', '<=', $req->input('dateEnd'));
        }
        $dopenjualans = $query->get();
        // $suppliers = Supplier::all();
        // $customers = Customer::all();
        $returs = $query->get();
        $customers = ReturPenjualan::select('customer_id')
        ->distinct()
        ->join('customers', 'retur_penjualans.customer_id', '=', 'customers.id')
        ->orderBy('customers.nama')
        ->get();
        // dd($customers);
        $suppliers = ReturPenjualan::select('supplier_id')
        ->distinct()
        ->join('suppliers', 'retur_penjualans.supplier_id', '=', 'suppliers.id')
        ->orderBy('suppliers.nama')
        ->get();
        // dd($returs);
        return view('returpenjualan.index', compact('returs', 'suppliers', 'customers'));
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
                                            'kode' => $komponen->kode_produk,
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

        // PRODUK GANTI
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
                        // $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                        // if(!$stok){
                        //     return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                        // }elseif($stok){
                        //     $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($data['jumlah2'][$i]));
                        //     $stok->update();
                        // }
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
                        $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen->kode_produk)->first();
                        if(!$stok){
                            // return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                        }elseif($stok){
                            $stok->jumlah = intval($stok->jumlah) - intval($data['jumlah2'][$i]);
                            $stok->update();
                        }
                    }
                }
            }
        }

        // PRODUK RETUR
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
        // $user = Auth::user();
        $lokasis = Lokasi::all();
        $karyawans = Karyawan::all();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_retur', $returpenjualans->no_retur)->get();
        // dd($produks);
        // $customers = Customer::where('id', $penjualans->id_customer)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $produkjuals = Produk_Terjual::all();
        $produkreturjuals = Produk_Jual::all();
        // dd($produkjuals);
        $suppliers = Supplier::all();
        $dopenjualans = DeliveryOrder::where('no_do', $returpenjualans->no_invoice)->get();
        // $produkjuals = Produk_Jual::all();
        $customers = Customer::all();
        $karyawans = Karyawan::all();
        $kondisis = Kondisi::all();
        $drivers = Karyawan::where('jabatan', 'driver')->get();
        $produkKomponens = Produk::where('tipe_produk', 1)->orWhere('tipe_produk', 2)->get();
        $juals = Produk_Jual::all();
        $ongkirs = Ongkir::get();
        $perPendapatan = [];

        foreach ($penjualans->produk_retur as $produk) {
            $selectedGFTKomponen = [];
            
            // foreach ($deliveryOrder->produk as $produk) {
                foreach ($produkjuals as $index => $pj) {
                    // dd($produkjuals);
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
                    
                }
            // }

            if (!empty($selectedGFTKomponen)) {
                $perPendapatan += $selectedGFTKomponen;
            }
        }
        // dd($perPendapatan);

        return view('returpenjualan.show', compact('perPendapatan','ongkirs','juals','produkKomponens','produkreturjuals','penjualans','returpenjualans','suppliers','drivers','dopenjualans', 'kondisis', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals'));
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
