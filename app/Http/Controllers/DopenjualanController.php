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
use App\Models\User;
use App\Models\Komponen_Produk_Terjual;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use PDF;

class DopenjualanController extends Controller
{
    public function index(Request $req)
{
    $user = Auth::user();
    $lokasi = Karyawan::where('user_id', $user->id)->first();

    $query = DeliveryOrder::with('customer', 'data_driver')->where('lokasi_pengirim', $lokasi->lokasi_id);

    if ($lokasi) {
        if ($lokasi->lokasi->tipe_lokasi == 2 && $user->hasRole(['KasirOutlet'])) {
            $query->where('no_do', 'LIKE', 'DVO%');
        } elseif ($lokasi->lokasi->tipe_lokasi == 1 && $user->hasRole(['KasirGallery', 'AdminGallery'])) {
            $query->where('no_do', 'LIKE', 'DOP%');
        } elseif ($user->hasRole(['Finance', 'Auditor'])) {
            $query->where('no_do', 'LIKE', $lokasi->lokasi->tipe_lokasi == 1 ? 'DOP%' : 'DVO%')
                  ->whereIn('status', ['DIKONFIRMASI', 'DIBATALKAN']);
        }
    }

    // Apply filters
    if ($search = $req->input('search.value')) {
        $columns = ['delivery_orders.no_do','delivery_orders.no_referensi', 'delivery_orders.tanggal_kirim', 'delivery_orders.status'];
        $query->where(function($q) use ($search, $columns) {
            foreach ($columns as $column) {
                $q->orWhere($column, 'like', "%{$search}%");
            }

            $q->orWhereHas('data_driver', function($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%");
            });
            $q->orWhereHas('customer', function($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%");
            });
        });
    }
    
    if ($order = $req->input('order.0.column')) {
        $columns = ['no_do', 'no_referensi', 'tanggal_kirim','status'];
        $query->orderBy($columns[$order], $req->input('order.0.dir'));
    }

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

    if ($req->ajax()) {
        $totalRecords = $query->count();
        
        $orderColumn = $req->input('order.0.column');
        $orderDir = $req->input('order.0.dir');
        
        // Map DataTables column index to database column name
        $columns = [
            0 => 'id',
            1 => 'no_do',
            2 => 'no_invoice',
            3 => 'nama_customer',
            4 => 'tanggal_kirim',
            5 => 'status',
            6 => 'nama_driver',
            7 => 'aksi'
        ];

        $orderByColumn = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'id';

        $data = $query->orderByDesc('id')
                      ->skip($req->input('start'))
                      ->take($req->input('length'))
                      ->get();

        return response()->json([
            'draw' => intval($req->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }

    // Load data for filters and non-AJAX view
    $customers = Customer::where('lokasi_id', $lokasi->lokasi_id)->orderBy('nama')->get();
    $drivers = Karyawan::where('jabatan', 'driver')->orderBy('nama')->get();

    return view('dopenjualan.index', compact('customers', 'drivers'));
}



    public function create($penjualan)
    {
        $penjualans = Penjualan::with('produk')->find($penjualan);
        $kondisis = Kondisi::all();
        $user = Auth::user();
        $karyawans = Karyawan::where('jabatan', 'Driver')->get();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $customers = Customer::where('id', $penjualans->id_customer)->get();
        $produkjuals = Produk_Jual::all();
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }

        return view('dopenjualan.create', compact('kondisis','penjualans', 'karyawans', 'produks', 'customers', 'produks', 'produkjuals', 'cekInvoice'));
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
        if ($req->hasFile('file')) {
            // Simpan file baru
            $file = $req->file('file');
            $fileName = $req->no_do . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_do_penjualan/' . $fileName;
        
            // Optimize dan simpan file baru
            Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                ->save(storage_path('app/public/' . $filePath));
        
            // Hapus file lama
            // if (!empty($penjualan->bukti_file)) {
            //     $oldFilePath = storage_path('app/public/' . $penjualan->bukti_file);
            //     if (File::exists($oldFilePath)) {
            //         File::delete($oldFilePath);
            //     }
            // }
        
            // Verifikasi penyimpanan file baru
            if (File::exists(storage_path('app/public/' . $filePath))) {
                $data['file'] = $filePath;
            } else {
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        }
        $lokasipengirim = Penjualan::where('no_invoice', $req->no_referensi)->value('lokasi_pengirim');
        $data['lokasi_pengirim'] = $lokasipengirim;
        $data['jenis_do'] = 'PENJUALAN'; 
        $data['pembuat'] = Auth::user()->id;
        $invoice = Penjualan::where('no_invoice', $req->no_referensi)->first();
        $data['alasan_batal'] = $req->alasan;
        $lokasi = Lokasi::where('id', $invoice->lokasi_id)->first();
        if($lokasi->tipe_lokasi == 2) {
            $data['lokasi_pengirim'] = $lokasi->id; 
        }

        if ($req->status == 'DIKONFIRMASI') {
            $requiredQuantities = $this->accumulateAllRequiredQuantities($req);
            $allStockAvailable = $this->checkStockAvailability($requiredQuantities, $invoice->lokasi_id, $lokasi->tipe_lokasi);
    
            if (!$allStockAvailable) {
                return redirect()->route('inven_galeri.create')->with('fail', 'Data Produk Belum Ada Di Inventory Atau Stok Kurang');
            }
        }

        if (substr($data['no_do'], 0,3) == 'DOP') {
            $prefix = 'DOP' . date('Ymd');
            $cekInvoice = DeliveryOrder::where('no_do', 'LIKE', $prefix . '%')
                ->orderBy('no_do', 'desc')
                ->get();

            if ($cekInvoice->isEmpty()) {
                $increment = 1;
            } else {
                $lastInvoice = $cekInvoice->first()->no_do;
                $lastIncrement = (int)substr($lastInvoice, strlen($prefix));
                $increment = $lastIncrement + 1;
            }

            $data['no_do'] = $prefix . str_pad($increment, 3, '0', STR_PAD_LEFT);

        } elseif (substr($data['no_do'], 0,3) == 'DOR') {
            $prefix = 'DOR' . date('Ymd');
            $cekInvoice = DeliveryOrder::where('no_do', 'LIKE', $prefix . '%')
                ->orderBy('no_do', 'desc')
                ->get();

            if ($cekInvoice->isEmpty()) {
                $increment = 1;
            } else {
                $lastInvoice = $cekInvoice->first()->no_do;
                $lastIncrement = (int)substr($lastInvoice, strlen($prefix));
                $increment = $lastIncrement + 1;
            }

            $data['no_do'] = $prefix . str_pad($increment, 3, '0', STR_PAD_LEFT);
        }

        // save data do
        $check = DeliveryOrder::create($data);
        if (!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
    
        // save produk do
        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = Produk_Terjual::with('komponen')->where('id', $data['nama_produk'][$i])->first();
            // dd($getProdukJual->komponen);
            $produk_terjual = Produk_Terjual::create([
                'produk_jual_id' => $getProdukJual->produk_jual_id,
                'no_invoice' => $data['nama_produk'][$i],
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
                    if($req->status == 'DIKONFIRMASI'){
                        $stok = InventoryGallery::where('lokasi_id', $lokasipengirim)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                        if(!$stok){
                            return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                        }elseif($stok){
                            $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($produk_terjual->jumlah));
                            $stok->update();
                        }
                    }
                }
            }elseif($lokasi->tipe_lokasi == 2 && $invoice->distribusi == 'Dikirim' && $req->status == 'DIKONFIRMASI')
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
                    //pengurangan inven outlet
                    $stok = InventoryOutlet::where('lokasi_id', $lokasipengirim)
                                ->where('kode_produk', $produk_terjual->produk->kode)
                                ->first();
                    // dd($stok);
                
                    if (!$stok) {
                        return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                    }

                    $stok->jumlah = intval($stok->jumlah) - intval($produk_terjual->jumlah);
                    $stok->save();
                }
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

                if($lokasi->tipe_lokasi == 1 && $invoice->distribusi == 'Dikirim' && $req->status == 'DIKONFIRMASI')
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
                        if($req->status == 'DIKONFIRMASI'){
                            $stok = InventoryGallery::where('lokasi_id', $lokasipengirim)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                            if(!$stok){
                                return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                            }elseif($stok){
                                $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($produk_terjual->jumlah));
                                $stok->update();
                            }
                        }
                    }
                }elseif($lokasi->tipe_lokasi == 2 && $invoice->distribusi == 'Dikirim' && $req->status == 'DIKONFIRMASI')
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
                        //pengurangan inven outlet
                        $stok = InventoryOutlet::where('lokasi_id', $lokasipengirim)
                                    ->where('kode_produk', $produk_terjual->produk->kode)
                                    ->first();
                        // dd($stok);
                    
