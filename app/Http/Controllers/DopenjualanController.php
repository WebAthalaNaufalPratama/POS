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
use App\Models\InventoryGallery;
use App\Models\InventoryOutlet;
use App\Models\InventoryGreenHouse;
use App\Models\Komponen_Produk_Terjual;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DopenjualanController extends Controller
{
    public function index(Request $req)
    {
        // $dopenjualans = DeliveryOrder::where('no_do', 'LIKE', 'DOP%')->orderBy('created_at', 'desc')->get();
        $query = DeliveryOrder::where('jenis_do', 'PENJUALAN')->where('no_do', 'LIKE', 'DOP%')->orderBy('created_at', 'desc');

        if ($req->customer) {
            $query->where('customer_id', $req->input('customer'));
        }
        if ($req->driver) {
            $query->where('driver', $req->input('driver'));
        }
        if ($req->dateStart) {
            $query->where('tanggal_kirim', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kirim', '<=', $req->input('dateEnd'));
        }
        $dopenjualans = $query->get();
        $customer = DeliveryOrder::select('customer_id')
        ->distinct()
        ->join('customers', 'delivery_orders.customer_id', '=', 'customers.id')
        ->orderBy('customers.nama')
        ->get();
        $driver = DeliveryOrder::select('driver')
        ->distinct()
        ->join('karyawans', 'delivery_orders.driver', '=', 'karyawans.id')
        ->orderBy('karyawans.nama')
        ->get();
        // dd($dopenjualans);
        return view('dopenjualan.index', compact('dopenjualans', 'customer', 'driver'));
    }

    public function create($penjualan)
    {
        $penjualans = Penjualan::with('produk')->find($penjualan);
        // dd($produk); 
        // dd($penjualans);
        $user = Auth::user();
        $lokasis = Lokasi::find($user);
        $karyawans = Karyawan::where('jabatan', 'Driver')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        // dd($produks);
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $produkjuals = Produk_Jual::all();
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        // dd($produks);

        return view('dopenjualan.create', compact('penjualans', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals', 'cekInvoice'));
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'no_do' => 'required',
            'no_referensi' => 'required',
            'tanggal_kirim' => 'required',
            'driver' => 'required',
            'customer_id' => 'required',
            'penerima' => 'required',
            'handphone' => 'required',
            'alamat' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        // dd($data);
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_do_penjualan', $fileName, 'public');
            // dd($filePath);
            $data['file'] = $filePath;
        }
        // dd($data['nama_produk']);
        $data['jenis_do'] = 'PENJUALAN';
        $data['status'] = 'DIKIRIM';
        // $data['tanggal_pembuat'] = now();
        $data['pembuat'] = Auth::user()->id;
        $invoice = Penjualan::where('no_invoice', $req->no_referensi)->first();
        // dd($invoice);
        $lokasi = Lokasi::where('id', $invoice->lokasi_id)->first();
        // dd($lokasi);

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

            // Check stock availability
            $allStockAvailable = $checkStockAvailability($requiredQuantities, $invoice->lokasi_id);
            // dd($allStockAvailable);

            // Redirect if any component is out of stock
            if (!$allStockAvailable) {
                return redirect()->route('inven_galeri.create')->with('fail', 'Data Produk Belum Ada Di Inventory Atau Stok Kurang');
            }
        }
        // elseif($lokasi->tipe_lokasi == 2){
        //     for ($i = 0; $i < count($data['nama_produk']); $i++) {
        //         $getProdukJual = Produk_Terjual::with('komponen')->where('id', $data['nama_produk'][$i])->first();
        //         $stok = InventoryOutlet::where('lokasi_id', $lokasi->id)
        //                             ->where('kode_produk', $getProdukJual->produk->kode)
        //                             ->first();
        //                 // dd($stok);
                    
        //         if (!$stok) {
        //             return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
        //         }

        //         $requiredQuantity = intval($data['jumlah'][$i]);
        //     }
        //     if (!$stok || $stok->jumlah < $requiredQuantity) {
        //         return false;
        //     }
        //     if (!$allStockAvailable) {
        //         return redirect()->route('inven_outlet.create')->with('fail', 'Data Produk Belum Ada Di Inventory Atau Stok Kurang');
        //     }
        // }
        

        // save data do
        $check = DeliveryOrder::create($data);
        if (!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
    
        // save produk do
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = Produk_Terjual::with('komponen')->where('id', $data['nama_produk'][$i])->first();
            // dd($getProdukJual->komponen);
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->produk_jual_id,
                'no_do' => $check->no_do,
                'jumlah' => $data['jumlah'][$i],
                'satuan' => $data['satuan'][$i],
                'keterangan' => $data['keterangan'][$i]
            ]);
            
            if($getProdukJual){
                $getProdukJual->jumlah_dikirim = intval($getProdukJual->jumlah_dikirim) - intval($produk_terjual->jumlah);
                $getProdukJual->update();
            }

            if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            if($lokasi->tipe_lokasi == 1 && $invoice->distribusi == 'Dikirim')
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
                    $stok = InventoryGallery::where('lokasi_id', $invoice->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                    if(!$stok){
                        return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                    }elseif($stok){
                        $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($produk_terjual->jumlah));
                        $stok->update();
                    }
                }
            }elseif($lokasi->tipe_lokasi == 2 && $invoice->distribusi == 'Dikirim')
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

                //pengurangan inven outlet
                $stok = InventoryOutlet::where('lokasi_id', $lokasi->id)
                            ->where('kode_produk', $produk_terjual->produk->kode)
                            ->first();
                // dd($stok);
            
                if (!$stok) {
                    return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                }

                $stok->jumlah = intval($stok->jumlah) - intval($produk_terjual->jumlah);
                $stok->save();
            }else{
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
                        return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data komponen produk terjual');
                    }
                }
            }
        }

        if (!empty($data['nama_produk2'])) {
            // Simpan data tambahan
            foreach ($data['nama_produk2'] as $index => $nama_produk) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $nama_produk)->first();
                if (!$getProdukJual) {
                    return redirect(route('penjualan.index'))->with('success', 'Data tersimpan');
                }
        
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_do' => $check->no_do,
                    'jumlah' => $data['jumlah2'][$index],
                    'satuan' => $data['satuan2'][$index],
                    'jenis' => 'TAMBAHAN',
                    'keterangan' => $data['keterangan2'][$index]
                ]);
        
                if (!$produk_terjual) {
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data produk terjual');
                }

                if($lokasi->tipe_lokasi == 1 && $invoice->distribusi == 'Dikirim')
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
                        $stok = InventoryGallery::where('lokasi_id', $invoice->lokasi_id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                        if(!$stok){
                            return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                        }elseif($stok){
                            $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($produk_terjual->jumlah));
                            $stok->update();
                        }
                    }
                }elseif($lokasi->tipe_lokasi == 2 && $invoice->distribusi == 'Dikirim')
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

                    //pengurangan inven outlet
                    $stok = InventoryOutlet::where('lokasi_id', $lokasi->id)
                                ->where('kode_produk', $produk_terjual->produk->kode)
                                ->first();
                    // dd($stok);
                
                    if (!$stok) {
                        return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                    }

                    $stok->jumlah = intval($stok->jumlah) - intval($produk_terjual->jumlah);
                    $stok->save();
                }else{
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
                            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data komponen produk terjual');
                        }
                    }
                }
            }
        }
        

        return redirect(route('penjualan.index'))->with('success', 'Data tersimpan');
    }

    public function show($dopenjualan)
    {
        $dopenjualan = DeliveryOrder::find($dopenjualan);
        $produkjuals = Produk_Jual::all();
        $customers = Customer::all();
        $karyawans = Karyawan::all();
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        return view('dopenjualan.show', compact('dopenjualan', 'produkjuals', 'karyawans', 'customers', 'cekInvoice'));
    }

    public function update(Request $req, $dopenjualan)
    {
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName = $req->no_do . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_do_sewa', $fileName, 'public');
            $data['file'] = $filePath;

            // update bukti DO
            $do = DeliveryOrder::find($dopenjualan);
            $do->file = $data['file'];
            $do->update();
            return redirect()->back()->with('success', 'File tersimpan');
        } else {
            return redirect()->back()->with('fail', 'Gagal menyimpan file');
        }
    }
}
