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
use App\Models\Pembayaran;

class ReturpenjualanController extends Controller
{
    public function index(Request $req)
    {
        // $role = Auth::user()->roles()->first();
        // // dd($role);
        // $rolePermissions = $role->permissions->pluck('name')->toArray();
        // if(in_array('penjualan.index',$rolePermissions)){
        //     dd('berhasil');
        // }
        // dd($rolePermissions);
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->first();
        // dd($lokasi);
        if($lokasi->lokasi->tipe_lokasi == 2){
            $query = ReturPenjualan::with('deliveryorder')->where('no_retur', 'LIKE', 'RTO%')->where('lokasi_id', $lokasi->lokasi_id)->orderBy('created_at', 'desc');
            // dd($query);
        }elseif($lokasi->lokasi->tipe_lokasi == 1){
            $query = ReturPenjualan::with('deliveryorder')->where('no_retur', 'LIKE', 'RTP%')->where('lokasi_id', $lokasi->lokasi_id)->orderBy('created_at', 'desc');
        }else{
            $query = Penjualan::with('karyawan')->whereNotNull('no_invoice');
        }
        

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
        
        // dd($penjualans);
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
        $tipe = $lokasis[0]->tipe_lokasi;
        // dd($lokasis);
        $karyawans = Karyawan::all();
        $kondisis = Kondisi::all();
        $produkjuals = Produk_Terjual::all();
        $perPendapatan = [];

        foreach ($penjualans->deliveryorder as $deliveryOrder) {
            $selectedGFTKomponen = [];
            
            foreach ($deliveryOrder->produk as $produk) {
                foreach ($produkjuals as $index => $pj) {
                    if($pj->produk && $produk->produk->kode)
                    {
                        // dd($pj->produk->kode);
                        $isSelectedGFT = ($pj->produk->kode == $produk->produk->kode && substr($pj->produk->kode, 0, 3) === 'GFT' && $pj->no_do ==  $deliveryOrder->no_do && $pj->jenis != 'TAMBAHAN');
                        // dd($pj);
                    
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
                            // dd($selectedGFTKomponen);
                        }
                    }
                    if (!empty($selectedGFTKomponen)) {
                        $perPendapatan += $selectedGFTKomponen;
                    }
                }
                
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

        return view('returpenjualan.create', compact('tipe', 'ongkirs','perPendapatan','juals','penjualans','suppliers','cekretur','drivers','dopenjualans', 'kondisis', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals', 'cekInvoice'));
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


        if ($req->hasFile('bukti')) {
            $filePath = $this->uploadFile($req->file('bukti'));
            $data['bukti'] = $filePath;
        }
        if ($req->hasFile('file')) {
            $filePath = $this->uploadFileDO($req->file('file'));
            $data['file'] = $filePath;
        }

        $data['jenis_do'] = 'RETUR';
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
        }

        // dd($deliveryOrder);

        $handphone = Customer::where('id', $req->customer_id)->first();
        // dd($handphone);
        $data['handphone'] = $handphone->handphone;
        $data['status'] = $req->status;
        $data['catatan'] = $req->catatan_komplain;
        $returPenjualan = ReturPenjualan::create($data);
        $deliveryorder = DeliveryOrder::create($data);

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
                        
                    }
                    if($req->status == 'DIKONFIRMASI'){
                        $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $produk_terjual->produk->kode)->first();
                        if(!$stok){
                            // return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                        }elseif($stok){
                            $stok->jumlah = intval($stok->jumlah) - intval($produk_terjual->jumlah);
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
                'jumlah_dikirim' => $data['jumlah'][$i],
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

                            if ($lokasi->tipe_lokasi == 1 && $req->status == 'DIKONFIRMASI' && $req->komplain != 'diskon') {
                                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)
                                    ->where('kode_produk', $komponen_produk_terjual->kode_produk)
                                    ->where('kondisi_id', $komponen_produk_terjual->kondisi)
                                    ->first();

                                if ($stok) {
                                    $stok->jumlah += intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah);
                                    $stok->update();
                                }
                            }
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
                // dd($data[$kondisi_key]);

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

                            if($lokasi->tipe_lokasi == 1 && $req->status == 'DIKONFIRMASI'){
                                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen_produk_terjual->kode_produk)->where('kondisi_id', $komponen_produk_terjual->kondisi)->first();
                                if ($stok) {
                                    $stok->jumlah = intval($stok->jumlah) + (intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah));
                                    $stok->update();
                                }
                            }

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
        $dopenjualans = DeliveryOrder::where('no_referensi', $returpenjualans->no_retur)->first();
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
                    if (!empty($selectedGFTKomponen)) {
                        $perPendapatan += $selectedGFTKomponen;
                    }
                }
                
            // }

            
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

    public function payment($returpenjualan)
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
                    if (!empty($selectedGFTKomponen)) {
                        $perPendapatan += $selectedGFTKomponen;
                    }
                }
                
            // }
            // dd($perPendapatan);

            
        }

        $bankpens = Rekening::get();
        $Invoice = Pembayaran::where('no_invoice_bayar', 'LIKE', 'BRP%')->latest()->first();

        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 000;
        }

        // Mengambil daftar nomor invoice dari data retur
        $noInvoices = $penjualans->pluck('no_invoice')->toArray();

        // Mengambil data penjualan yang memiliki nomor invoice yang sama dengan retur
        $cekbayar = Penjualan::with('karyawan')->whereIn('no_invoice', $noInvoices)->first();
        $pembayarans = Pembayaran::with('rekening')->where('invoice_penjualan_id', $penjualans->id)->where('no_invoice_bayar', 'LIKE', 'BRP%')->orderBy('created_at', 'desc')->get();
        // dd($cekbayar);

        return view('returpenjualan.payment', compact('cekbayar','cekInvoice','bankpens','pembayarans','perPendapatan','ongkirs','juals','produkKomponens','produkreturjuals','penjualans','returpenjualans','suppliers','drivers','dopenjualans', 'kondisis', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals'));
    }

    public function paymentretur(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'invoice_penjualan_id' => 'required',
            'no_invoice_bayar' => 'required',
            'nominal' => 'required',
            'rekening_id' => 'required',
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

        $penjualan = ReturPenjualan::find($req->invoice_penjualan_id);

        // dd($penjualan);
        if ($penjualan) {
            $cekTotalTagihan = $penjualan->total - $req->nominal;
            // $penjualan->update([
            //     'sisa_bayar' => $cekTotalTagihan,
            // ]);
            $cek = $penjualan->total;
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
            return redirect(route('returpenjualan.payment', ['penjualan' => $req->invoice_penjualan_id]))->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->back()->with('fail', 'Gagal menyimpan data');
        }
    }  

    public function audit($returpenjualan)
    {
        $penjualans = ReturPenjualan::with('produk_retur', 'deliveryorder')->find($returpenjualan);
        $returpenjualans = ReturPenjualan::with('deliveryorder')->find($returpenjualan);
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->value('lokasi_id');
        $lokasis = Lokasi::where('id', $karyawan)->get();
        $karyawans = Karyawan::all();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_retur', $returpenjualans->no_retur)->get();
        $produkjuals = Produk_Terjual::all();
        $produkreturjuals = Produk_Jual::all();
        // dd($produkjuals);
        $suppliers = Supplier::all();
        $dopenjualans = DeliveryOrder::where('no_referensi', $returpenjualans->no_retur)->first();
        // dd($dopenjualans);
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
                                            'id' => $komponen->id,
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
                
            // }

            
        }
        // dd($perPendapatan);

        $bankpens = Rekening::get();
        $Invoice = Pembayaran::where('no_invoice_bayar', 'LIKE', 'BRP%')->latest()->first();

        if ($Invoice != null) {
            $substring = substr($Invoice->no_invoice_bayar, 11);
            $cekInvoice = substr($substring, 0, 3);
            // dd($cekInvoice);
        } else {
            $cekInvoice = 000;
        }

        // Mengambil daftar nomor invoice dari data retur
        $noInvoices = $penjualans->pluck('no_invoice')->toArray();

        // Mengambil data penjualan yang memiliki nomor invoice yang sama dengan retur
        $cekbayar = Penjualan::with('karyawan')->whereIn('no_invoice', $noInvoices)->first();
        $pembayarans = Pembayaran::with('rekening')->where('invoice_penjualan_id', $penjualans->id)->where('no_invoice_bayar', 'LIKE', 'BRP%')->orderBy('created_at', 'desc')->get();
        

        return view('returpenjualan.audit', compact('pembayarans','perPendapatan','ongkirs','juals','produkKomponens','produkreturjuals','penjualans','returpenjualans','suppliers','drivers','dopenjualans', 'kondisis', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals'));
    }

    public function auditretur_update(Request $req)
    {
        // dd($req);
        $retur = $req->input('penjualan');

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = $req->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_retur_penjualan', $fileName, 'public');
            $data['bukti'] = $filePath;
        }

        $allkeys = array_keys($req->all());

        $prefixes = [
            'kodegiftproduk_', 'komponengiftproduk', 'kondisigiftproduk', 'jumlahgiftproduk',
            'jumlah', 'alasan', 'diskon', 'jenis_diskon', 'harga', 'totalharga',
            'namaproduk2', 'jumlah2', 'satuan2', 'keterangan2', 'nama_produk', 'file', 'status',
            'kondisitradproduk_', 'jumlahtradproduk_', 'kode_produk2', 'idgiftproduk_'
        ];
        
        // Filter kunci berdasarkan awalan yang ditentukan
        $keysToFilter = array_filter($allkeys, function($key) use ($prefixes) {
            foreach ($prefixes as $prefix) {
                if (strpos($key, $prefix) === 0) {
                    return true;
                }
            }
            return false;
        });
        $merge = array_merge(['_method', '_token', 'DataTables_Table_0_length', 'nama_produk', 'penjualan', 'penerima', 'tanggal_kirim', 'driver','alamat', 'alamat_tujuan'], $keysToFilter);
        // dd($merge);

        $data = $req->except($merge);
        // dd($data);
        $handphone = Customer::where('id', $req->customer_id)->value('handphone');

        $update = ReturPenjualan::where('id', $retur)->update($data);

        $datado = [
            'no_do' => $req->no_do,
            'no_referensi' => $req->no_retur,
            'jenis_do' => 'RETUR',
            'tanggal_kirim' => $req->tanggal_kirim,
            'customer_id' => $req->customer_id,
            'handphone' => $handphone,
            'penerima' => $req->penerima,
            'alamat' => $req->alamat,
            'catatan' => $req->catatan,
            'status' => $req->status,
            'alasan_batal' => $req->alasan_batal, 
            'driver' => $req->driver,
        ];
        
        if ($req->hasFile('file')) {
            $filePath = $this->uploadFileDO($req->file('file'));
            $datado['file'] = $filePath; // Menggunakan $datado
        }
        
        $updateDO = DeliveryOrder::where('no_do', $req->no_do)->update($datado); // Memasukkan data untuk update
        if($req->status == 'DIBATALKAN'){
            return redirect(route('returpenjualan.index'))->with('success', 'Berhasil Mengupdate Data');
        }
        $lokasi = Lokasi::where('id', $req->lokasi_id)->first();

        //hapus komponen agar bisa di create ulang
        $produkterjualretur = Produk_Terjual::whereIn('id', $req->nama_produk)->get();
        $produkterjualganti = Produk_Terjual::whereIn('id', $req->nama_produk2)->get();
        $arrayCombined = array_merge($produkterjualganti->pluck('id')->toArray(), $produkterjualretur->pluck('id')->toArray());
        $cek = Produk_Terjual::whereNotIn('id', $arrayCombined)->where('no_retur', $req->no_retur)->get();
        $ceken = $cek->pluck('id')->toArray();

        if (!empty($ceken)) {
            Produk_Terjual::whereIn('id', $ceken)->forceDelete();
            Komponen_Produk_Terjual::whereIn('produk_terjual_id', $ceken)->forceDelete();
        }
        Komponen_Produk_Terjual::whereIn('produk_terjual_id', $produkterjualganti->pluck('id')->toArray())->forceDelete();

        if($req->komplain == 'retur'){
            for ($i = 0; $i < count($req->nama_produk2); $i++) {
                $getProdukJual = Produk_Terjual::with('komponen')->where('id', $req->nama_produk2[$i])->first();
                $getProduk = Produk_Jual::where('kode', $req->kode_produk2[$i])->first();
                $produk_terjual = Produk_Terjual::where('id', $req->nama_produk2[$i])->update([
                    'produk_jual_id' => $getProduk->id,
                    'no_retur' => $getProdukJual->no_retur,
                    'jumlah' => $req->jumlah2[$i],
                    'satuan' => $req->satuan2[$i],
                    'jenis' => 'GANTI',
                    'keterangan' => $req->keterangan2[$i]
                ]);
    
                if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                if($lokasi->tipe_lokasi == 1)
                {
                    foreach ($getProduk->komponen as $komponen ) {
                        $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                            'produk_terjual_id' => $getProdukJual->id,
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
                    foreach ($getProduk->komponen as $komponen ) {
                        $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                            'produk_terjual_id' => $getProdukJual->id,
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
                        
                    }
                    if($req->status == 'DIKONFIRMASI'){
                        $stok = InventoryOutlet::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $getProdukJual->produk->kode)->first();
                        if(!$stok){
                            // return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                        }elseif($stok){
                            $stok->jumlah = intval($stok->jumlah) - intval($req->jumlah);
                            $stok->update();
                        }
                    }
                }
            }
        }

        // PRODUK RETUR
        $cek = [];
        for ($i = 0; $i < count($req->nama_produk); $i++) {
            $getProdukJual = Produk_Terjual::with('komponen')->where('id', $req->nama_produk[$i])->first();
            $produk_terjual = Produk_Terjual::where('id', $req->nama_produk[$i])->update([
                'produk_jual_id' => $getProdukJual->produk_jual_id,
                'no_retur' => $getProdukJual->no_retur,
                'jenis' => 'RETUR',
                'alasan' => $req->alasan[$i],
                'jumlah_dikirim' => $req->jumlah[$i],
                'jumlah' => $req->jumlah[$i],
                'jenis_diskon' => $req->jenis_diskon[$i],
                'diskon' => $req->diskon[$i],
                'harga' => $req->harga[$i],
                'harga_jual' => $req->totalharga[$i]
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
                $id_key = 'idgiftproduk_' . $i;

                if (isset($req->$komponen_key) && is_array($req->$komponen_key)) {
                    $cekcount = count($req->$komponen_key);
                    $produkCollection = collect();

                    // Membuat koleksi produk dari kode produk yang diberikan
                    foreach ($req->$kode_key as $index => $kodeProduk) {
                        $produk = Produk::where('kode', $kodeProduk)->first();
                        if ($produk) {
                            $produkCollection->push([
                                'id' => isset($req->$id_key[$index]) ? $req->$id_key[$index] : 0,
                                'produk' => $produk,
                                'kondisi' => isset($req->$kondisi_key[$index]) ? Kondisi::where('nama', $req->$kondisi_key[$index])->value('id') : null,
                                'jumlah' => isset($req->$jumlah_key[$index]) ? $req->$jumlah_key[$index] : 0,
                            ]);
                        }
                    }


                    // Iterasi melalui setiap produk dan setiap komponen
                    foreach ($produkCollection as $getProduk) {
                            // Buat objek Komponen_Produk_Terjual
                            $komponen_produk_terjual = Komponen_Produk_Terjual::where('produk_terjual_id', $getProdukJual->id)->where('id', $getProduk['id'])->update([
                                'produk_terjual_id' => $getProdukJual->id,
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

                            if ($lokasi->tipe_lokasi == 1 && $req->status == 'DIKONFIRMASI' && $req->komplain != 'diskon') {
                                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)
                                    ->where('kode_produk', $getProduk['produk']->kode)
                                    ->where('kondisi_id', $getProduk['kondisi'])
                                    ->first();

                                if ($stok) {
                                    $stok->jumlah += intval($getProduk['jumlah']) * intval($getProdukJual->jumlah);
                                    $stok->update();
                                }
                            }
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

                if (isset($req->$kondisi_key) && is_array($req->$kondisi_key)) {
                    for ($index = 0; $index < count($req->$kondisi_key); $index++) {
                        $kondisi = Kondisi::where('nama', $req->$kondisi_key[$index])->value('id');
                        $jumlah = $req->$jumlah_key[$index];

                        foreach ($getProdukJual->komponen as $komponen) {
                            $komponen_produk_terjual = Komponen_Produk_Terjual::where('produk_terjual_id', $getProdukJual->id)->update([
                                'produk_terjual_id' => $getProdukJual->id,
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

                            if($lokasi->tipe_lokasi == 1 && $req->status == 'DIKONFIRMASI'){
                                $stok = InventoryGallery::where('lokasi_id', $req->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $kondisi)->first();
                                // dd($stok);
                                if ($stok) {
                                    $stok->jumlah = intval($stok->jumlah) + (intval($jumlah) * intval($getProdukJual->jumlah));
                                    // dd($getProdukJual->jumlah);
                                    $stok->update();
                                }
                            }

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
        }
        
        if($req->status == 'DIKONFIRMASI'){
            return redirect(route('returpenjualan.show', ['returpenjualan' => $retur]))->with('success', 'Silakan set Komponen Ganti');
        }elseif($req->status == 'TUNDA'){
            return redirect(route('returpenjualan.index'))->with('success', 'Berhasil Mengupdate data');
        }else{
            return redirect()->back()->with('fail', 'Gagal Mengupdate data');
        }
    }

}