                        if (!$stok) {
                            return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                        }

                        $stok->jumlah = intval($stok->jumlah) - intval($produk_terjual->jumlah);
                        $stok->save();
                    }
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

    private function accumulateRequiredQuantities($productNames, $quantities, $model, $field)
    {
        $requiredQuantities = [];
        foreach ($productNames as $index => $name) {
            $product = $model::with('komponen')->where($field, $name)->first();

            if ($product) {
                foreach ($product->komponen as $komponen) {
                    $key = $komponen->kode_produk . '_' . $komponen->kondisi;
                    $requiredQuantities[$key] = ($requiredQuantities[$key] ?? 0) + intval($quantities[$index]) * intval($komponen->jumlah);
                }
            } else {
                // Log or handle the case where the product is not found
                \Log::error("Product not found: $name");
            }
        }

        return $requiredQuantities;
    }

    // Helper function to accumulate all required quantities
    private function accumulateAllRequiredQuantities($req)
    {
        $requiredQuantities = $this->accumulateRequiredQuantities($req->nama_produk, $req->jumlah, Produk_Terjual::class, 'id');
        
        if (!empty($req->nama_produk2)) {
            $additionalQuantities = $this->accumulateRequiredQuantities($req->nama_produk2, $req->jumlah2, Produk_Jual::class, 'kode');
            $requiredQuantities = array_merge_recursive($requiredQuantities, $additionalQuantities);
        }

        return $requiredQuantities;
    }

    // Helper function to check stock availability
    private function checkStockAvailability($requiredQuantities, $lokasi_id, $lokasi_tipe)
    {
        foreach ($requiredQuantities as $key => $requiredQuantity) {
            list($kode_produk, $kondisi) = explode('_', $key);
            
            $stok = $lokasi_tipe == 1 
                ? InventoryGallery::where('lokasi_id', $lokasi_id)->where('kode_produk', $kode_produk)->where('kondisi_id', $kondisi)->first()
                : InventoryOutlet::where('lokasi_id', $lokasi_id)->where('kode_produk', $kode_produk)->first();

            if (!$stok || $stok->jumlah < $requiredQuantity) {
                return false;
            }
        }

        return true;
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

    public function pdfdopenjualan($dopenjualan)
    {
        $data = DeliveryOrder::find($dopenjualan)->toArray();
        $customer = Customer::where('id', $data['customer_id'])->first();
        $data['customer'] = $customer->nama;
        $lokasi = Penjualan::where('no_invoice', $data['no_referensi'])->first();
        $data['tanggal_invoice'] = $lokasi->tanggal_invoice;
        $data['lokasi'] = Lokasi::where('id', $lokasi->lokasi_id)->first();
        $data['produks'] = Produk_Terjual::with('komponen', 'produk')->where('no_do', $data['no_do'])->get();
        $data['driver'] = Karyawan::where('id', $data['driver'])->value('nama');
        $data['dibuat'] = User::where('id', $data['pembuat'])->value('name');
        $data['disetujui'] =User::where('id', $data['penyetuju'])->value('name');
        $data['diperiksa'] = User::where('id', $data['pemeriksa'])->value('name');
        // dd($data);
        $pdf = PDF::loadView('dopenjualan.view', $data);
    
        return $pdf->stream($data['no_do'] . '_DELIVERY ORDER.pdf');
    }

    public function audit($dopenjualan)
    {
        $dopenjualan = DeliveryOrder::find($dopenjualan);
        $produkjuals = Produk_Jual::all();
        $customers = Customer::all();
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $karyawans = Karyawan::where('jabatan', 'Driver')->where('lokasi_id', $karyawan->lokasi_id)->get();
        $kondisis = Kondisi::all();
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        return view('dopenjualan.audit', compact('kondisis','dopenjualan', 'produkjuals', 'karyawans', 'customers', 'cekInvoice'));
    }

    public function audit_update(Request $req)
    {
        // dd($req);
        $dopenjualanIds = $req->input('dopenjualan');
        $dopenjualan = DeliveryOrder::where('id', $dopenjualanIds)->first();

        $data = $req->except(['_token', '_method','dopenjualan', 'nama_produk', 'jumlah', 'satuan', 'keterangan', 'nama_produk2', 'jumlah2', 'satuan2', 'keterangan2', 'alasan']);
        //update bukti do
        if ($req->hasFile('file')) {
            // Simpan file baru
            $file = $req->file('file');
            $fileName = $req->no_do . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_do_penjualan/' . $fileName;
        
            // Optimize dan simpan file baru
            Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                ->save(storage_path('app/public/' . $filePath));
        
            // Hapus file lama
            if (!empty($dopenjualan->file)) {
                $oldFilePath = storage_path('app/public/' . $dopenjualan->file);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }
        
            // Verifikasi penyimpanan file baru
            if (File::exists(storage_path('app/public/' . $filePath))) {
                $data['file'] = $filePath;
            } else {
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        }

        //update data ttd
        $user = Auth::user();
        $jabatan = Karyawan::where('user_id', $user->id)->first();
        $jabatanpegawai = $jabatan->jabatan;
       

        if($dopenjualan->status == 'DIKONFIRMASI' && $user->hasRole(['Auditor'])){
            $data['pemeriksa'] = Auth::user()->id;
        }elseif($dopenjualan->status == 'DIKONFIRMASI' && $user->hasRole(['Finance'])){
            $data['penyetuju'] = Auth::user()->id;
        }elseif($user->hasRole(['KasirAdmin', 'AdminGallery', 'KasirOutlet'])) {
            $data['pembuat'] = Auth::user()->id;
        }
        $data['alasan_batal'] = $req->alasan;

        $invoice = Penjualan::where('no_invoice', $req->no_referensi)->first();
        $lokasi = Lokasi::where('id', $invoice->lokasi_id)->first();
        $lokasipengirim = Penjualan::where('no_invoice', $req->no_referensi)->value('lokasi_pengirim');
        $user = Auth::user();
        if($user->hasRole(['KasirOutlet', 'Auditor', 'Finance'])) {
            $lokasikirimdo = DeliveryOrder::where('no_do', $req->no_do)->first();
        }

        //cek produk urung bar
        if ($lokasi->tipe_lokasi == 1 && $invoice->distribusi == 'Dikirim' && $req->status == 'DIKONFIRMASI' && $user->hasRole(['KasirOutlet', 'AdminGallery', 'KasirGallery']) || $user->hasRole(['Auditor']) && $req->ubahapa == 'ubahsemua' || $user->hasRole(['Finance']) && $req->ubahapa == 'ubahsemua') {
            if (empty($req->nama_produk)) {
                $merged_nama_produk = $req->nama_produk2;
                $merged_jumlah = $req->jumlah2;
            } elseif (empty($req->nama_produk2)) {
                $merged_nama_produk = $req->nama_produk;
                $merged_jumlah = $req->jumlah;
            } else {
                $merged_nama_produk = array_merge($req->nama_produk, $req->nama_produk2);
                $merged_jumlah = array_merge($req->jumlah, $req->jumlah2);
            }
            $komponenJumlah = [];
            $produkJumlah = [];
    
            for ($i = 0; $i < count($merged_nama_produk); $i++) {
                $getProdukJual = Produk_Terjual::with('komponen')->where('id', $merged_nama_produk[$i])->first();
    
                if ($lokasi->tipe_lokasi == 1 && $invoice->distribusi == 'Dikirim') {
                    foreach ($getProdukJual->komponen as $komponen) {
                        $key = $komponen->kode_produk . '-' . $komponen->kondisi;
                        // dd($key);
                        if (!isset($komponenJumlah[$key])) {
                            $komponenJumlah[$key] = 0;
                        }
    
                        $komponenJumlah[$key] += $komponen->jumlah * $merged_jumlah[$i];
                    }
                } elseif ($lokasi->tipe_lokasi == 2 && $invoice->distribusi == 'Dikirim') {
                    $key = $getProdukJual->produk->kode;
                    
                    if (!isset($produkJumlah[$key])) {
                        $produkJumlah[$key] = 0;
                    }
    
                    $produkJumlah[$key] += $merged_jumlah[$i];
                }
            }

            if ($lokasi->tipe_lokasi == 1 && $invoice->distribusi == 'Dikirim') {
                foreach ($komponenJumlah as $key => $totalJumlah) {
                    $parts = explode('-', $key);
                    $kode_produk = $parts[0] . "-" . $parts[1];
                    $kondisi = $parts[2];
    
                    $stok = InventoryGallery::where('lokasi_id', $lokasipengirim)
                                            ->where('kode_produk', $kode_produk)
                                            ->where('kondisi_id', $kondisi)
                                            ->first();

                    if (!$stok || $stok->jumlah < $totalJumlah) {
                        return redirect()->back()->with('fail', 'Data Produk Tidak Ada atau Tidak Cukup Di Inventory');
                    }
                }
            } elseif ($lokasi->tipe_lokasi == 2 && $invoice->distribusi == 'Dikirim') {
                foreach ($produkJumlah as $kode_produk => $totalJumlah) {
                    $stok = InventoryOutlet::where('lokasi_id', $lokasikirimdo->lokasi_pengirim)
                                            ->where('kode_produk', $kode_produk)
                                            ->first();
    
                    if (!$stok || $stok->jumlah < $totalJumlah) {
                        return redirect()->back()->with('fail', 'Data Produk Tidak Cukup Di Inventory Outlet');
                    }
                }
            }
        }
        


        $update = DeliveryOrder::where('id', $dopenjualanIds)->update($data);

        if($req->status == 'DIBATALKAN')
        {
            // Mendapatkan data yang ada berdasarkan id untuk nama_produk
            $exist = Produk_Terjual::where('no_do', $req->no_do)->get();
            // dd($exist);
            $arrayExist = $exist->pluck('id')->toArray();

            $cek = Produk_Terjual::whereIn('id', $arrayExist)->where('no_do', $req->no_do)->get();
            $ceken = $cek->pluck('id')->toArray();
            $dikirim = Produk_Terjual::whereIn('id', $ceken)->get();
            $idkirim = $dikirim->pluck('no_invoice')->toArray();
            $kirim = Produk_Terjual::whereIn('id', $idkirim)->get();

            foreach ($exist as $item) {
                if($item->jenis != 'TAMBAHAN'){
                    $itemKirim = $kirim->where('id', $item->no_invoice)->first();
                    // dd($item->no_invoice);
                    $tambah = (int)$itemKirim->jumlah_dikirim + (int)$item->jumlah;
                    Produk_Terjual::where('id', $item->no_invoice)->update([
                        'jumlah_dikirim' => $tambah 
                    ]);
                }
            }
            return redirect(route('dopenjualan.index'))->with('success', 'Berhasil Mengupdate Data');
        }

        
        $exist = Produk_Terjual::whereIn('id', $req->nama_produk)->get();
        $arrayExist = $exist->pluck('id')->toArray();
        if(!empty($req->nama_produk2)) {
            $exist2 = Produk_Terjual::whereIn('id', $req->nama_produk2)->get();
            $arrayExist2 = $exist2->pluck('id')->toArray();
            $arrayCombined = array_merge($arrayExist, $arrayExist2);
        }else{
            $arrayCombined = $arrayExist;
        }
    
        $cek = Produk_Terjual::whereNotIn('id', $arrayCombined)->where('no_do', $req->no_do)->get();
        $ceken = $cek->pluck('id')->toArray();
        $dikirim = Produk_Terjual::whereIn('id', $ceken)->get();
        $idkirim = $dikirim->pluck('no_invoice')->toArray();
        $kirim = Produk_Terjual::whereIn('id', $idkirim)->get();

        if ($dikirim->isNotEmpty()) {
            foreach ($dikirim as $item) {
                //update stok pengurangan untuk auditor dan finance 
                if($user->hasRole(['Auditor', 'Finance'])) {
                    $komponens = Komponen_Produk_Terjual::where('produk_terjual_id', $item->id)->get();
                    foreach($komponens as $komponen) {
                        $stok = InventoryGallery::where('lokasi_id', $lokasi->id)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                        if($stok) {
                            $stok->jumlah += intval($komponen->jumlah) * intval($item->jumlah_dikirim);
                            $stok->update();
                        }
                    }
                    
                }
            }
            Produk_Terjual::whereIn('id', $ceken)->forceDelete();
        }

        if($req->nama_produk){
            for ($i = 0; $i < count($req->nama_produk); $i++) {
                $getProdukJual = Produk_Terjual::with('komponen')->where('id', $req->nama_produk[$i])->first();
                if ($getProdukJual) {
                    // Lakukan update jika data ditemukan
                    $produk_terjual = Produk_Terjual::where('id', $req->nama_produk[$i])->update([
                        'produk_jual_id' => $getProdukJual->produk_jual_id,
                        'no_do' => $req->no_do,
                        'jumlah' => $req->jumlah[$i],
                        'satuan' => $req->satuan[$i],
                        'keterangan' => $req->keterangan[$i]
                    ]);

                    //update jumlah dikirim
                    if($getProdukJual->jumlah != $req->jumlah[$i])
                    {
                        $intinvoice = $getProdukJual->no_invoice;
                        // pj invoice
                        $updateexist = Produk_Terjual::where('id', $intinvoice)->first();
                        //kurang invoice update invoice
                        $jumlahDikirim = (intval($getProdukJual->jumlah) - intval($req->jumlah[$i])) + intval($updateexist->jumlah_dikirim);
                        $updatecek = Produk_terjual::where('id', $updateexist->id)->update([
                            'jumlah_dikirim' => $jumlahDikirim
                        ]);   
                    }
                
                    if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    if($lokasi->tipe_lokasi == 1 && $invoice->distribusi == 'Dikirim')
                    {
                        //cek auditor
                        if($jabatanpegawai == 'admin' || $jabatanpegawai == 'kasir'){
                            foreach ($getProdukJual->komponen as $komponen ) {
                            
                                if($req->status == 'DIKONFIRMASI'){
                                    $stok = InventoryGallery::where('lokasi_id', $lokasipengirim)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                                    if(!$stok){
                                        return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                                    }elseif($stok){
                                        //pengurangan stok
                                        // dd($req->jumlah[$i]);
                                        $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($req->jumlah[$i]));
                                        $stok->update();
                                    }
                                }
                            }
                        }elseif($jabatanpegawai == 'finance' || $jabatanpegawai == 'auditor'){
                            foreach ($getProdukJual->komponen as $komponen ) {
                            
                                if($req->status == 'DIKONFIRMASI'){
                                    $stok = InventoryGallery::where('lokasi_id', $lokasipengirim)->where('kode_produk', $komponen->kode_produk)->where('kondisi_id', $komponen->kondisi)->first();
                                    if(!$stok){
                                        return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                                    }elseif($stok){
                                        //penambahan stok jika data sudah ada
                                        $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($getProdukJual->jumlah));
                                        $stok->update();
                                        //pengurangan stok
                                        $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($req->jumlah[$i]));
                                        $stok->update();
                                    }
                                }
                            }
                        }
                        
                    }elseif($lokasi->tipe_lokasi == 2 && $invoice->distribusi == 'Dikirim' && $req->status == 'DIKONFIRMASI')
                    {
                        if($req->status == 'DIKONFIRMASI' && $jabatanpegawai == 'admin' || $jabatanpegawai == 'kasir'){
                            //pengurangan inven outlet
                            $stok = InventoryOutlet::where('lokasi_id', $lokasikirimdo->lokasi_pengirim)
                                        ->where('kode_produk', $getProdukJual->produk->kode)
                                        ->first();
                        
                            if (!$stok) {
                                return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                            }
    
                            $stok->jumlah = intval($stok->jumlah) - intval($req->jumlah[$i]);
                            $stok->save();
                        }elseif($jabatanpegawai == 'finance' || $jabatanpegawai == 'auditor'){
                            $stok = InventoryOutlet::where('lokasi_id', $lokasikirimdo->lokasi_pengirim)
                                        ->where('kode_produk', $getProdukJual->produk->kode)
                                        ->first();
                            // dd($stok);
                        
                            if (!$stok) {
                                return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                            }
                            $stok->jumlah = intval($stok->jumlah) + intval($getProdukJual->jumlah);
                            $stok->save();
    
                            $stok->jumlah = intval($stok->jumlah) - intval($req->jumlah[$i]);
                            $stok->save();
                        }
                    }
                }
            }
        }

        if($req->nama_produk2){
            if (!empty($req->nama_produk2)) {
                // Simpan data tambahan
                foreach ($req->nama_produk2 as $index => $nama_produk) {
                    $getProdukJual = Produk_Terjual::with('komponen')->where('id', $nama_produk)->first();
                    // dd($nama_produk);
                    $produk_terjual = Produk_Terjual::where('id', $nama_produk)->update(
                        [
                            'produk_jual_id' => $getProdukJual->produk_jual_id,
                            'no_do' => $req->no_do,
                            'jumlah' => $req->jumlah2[$index],
                            'satuan' => $req->satuan2[$index],
                            'jenis' => 'TAMBAHAN',
                            'keterangan' => $req->keterangan2[$index]
                        ]
                    );
            
            
                    // Periksa lokasi dan status untuk penanganan stok
                    if ($lokasi->tipe_lokasi == 1 && $invoice->distribusi == 'Dikirim' && $req->status == 'DIKONFIRMASI') {
                        foreach ($getProdukJual->komponen as $komponen) {
                            // Pengurangan stok jika admin atau kasir
                            if ($jabatanpegawai == 'admin' || $jabatanpegawai == 'kasir') {
                                $stok = InventoryGallery::where('lokasi_id', $lokasipengirim)
                                    ->where('kode_produk', $komponen->kode_produk)
                                    ->where('kondisi_id', $komponen->kondisi)
                                    ->first();
            
                                if (!$stok) {
                                    return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                                }
            
                                $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($req->jumlah2[$index]));
                                $stok->save();
                            }elseif($jabatanpegawai == 'finance' || $jabatanpegawai == 'auditor'){
                                $stok = InventoryGallery::where('lokasi_id', $lokasipengirim)
                                    ->where('kode_produk', $komponen->kode_produk)
                                    ->where('kondisi_id', $komponen->kondisi)
                                    ->first();
            
                                if (!$stok) {
                                    return redirect()->back()->with('fail', 'Data Produk Belum Ada Di Inventory');
                                }
            
                                // Pengurangan dan penambahan stok jika bukan admin atau kasir
                                $stok->jumlah = intval($stok->jumlah) + (intval($komponen->jumlah) * intval($getProdukJual->jumlah));
                                $stok->save();
                                $stok->jumlah = intval($stok->jumlah) - (intval($komponen->jumlah) * intval($req->jumlah2[$index]));
                                $stok->save();
                            }
                        }
                    } elseif ($lokasi->tipe_lokasi == 2 && $invoice->distribusi == 'Dikirim' && $req->status == 'DIKONFIRMASI' && !$user->hasRole(['Auditor', 'Finance'])) {
                        foreach ($getProdukJual->komponen as $komponen) {
                            // Simpan komponen produk terjual
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
            
                        // Pengurangan stok jika admin atau kasir
                        if ($req->status == 'DIKONFIRMASI' && ($jabatanpegawai == 'admin' || $jabatanpegawai == 'kasir')) {
                            $stok = InventoryOutlet::where('lokasi_id', $lokasikirimdo->lokasi_pengirim)
                                ->where('kode_produk', $getProdukJual->produk->kode)
                                ->first();
            
                            if (!$stok) {
                                return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                            }
            
                            $stok->jumlah = intval($stok->jumlah) - intval($getProdukJual->jumlah);
                            $stok->save();
                        }elseif($jabatanpegawai == 'finance' || $jabatanpegawai == 'auditor'){
                            // Pengurangan dan penambahan stok jika bukan admin atau kasir
                            $stok = InventoryOutlet::where('lokasi_id', $lokasikirimdo->lokasi_pengirim)
                                ->where('kode_produk', $getProdukJual->produk->kode)
                                ->first();
            
                            if (!$stok) {
                                return redirect(route('inven_outlet.create'))->with('fail', 'Data Produk Belum Ada Di Inventory');
                            }
            
                            $stok->jumlah = intval($stok->jumlah) + intval($getProdukJual->jumlah);
                            $stok->save();
                            $stok->jumlah = intval($stok->jumlah) - intval($getProdukJual->jumlah);
                            $stok->save();
                        }
                    }
                }
            }
        }

        if($req->status == 'DIKONFIRMASI'){
            return redirect(route('dopenjualan.show', ['dopenjualan' => $dopenjualanIds]))->with('success', 'Berhasil Mengupdate Data');
        }elseif($req->status == 'TUNDA'){
            return redirect(route('dopenjualan.index'))->with('success', 'Berhasil Mengupdate data');
        }else{
            return redirect()->back()->with('fail', 'Gagal Mengupdate data');
        }

    }
}
