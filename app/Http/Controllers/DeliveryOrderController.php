<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\InventoryGallery;
use App\Models\Karyawan;
use App\Models\KembaliSewa;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kontrak;
use App\Models\Produk_Jual;
use App\Models\Produk_Terjual;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use Intervention\Image\Facades\Image;

class DeliveryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index_sewa(Request $req)
    {
        $query = DeliveryOrder::where('jenis_do', 'SEWA');
        if(Auth::user()->hasRole('AdminGallery')){
            $query->whereHas('kontrak', function($q) {
                $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
            });
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
        if(Auth::user()->hasRole('Finance') || Auth::user()->hasRole('Auditor')){
            $query->where('status', 'DIKONFIRMASI');
        }
        $customer = DeliveryOrder::select('customer_id')
            ->distinct()
            ->join('customers', 'delivery_orders.customer_id', '=', 'customers.id')
            ->when(Auth::user()->hasRole('AdminGallery'), function ($query) {
                return $query->where('customers.lokasi_id', Auth::user()->karyawans->lokasi_id);
            })
            ->where('tipe', 'sewa')
            ->orderBy('customers.nama')
        ->get();
        $driver = DeliveryOrder::select('driver')
            ->distinct()
            ->join('karyawans', 'delivery_orders.driver', '=', 'karyawans.id')
            ->when(Auth::user()->hasRole('AdminGallery'), function ($query) {
                return $query->where('karyawans.lokasi_id', Auth::user()->karyawans->lokasi_id);
            })
            ->orderBy('karyawans.nama')
        ->get();
        if ($req->ajax()) {
            $start = $req->input('start');
            $length = $req->input('length');
            $order = $req->input('order')[0]['column'];
            $dir = $req->input('order')[0]['dir'];
            $columnName = $req->input('columns')[$order]['data'];

            // search
            $search = $req->input('search.value');
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('no_do', 'like', "%$search%")
                    ->orWhere('no_referensi', 'like', "%$search%")
                    ->orWhere('tanggal_kirim', 'like', "%$search%")
                    ->orWhere('pic', 'like', "%$search%")
                    ->orWhere('status', 'like', "%$search%")
                    ->orWhere('tanggal_pembuat', 'like', "%$search%")
                    ->orWhere('tanggal_penyetuju', 'like', "%$search%")
                    ->orWhere('tanggal_pemeriksa', 'like', "%$search%")
                    ->orWhereHas('customer', function($c) use($search){
                        $c->where('nama', 'like', "%$search%");
                    })
                    ->orWhereHas('data_driver', function($c) use($search){
                        $c->where('nama', 'like', "%$search%");
                    });
                });
            }
    
            $query->orderBy($columnName, $dir);
            $recordsFiltered = $query->count();
            $tempData = $query->offset($start)->limit($length)->get();
    
            $currentPage = ($start / $length) + 1;
            $perPage = $length;
        
            $data = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
                $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                $item->tanggal_kirim_format = $item->tanggal_kirim == null ? '-' : tanggalindo($item->tanggal_kirim);
                $item->tanggal_pembuat_format = $item->tanggal_pembuat == null ? '-' : tanggalindo($item->tanggal_pembuat);
                $item->tanggal_penyetuju_format = $item->tanggal_penyetuju == null ? '-' : tanggalindo($item->tanggal_penyetuju);
                $item->tanggal_pemeriksa_format = $item->tanggal_pemeriksa == null ? '-' : tanggalindo($item->tanggal_pemeriksa);
                $item->nama_customer = $item->customer->nama;
                $item->nama_driver = $item->data_driver->nama;
                $item->userRole = Auth::user()->getRoleNames()->first();
                $item->hasKembaliSewa = KembaliSewa::where('no_sewa', $item->no_sewa)->where('status', 'DIKONFIRMASI')->exists();
                return $item;
            });

            return response()->json([
                'draw' => $req->input('draw'),
                'recordsTotal' => DeliveryOrder::where('jenis_do', 'SEWA')->count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);

        }
        return view('do_sewa.index', compact('driver', 'customer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create_sewa(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kontrak' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'route']);

        // data
        $kontrak = Kontrak::with('produk')->find($data['kontrak']);
        if(Auth::user()->hasRole('AdminGallery')){
            $drivers = Karyawan::where('jabatan', 'driver')->where('lokasi_id',Auth::user()->karyawans->lokasi_id)->get();
        } else {
            $drivers = Karyawan::where('jabatan', 'driver')->get();
        }
        $produkjuals = Produk_Jual::all();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $latestDO = DeliveryOrder::withTrashed()->orderByDesc('id')->first();

        // kode do
        if (!$latestDO) {
            $getKode = 'DVS' . date('Ymd') . '00001';
        } else {
            $lastDate = substr($latestDO->no_do, 3, 8);
            $todayDate = date('Ymd');
            if ($lastDate != $todayDate) {
                $getKode = 'DVS' . date('Ymd') . '00001';
            } else {
                $lastNumber = substr($latestDO->no_do, -5);
                $nextNumber = str_pad((int)$lastNumber + 1, 5, '0', STR_PAD_LEFT);
                $getKode = 'DVS' . date('Ymd') . $nextNumber;
            }
        }

        return view('do_sewa.create', compact('kontrak', 'drivers', 'produkjuals', 'getKode', 'produkSewa'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store_sewa(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'no_do' => 'required',
            'no_referensi' => 'required',
            'tanggal_kirim' => 'required|date',
            'driver' => 'required',
            'customer_id' => 'required',
            'pic' => 'required',
            'handphone' => 'required|numeric|digits_between:11,13',
            'alamat' => 'required',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $error);
        }

        $data = $req->except(['_token', '_method']);
        $data['jenis_do'] = 'SEWA';
        $data['status'] = 'TUNDA';
        $data['tanggal_pembuat'] = now();
        $data['pembuat'] = Auth::user()->id;

        DB::beginTransaction();
        try {
            // Check produk and quantity from sewa
            $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_referensi'])->first();
            $produkSewa = $kontrak->produk()->whereHas('produk', function ($q) {
                $q->whereColumn('jumlah_dikirim', '<', 'jumlah')->orWhereNull('jumlah_dikirim');
            })->get();

            // store file
            if ($req->hasFile('file')) {
                // Simpan file baru
                $file = $req->file('file');
                $fileName = $req->no_do . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = 'bukti_do_sewa/' . $fileName;
            
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
                    $data['file'] = $filePath;
                } else {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                }
            }

            // Cek input dengan sewa
            foreach ($produkSewa as $item) {
                for ($i = 0; $i < count($data['produk_id']); $i++) {
                    if ($data['produk_id'][$i] == $item->id) {
                        if ($data['jumlah'][$i] > $item->jumlah || $data['jumlah'][$i] > ($item->jumlah - $item->jumlah_dikirim)) {
                            DB::rollBack();
                            return redirect()->back()->withInput()->with('fail', 'Jumlah produk tidak sesuai dengan kontrak');
                        }
                    }
                }
            }

            // Ambil akumulasi komponen DO
            $dataDO = [];
            for ($i = 0; $i < count($data['produk_id']); $i++) {
                $produkTerjual = Produk_Terjual::find($data['produk_id'][$i])->komponen;
                foreach ($produkTerjual as $item) {
                    if (isset($dataDO[$item->kode_produk]['kondisi'])) {
                        $dataDO[$item->kode_produk]['jumlah'] += $item->jumlah * $data['jumlah'][$i];
                    } else {
                        $dataDO[$item->kode_produk] = ['kondisi' => $item->kondisi, 'jumlah' => $item->jumlah * $data['jumlah'][$i]];
                    }
                }
            }

            // Ambil akumulasi komponen tambahan DO
            $dataTambahanDO = [];
            if (isset($data['nama_produk2'][0])) {
                for ($i = 0; $i < count($data['produk_id2']); $i++) {
                    $produkTerjual = Produk_Jual::with('komponen')->find($data['produk_id2'][$i])->komponen;
                    foreach ($produkTerjual as $item) {
                        if (isset($dataTambahanDO[$item->kode_produk]['kondisi'])) {
                            $dataTambahanDO[$item->kode_produk]['jumlah'] += $item->jumlah * $data['jumlah2'][$i];
                        } else {
                            $dataTambahanDO[$item->kode_produk] = ['kondisi' => $item->kondisi, 'jumlah' => $item->jumlah * $data['jumlah2'][$i]];
                        }
                    }
                }

                // Penggabungan data DO dan tambahan
                foreach ($dataTambahanDO as $key => $value) {
                    if (isset($dataDO[$key]) && $dataDO[$key]['kondisi'] == $value['kondisi']) {
                        $dataDO[$key]['jumlah'] += $value['jumlah'];
                    } else {
                        $dataDO[$key] = $value;
                    }
                }
            }

            // Save data DO
            $check = DeliveryOrder::create($data);
            if (!$check) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }

            // Save produk DO
            for ($i = 0; $i < count($data['nama_produk']); $i++) {
                $getProdukTerjual = Produk_Terjual::with('komponen')->find($data['produk_id'][$i]);
                $produk_terjual = Produk_Terjual::create([
                    'produk_jual_id' => $getProdukTerjual->produk_jual_id,
                    'no_do' => $check->no_do,
                    'jumlah' => $data['jumlah'][$i],
                    'satuan' => $data['satuan'][$i],
                    'detail_lokasi' => $data['detail_lokasi'][$i]
                ]);

                if (!$produk_terjual) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }

                $getProdukSewa = Produk_Terjual::find($data['produk_id'][$i]);
                $getProdukSewa->jumlah_dikirim = ($getProdukSewa->jumlah_dikirim ?? 0) + intval($data['jumlah'][$i]);
                $getProdukSewa->update();

                foreach ($getProdukTerjual->komponen as $komponen) {
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

            // Save data tambahan
            if (isset($data['nama_produk2'][0])) {
                for ($i = 0; $i < count($data['nama_produk2']); $i++) {
                    $getProdukJual = Produk_Jual::with('komponen')->find($data['produk_id2'][$i]);
                    $produk_terjual = Produk_Terjual::create([
                        'produk_jual_id' => $getProdukJual->id,
                        'no_do' => $check->no_do,
                        'jumlah' => $data['jumlah2'][$i],
                        'satuan' => $data['satuan2'][$i],
                        'jenis' => 'TAMBAHAN',
                        'detail_lokasi' => $data['detail_lokasi2'][$i]
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

            DB::commit();
            return redirect(route('do_sewa.index'))->with('success', 'Data tersimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function show_sewa($deliveryOrder)
    {
        $data = DeliveryOrder::find($deliveryOrder);
        $produkJuals = Produk_Jual::all();
        $kontrak = Kontrak::where('no_kontrak', $data->kontrak->no_kontrak)->first();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $drivers = Karyawan::where('jabatan', 'DRIVER')->get();
        $riwayat = Activity::where('subject_type', DeliveryOrder::class)->where('subject_id', $deliveryOrder)->orderBy('id', 'desc')->get();
        return view('do_sewa.show', compact('data', 'produkJuals', 'drivers', 'riwayat', 'produkSewa'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function edit_sewa($deliveryOrder)
    {
        $data = DeliveryOrder::find($deliveryOrder);
        $produkJuals = Produk_Jual::all();
        $kontrak = Kontrak::where('no_kontrak', $data->kontrak->no_kontrak)->first();
        $produkSewa = $kontrak->produk()->whereHas('produk')->get();
        $drivers = Karyawan::where('jabatan', 'DRIVER')->get();
        $riwayat = Activity::where('subject_type', DeliveryOrder::class)->where('subject_id', $deliveryOrder)->orderBy('id', 'desc')->get();
        return view('do_sewa.edit', compact('data', 'produkJuals', 'drivers', 'riwayat', 'produkSewa'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function update_sewa(Request $req, $deliveryOrder)
    {
        if($req->konfirmasi){
            DB::beginTransaction();
            try {
                $do_sewa = DeliveryOrder::find($deliveryOrder);
                if (!$do_sewa) {
                    return redirect()->back()->withInput()->with('fail', 'Delivery order not found');
                }

                if ($req->konfirmasi == 'confirm') {
                    $do_sewa->status = 'DIKONFIRMASI';
                    $msg = 'Dikonfirmasi';

                    // Check inventory stock
                    foreach ($do_sewa->produk as $produk) {
                        foreach ($produk->komponen as $komponen) {
                            $inventory = InventoryGallery::where('kode_produk', $komponen->kode_produk)
                                ->where('kondisi_id', $komponen->kondisi)
                                ->where('lokasi_id', $do_sewa->kontrak->lokasi_id)
                                ->first();

                            if (!$inventory) {
                                DB::rollBack();
                                return redirect()->back()->withInput()->with('fail', 'Stok '.$komponen->kode_produk.' tidak ada');
                            }

                            $requiredQuantity = $komponen->jumlah * $produk->jumlah;
                            if ($inventory->jumlah < $requiredQuantity) {
                                DB::rollBack();
                                return redirect()->back()->withInput()->with('fail', 'Stok '.$komponen->kode_produk.' tidak mencukupi');
                            }

                            $sufficient = $inventory->jumlah - $requiredQuantity >= $inventory->min_stok;
                            if (!$sufficient) {
                                DB::rollBack();
                                return redirect()->back()->withInput()->with('fail', 'Stok '.$komponen->kode_produk.' dibawah minimum');
                            }
                        }
                    }

                    // Deduct stock if the user has the 'AdminGallery' role
                    if (Auth::user()->hasRole('AdminGallery')) {
                        foreach ($do_sewa->produk as $produk) {
                            foreach ($produk->komponen as $komponen) {
                                $inventory = InventoryGallery::where('kode_produk', $komponen->kode_produk)
                                    ->where('kondisi_id', $komponen->kondisi)
                                    ->where('lokasi_id', $do_sewa->kontrak->lokasi_id)
                                    ->first();
                                $inventory->jumlah -= intval($komponen->jumlah) * $produk->jumlah;
                                $inventory->save();
                            }
                        }
                    }

                } elseif ($req->konfirmasi == 'cancel') {
                    $do_sewa->status = 'BATAL';
                    $msg = 'Dibatalkan';
                } else {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'Status tidak sesuai');
                }

                // Update approval information based on user role
                if (Auth::user()->hasRole('Auditor')) {
                    $do_sewa->penyetuju = Auth::user()->id;
                    $do_sewa->tanggal_penyetuju = $req->tanggal_penyetuju;
                }

                if (Auth::user()->hasRole('Finance')) {
                    $do_sewa->pemeriksa = Auth::user()->id;
                    $do_sewa->tanggal_pemeriksa = $req->tanggal_pemeriksa;
                }

                $do_sewa->save();

                DB::commit();
                return redirect()->back()->withInput()->with('success', 'Data Berhasil ' . $msg);

            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal mengubah status: ' . $e->getMessage());
            }
        } else {
            // Validation
            $validator = Validator::make($req->all(), [
                'no_do' => 'required',
                'no_referensi' => 'required',
                'tanggal_kirim' => 'required',
                'driver' => 'required',
                'customer_id' => 'required',
                'pic' => 'required',
                'handphone' => 'required|numeric|digits_between:11,13',
                'alamat' => 'required',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
            }

            // Start DB transaction
            DB::beginTransaction();
            try {
                $deliveryOrder = DeliveryOrder::find($deliveryOrder);
                $data = $req->except(['_token', '_method']);
                // store file
                if ($req->hasFile('file')) {
                    // Simpan file baru
                    $file = $req->file('file');
                    $fileName = $req->no_do . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                    $filePath = 'bukti_do_sewa/' . $fileName;
                
                    // Optimize dan simpan file baru
                    Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                        ->save(storage_path('app/public/' . $filePath));
                
                    // Hapus file lama
                    if (!empty($deliveryOrder->file)) {
                        $oldFilePath = storage_path('app/public/' . $deliveryOrder->file);
                        if (File::exists($oldFilePath)) {
                            File::delete($oldFilePath);
                        }
                    }
                
                    // Verifikasi penyimpanan file baru
                    if (File::exists(storage_path('app/public/' . $filePath))) {
                        $data['file'] = $filePath;
                    } else {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                    }
                }

                // Check produk and quantity from sewa
                $kontrak = Kontrak::with('produk')->where('no_kontrak', $data['no_referensi'])->first();
                $produkSewa = $kontrak->produk()->whereHas('produk')->get();

                // Cek input dengan sewa
                $sesuaiKontrak = true;
                $inputProduk = [];
                for ($i = 0; $i < count($data['nama_produk']); $i++) {
                    $kodeProduk = $data['nama_produk'][$i];
                    $jumlahProduk = $data['jumlah'][$i];

                    if (!isset($inputProduk[$kodeProduk])) {
                        $inputProduk[$kodeProduk] = 0;
                    }

                    $inputProduk[$kodeProduk] += $jumlahProduk;
                }

                $produkSewa->each(function ($produk) use (&$inputProduk, &$sesuaiKontrak) {
                    $kodeProduk = $produk->produk->kode;

                    if (isset($inputProduk[$kodeProduk])) {
                        $inputProduk[$kodeProduk] -= $produk->jumlah;
                        if ($inputProduk[$kodeProduk] < 0) {
                            $sesuaiKontrak = false;
                            return false;
                        }
                        if ($inputProduk[$kodeProduk] == 0) {
                            unset($inputProduk[$kodeProduk]);
                        }
                    }
                });

                if (!empty($inputProduk)) {
                    $sesuaiKontrak = false;
                }

                if (!$sesuaiKontrak) {
                    return redirect()->back()->withInput()->with('fail', 'Produk tidak sesuai kontrak');
                }

                // Save data do
                $deliveryOrder->update($data);

                $data_do = DeliveryOrder::find($deliveryOrder->id);
                $dataProduk = Produk_Terjual::where('no_do', $data_do->no_do)->get();

                // Delete old data
                foreach ($dataProduk as $item) {
                    if ($data_do->status == 'DIKONFIRMASI') {
                        foreach ($item->komponen as $komponen) {
                            // Pengembalian stok
                            $inventory = InventoryGallery::where('kode_produk', $komponen->kode_produk)
                                ->where('kondisi_id', $komponen->kondisi)
                                ->where('lokasi_id', $kontrak->lokasi_id)
                                ->first();
                            $inventory->jumlah += (intval($komponen->jumlah) * intval($item->jumlah));
                            $inventory->update();
                        }
                    }
                    Komponen_Produk_Terjual::where('produk_terjual_id', $item->id)->forceDelete();
                    $item->forceDelete();
                }

                // Save new produk do
                for ($i = 0; $i < count($data['nama_produk']); $i++) {
                    $getProdukTerjual = Produk_Terjual::with('komponen')->find($data['produk_id'][$i]);
                    $produk_terjual = Produk_Terjual::create([
                        'produk_jual_id' => $getProdukTerjual->produk_jual_id,
                        'no_do' => $data_do->no_do,
                        'jumlah' => $data['jumlah'][$i],
                        'satuan' => $data['satuan'][$i],
                        'detail_lokasi' => $data['detail_lokasi'][$i]
                    ]);
                    $getProdukSewa = Produk_Terjual::find($data['produk_id'][$i]);
                    $getProdukSewa->jumlah_dikirim = ($getProdukSewa->jumlah_dikirim ?? 0) + intval($data['jumlah'][$i]);
                    $getProdukSewa->update();

                    if (!$produk_terjual) {
                        DB::rollBack();
                        return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                    }
                    foreach ($getProdukTerjual->komponen as $komponen) {
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

                        // Pengurangan stok
                        if ($data_do->status == 'DIKONFIRMASI') {
                            $inventory = InventoryGallery::where('kode_produk', $komponen_produk_terjual->kode_produk)
                                ->where('kondisi_id', $komponen_produk_terjual->kondisi)
                                ->where('lokasi_id', $kontrak->lokasi_id)
                                ->first();
                            $inventory->jumlah -= (intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah));
                            $inventory->update();
                        }
                    }
                }

                // Save additional data
                if (isset($data['nama_produk2'][0])) {
                    for ($i = 0; $i < count($data['nama_produk2']); $i++) {
                        $getProdukJual = Produk_Jual::with('komponen')->find($data['produk_id2'][$i]);
                        $produk_terjual = Produk_Terjual::create([
                            'produk_jual_id' => $getProdukJual->id,
                            'no_do' => $data_do->no_do,
                            'jumlah' => $data['jumlah2'][$i],
                            'satuan' => $data['satuan2'][$i],
                            'jenis' => 'TAMBAHAN',
                            'keterangan' => $data['keterangan2'][$i]
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

                            // Pengurangan stok
                            if ($data_do->status == 'DIKONFIRMASI') {
                                $inventory = InventoryGallery::where('kode_produk', $komponen_produk_terjual->kode_produk)
                                    ->where('kondisi_id', $komponen_produk_terjual->kondisi)
                                    ->where('lokasi_id', $kontrak->lokasi_id)
                                    ->first();
                                $inventory->jumlah -= (intval($komponen_produk_terjual->jumlah) * intval($produk_terjual->jumlah));
                                $inventory->update();
                            }
                        }
                    }
                }

                DB::commit();
                return redirect(route('do_sewa.index'))->with('success', 'Data tersimpan');
            } catch (\Exception $e) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal update data: ' . $e->getMessage());
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DeliveryOrder  $deliveryOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy_sewa($deliveryOrder)
    {
        $data = DeliveryOrder::find($deliveryOrder);
        if(!$data) return response()->json(['msg' => 'Delivery Order tidak ditemukan']);
        $data->status = 'BATAL';
        $check = $data->update();
        if(!$check) return response()->json(['msg' => 'Gagal membatalkan delivery order']);
        return response()->json(['msg' => 'Berhasil membatalkan delivery order']);
    }

    public function getProdukDo(Request $req)
    {
        $produksDO = Produk_Terjual::with('komponen', 'produk')->where('no_do', $req->no_do)->get();
        if(!$produksDO) return response()->json('Not found', 404);
        return response()->json($produksDO);
    }
}
