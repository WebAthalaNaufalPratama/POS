<?php

namespace App\Http\Controllers;

use App\Models\Invoicepo;
use App\Models\InvoiceSewa;
use App\Models\Mutasiindens;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use App\Models\Rekening;
use App\Models\Customer;
use App\Models\Karyawan;
use App\Models\Lokasi;
use App\Models\Returinden;
use App\Models\Returpembelian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class PembayaranController extends Controller
{

    public function index(Request $req)
    {
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->first();

        // Determine the query based on the location type
        if ($lokasi->lokasi->tipe_lokasi == 2) {
            $penjualanIds = Penjualan::where('no_invoice', 'LIKE', 'IPO%')
                                    ->where('lokasi_id', $lokasi->lokasi_id)
                                    ->pluck('id')
                                    ->toArray();
            $query = Pembayaran::whereNotNull('invoice_penjualan_id')
                            ->whereIn('invoice_penjualan_id', $penjualanIds)
                            ->where('no_invoice_bayar', 'LIKE', 'BOT%');
        } elseif ($lokasi->lokasi->tipe_lokasi == 1) {
            $penjualanIds = Penjualan::where('no_invoice', 'LIKE', 'INV%')
                                    ->where('lokasi_id', $lokasi->lokasi_id)
                                    ->pluck('id')
                                    ->toArray();
            $query = Pembayaran::whereNotNull('invoice_penjualan_id')
                            ->whereIn('invoice_penjualan_id', $penjualanIds)
                            ->where('no_invoice_bayar', 'LIKE', 'BYR%');
        } else {
            $query = Pembayaran::query(); // Change to Pembayaran to match the default case
        }

        // Apply additional filters based on request parameters
        if ($req->metode) {
            $query->where('cara_bayar', $req->input('metode'));
        }
        if ($req->dateStart) {
            $query->where('tanggal_bayar', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_bayar', '<=', $req->input('dateEnd'));
        }

        if ($req->ajax()) {
            // Handle sorting
            $orderColumn = $req->input('columns.' . $req->input('order.0.column') . '.data');
            $orderDirection = $req->input('order.0.dir');

            $totalRecords = $query->count();

            // Apply sorting
            $query->orderBy($orderColumn, $orderDirection);

            // Apply pagination
            $data = $query->skip($req->input('start'))
                        ->take($req->input('length'))
                        ->get();

                $data = $data->map(function ($item) {
                $nominal = str_replace('.', '', $item->nominal); // Remove thousands separator
                $nominal = str_replace(',', '.', $nominal); // Replace decimal comma with dot

                return [
                    'id' => $item->id,
                    'no_invoice' => $item->penjualan->no_invoice,
                    'no_invoice_bayar' => $item->no_invoice_bayar,
                    'cara_bayar' => $item->cara_bayar,
                    'nominal' => $nominal, 
                    'rekening' => $item->rekening ? $item->rekening->bank : 'Pembayaran Cash',
                    'tanggal_bayar' => $item->tanggal_bayar ? \Carbon\Carbon::parse($item->tanggal_bayar)->format('d F Y') : '',
                    'status_bayar' => $item->status_bayar,
                    'action' => '<a href="' . route('pembayaran.edit', ['pembayaran' => $item->id]) . '" class="btn btn-sm btn-primary">Edit</a>'
                ];
            });
                        
                        

            return response()->json([
                'draw' => intval($req->input('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords, // Adjust if using a filter
                'data' => $data
            ]);
        }

        return view('pembayaran.index');
    }

    public function edit($pembayaran)
    {
        // dd($pembayaran);
        $bankpens = Rekening::all();
        $data = Pembayaran::with('rekening')->find($pembayaran);
        $penjualan = Penjualan::where('id', $data->invoice_penjualan_id)->first();
        return view('pembayaran.edit', compact('data', 'bankpens', 'penjualan'));
    }

    public function update(Request $req, $pembayaran)
    {
        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = $req->no_invoice_bayar . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_pembayaran_penjualan/' . $fileName;

            $do = Pembayaran::find($pembayaran);

            // Optimize dan simpan file baru
            Image::make($file)->encode($file->getClientOriginalExtension(), 70)
            ->save(storage_path('app/public/' . $filePath));
    
            // Hapus file lama
            if (!empty($do->bukti)) {
                $oldFilePath = storage_path('app/public/' . $do->bukti);
                if (File::exists($oldFilePath)) {
                    File::delete($oldFilePath);
                }
            }
        
            // Verifikasi penyimpanan file baru
            if (File::exists(storage_path('app/public/' . $filePath))) {
                $data['bukti'] = $filePath;
            } else {
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }

            $penjualan = Penjualan::find($do->invoice_penjualan_id);

            
            $resetTagihan = $penjualan->sisa_bayar + $do->nominal;
            $penjualan->update([
                'sisa_bayar' => $resetTagihan,
            ]);
            $cekTotalTagihan = $penjualan->sisa_bayar - $req->nominal;
            $penjualan->update([
                'sisa_bayar' => $cekTotalTagihan,
            ]);
            $cek = $penjualan->sisa_bayar;
            // dd($cek);
            if ($cek <= 0) {
                $data['status_bayar'] = 'LUNAS';
                $customer = Customer::where('id', $penjualan->id_customer)->update([
                    'status_piutang' => 'LUNAS',
                ]);
            } else {
                $data['status_bayar'] = 'BELUM LUNAS';
            }
            $do->cara_bayar = $req->cara_bayar;
            $do->nominal = $req->nominal;
            $do->rekening_id = $req->rekening_id;
            $do->tanggal_bayar = $req->tanggal_bayar;
            $do->bukti = $data['bukti'];
            $do->status_bayar = $data['status_bayar'];
            $do->update();
            return redirect()->back()->with('success', 'Edit Pembayaran Berhasil');
        } else {
            return redirect()->back()->with('fail', 'Gagal mengedit');
        }
    }

    private function parseRupiahToNumber($rupiah)
    {
        $cleaned = str_replace('.', '', $rupiah); 
        $cleaned = str_replace(',', '.', $cleaned); 

        return (float) $cleaned; 
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'invoice_penjualan_id' => 'required',
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

        // if ($req->hasFile('bukti')) {
        //     $file = $req->file('bukti');
        //     $fileName = time() . '_' . $file->getClientOriginalName();
        //     $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
        //     $data['bukti'] = $filePath;
        // }

        $data['nominal'] = $this->parseRupiahToNumber($data['nominal']);
        $penjualan = Penjualan::find($req->invoice_penjualan_id);
        
        if ($penjualan) {
            // store file
            if ($req->hasFile('bukti')) {
                // Simpan file baru
                $file = $req->file('bukti');
                $fileName = $penjualan->no_invoice . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = 'bukti_pembayaran_penjualan/' . $fileName;
            
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
                    $data['bukti'] = $filePath;
                } else {
                    return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                }
            }

            $cekTotalTagihan = $penjualan->sisa_bayar - $data['nominal'];
            $penjualan->update([
                'sisa_bayar' => $cekTotalTagihan,
            ]);
            $cek = $penjualan->sisa_bayar;
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
            return redirect(route('penjualan.payment', ['penjualan' => $req->invoice_penjualan_id]))->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->back()->with('fail', 'Gagal menyimpan data');
        }
    }          

    public function index_sewa(Request $req){
        $query = Pembayaran::with('sewa', 'rekening')->whereNotNull('invoice_sewa_id');
        if(Auth::user()->hasRole('AdminGallery')){
            $query->whereHas('sewa', function($q) {
                $q->whereHas('kontrak', function($p) {
                    $p->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                });
            });
        }
        if ($req->metode) {
            $query->where('cara_bayar', $req->input('metode'));
        }
        if ($req->dateStart) {
            $query->where('tanggal_bayar', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_bayar', '<=', $req->input('dateEnd'));
        }
        
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
                    $q->where('no_invoice_bayar', 'like', "%$search%")
                    ->orWhere('cara_bayar', 'like', "%$search%")
                    ->orWhere('tanggal_bayar', 'like', "%$search%")
                    ->orWhere('nominal', 'like', "%$search%")
                    ->orWhereHas('sewa', function($c) use($search){
                        $c->where('no_invoice', 'like', "%$search%")
                        ->orWhere('no_sewa', 'like', "%$search%");
                    })
                    ->orWhereHas('rekening', function($c) use($search){
                        $c->where('nama_akun', 'like', "%$search%");
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
                $item->tanggal_bayar_format = $item->tanggal_bayar == null ? '-' : tanggalindo($item->tanggal_bayar);
                $item->no_kontrak = $item->sewa->no_sewa;
                $item->no_invoice_tagihan = $item->sewa->no_invoice;
                $item->nominal_format = formatRupiah($item->nominal);
                $item->nama_rekening = $item->rekening->nama_akun ?? '-';
                $item->userRole = Auth::user()->getRoleNames()->first();
                $item->cara_bayar = ucfirst($item->cara_bayar);
                return $item;
            });

            // search
            $search = $req->input('search.value');
            if (!empty($search)) {
                $data = $data->filter(function($item) use ($search) {
                    return stripos($item->no_invoice_bayar, $search) !== false
                        || stripos($item->cara_bayar, $search) !== false
                        || stripos($item->tanggal_bayar, $search) !== false
                        || stripos($item->nominal, $search) !== false
                        || stripos($item->no_kontrak, $search) !== false
                        || stripos($item->no_invoice_tagihan, $search) !== false
                        || stripos($item->nama_rekening, $search) !== false
                        || stripos($item->nama_perangkai, $search) !== false;
                });
            }

            return response()->json([
                'draw' => $req->input('draw'),
                'recordsTotal' => Pembayaran::whereNotNull('invoice_sewa_id')->count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);
        }

        $bankpens = Rekening::get();
        return view('pembayaran_sewa.index', compact('bankpens'));
    }

    public function store_sewa(Request $req) {
        // Validasi
        $validator = Validator::make($req->all(), [
            'invoice_sewa_id' => 'required',
            'no_invoice_bayar' => 'required',
            'nominal' => 'required',
            'tanggal_bayar' => 'required',
        ]);
    
        if ($validator->fails()) {
            $error = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $error);
        }
    
        $data = $req->except(['_token', '_method', 'bukti']);
        $invoice_tagihan = InvoiceSewa::find($data['invoice_sewa_id']);
    
        // Cek sisa bayar
        if ($invoice_tagihan->sisa_bayar <= 0) {
            return redirect()->back()->withInput()->with('fail', 'Invoice sudah lunas');
        }
    
        DB::beginTransaction();
        try {
            // Update sisa invoice
            $totalPembayaran = Pembayaran::where('invoice_sewa_id', $data['invoice_sewa_id'])->sum('nominal');
            $invoice_tagihan->sisa_bayar = intval($invoice_tagihan->total_tagihan) - intval($invoice_tagihan->dp) - intval($totalPembayaran) - intval($data['nominal']);
    
            if (!$invoice_tagihan->save()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
    
            // store file
            if ($req->hasFile('bukti')) {
                // Simpan file baru
                $file = $req->file('bukti');
                $fileName = $invoice_tagihan->no_invoice . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = 'bukti_pembayaran_sewa/' . $fileName;
            
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
                    $data['bukti'] = $filePath;
                } else {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                }
            }

            $new_invoice_tagihan = InvoiceSewa::find($data['invoice_sewa_id']);
            $data['status_bayar'] = $new_invoice_tagihan->sisa_bayar <= 0 ? 'LUNAS' : 'BELUM LUNAS';
    
            $Invoice = Pembayaran::whereRaw('LENGTH(no_invoice_bayar) = 16')->latest()->first();
            if (!$Invoice) {
                $data['no_invoice_bayar'] = 'BYR' . date('Ymd') . '00001';
            } else {
                $lastDate = substr($Invoice->no_invoice_bayar, 3, 8);
                $todayDate = date('Ymd');
                if ($lastDate != $todayDate) {
                    $data['no_invoice_bayar'] = 'BYR' . date('Ymd') . '00001';
                } else {
                    $lastNumber = substr($Invoice->no_invoice_bayar, -5);
                    $nextNumber = str_pad((int)$lastNumber + 1, 5, '0', STR_PAD_LEFT);
                    $data['no_invoice_bayar'] = 'BYR' . date('Ymd') . $nextNumber;
                }
            }

            $pembayaran = Pembayaran::create($data);
            if (!$pembayaran) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
    
            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function show_sewa(Request $req, $id)
    {
        $data = Pembayaran::with('rekening', 'sewa', 'sewa.kontrak')->find($id);
        if(!$data) return response()->json('Data tidak ditemukan', 404);
        return response()->json($data);
    }

    public function update_sewa(Request $req, $id) {
        // Validasi
        $validator = Validator::make($req->all(), [
            'invoice_sewa_id' => 'required',
            'no_invoice_bayar' => 'required',
            'nominal' => 'required',
            'tanggal_bayar' => 'required',
        ]);
    
        if ($validator->fails()) {
            $error = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $error);
        }
    
        $data = $req->except(['_token', '_method', 'bukti']);
        $invoice_tagihan = InvoiceSewa::find($data['invoice_sewa_id']);
        $pembayaran = Pembayaran::find($id);
    
        DB::beginTransaction();
        try {
            // Update sisa invoice
            $invoice_tagihan->sisa_bayar = intval($invoice_tagihan->sisa_bayar) + intval($pembayaran->nominal) - intval($data['nominal']);
    
            if (!$invoice_tagihan->save()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
    
            // store file
            if ($req->hasFile('bukti')) {
                // Simpan file baru
                $file = $req->file('bukti');
                $fileName = $invoice_tagihan->no_invoice . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = 'bukti_pembayaran_sewa/' . $fileName;
            
                // Optimize dan simpan file baru
                Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                    ->save(storage_path('app/public/' . $filePath));
            
                // Hapus file lama
                if (!empty($pembayaran->bukti)) {
                    $oldFilePath = storage_path('app/public/' . $pembayaran->bukti);
                    if (File::exists($oldFilePath)) {
                        File::delete($oldFilePath);
                    }
                }
            
                // Verifikasi penyimpanan file baru
                if (File::exists(storage_path('app/public/' . $filePath))) {
                    $data['bukti'] = $filePath;
                } else {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                }
            }
    
            $data['status_bayar'] = $invoice_tagihan->sisa_bayar <= 0 ? 'LUNAS' : 'BELUM LUNAS';
    
            if (!$pembayaran->update($data)) {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
            $this->updateStatusAll($data['invoice_sewa_id'], 'Sewa');
            DB::commit();
            return redirect()->back()->with('success', 'Pembayaran berhasil');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store_bayar_po(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'invoice_purchase_id' => 'required',
            'no_invoice_bayar' => 'required',
            'id_po' => 'required',
            'type' => 'required',
            'tanggal_bayar' => 'required',
            'nominal' => 'required',
            'bukti' => 'required|file',
            'metode' => 'required', // Validasi untuk metode pembayaran
        ]);
        
        // Validasi input dari request
        if ($validator->fails()) {
            // Mengembalikan respon jika validasi gagal
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }

        $datainv = Invoicepo::find($req->invoice_purchase_id);

        if (!$datainv) {
            // Mengembalikan respon jika invoice tidak ditemukan
            return redirect()->back()->with('fail', 'Tagihan tidak ditemukan.');
        }
        if ($req->nominal > $datainv->sisa) {
            return redirect()->back()->with('fail', 'Nominal melebihi sisa tagihan');
        }
    
    
        if ($datainv->sisa === 0) {
            // Mengembalikan respon jika tagihan sudah lunas
            return redirect()->back()->with('success', 'Tagihan sudah Lunas');
        }
    
        // Mengolah metode pembayaran
        $metode = $req->input('metode');
        if (strpos($metode, 'transfer-') === 0) {
            $cara_bayar = 'transfer';
            $rekening_id = str_replace('transfer-', '', $metode);
        } else {
            $cara_bayar = $metode;
            $rekening_id = null;
        }
    
        // Menyiapkan data untuk disimpan
        $data = $req->except(['_token', '_method', 'bukti', 'status_bayar', 'metode']);
        $data['cara_bayar'] = $cara_bayar;
        $data['rekening_id'] = $rekening_id;
    
        // if ($req->hasFile('bukti')) {
        //     // Mengunggah file bukti pembayaran
        //     $file = $req->file('bukti');
        //     $fileName = time() . '_' . $file->getClientOriginalName();
        //     $filePath = $file->storeAs('bukti_pembayaran_purchase', $fileName, 'public');
        //     $data['bukti'] = $filePath;
        // }
        // store file
        if ($req->hasFile('bukti')) {
            // Simpan file baru
            $file = $req->file('bukti');
            $fileName = $datainv->no_inv . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_pembayaran_purchase/' . $fileName;
        
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
                $data['bukti'] = $filePath;
            } else {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        }
    
        // Menghitung sisa tagihan setelah pembayaran
        $cekTotalTagihan = $datainv->sisa - $req->nominal;
        $datainv->update(['sisa' => $cekTotalTagihan]);

        if ($datainv->dp === 0) {
            $datainv->update(['dp' => $req->nominal]);
        }

        $lastPayment = Pembayaran::where('invoice_purchase_id', $req->invoice_purchase_id)
        ->orderBy('created_at', 'asc')
        ->get(); // Menggunakan get() untuk mendapatkan semua hasil

        // Menghitung jumlah cicilan sebelumnya
        $cicilanSebelumnya = $lastPayment->count();

        // Menentukan status pembayaran berdasarkan sisa tagihan
        if ($cekTotalTagihan <= 0) {
            $data['status_bayar'] = 'LUNAS';
        } else {
            $data['status_bayar'] = 'Cicilan ke-' . ($cicilanSebelumnya + 1);   
        }
    
        // Menyimpan data pembayaran
        $pembayaran = Pembayaran::create($data);
    
        if ($cekTotalTagihan <= 0) {
            // Mengembalikan respon jika tagihan sudah lunas setelah pembayaran
            return redirect()->back()->with('success', 'Tagihan sudah Lunas');
        }


        $type = $req->input('type');
        if ($type === 'pembelian') {

            if ($pembayaran) {
                // Mengembalikan respon sukses jika data pembayaran berhasil disimpan
                return redirect(route('invoice.edit', ['datapo' => $req->id_po, 'type' => $type, 'id' => $datainv->id]))->with('success', 'Data Berhasil Disimpan');
            } else {
                // Mengembalikan respon gagal jika penyimpanan data pembayaran gagal
                return redirect()->back()->with('fail', 'Gagal menyimpan data');
            }
        }elseif ($type === 'poinden') {
            if ($pembayaran) {
                // Mengembalikan respon sukses jika data pembayaran berhasil disimpan
                return redirect(route('invoice.edit', ['datapo' => $req->id_po, 'type' => $type, 'id' => $datainv->id]))->with('success', 'Data Berhasil Disimpan');
            } else {
                // Mengembalikan respon gagal jika penyimpanan data pembayaran gagal
                return redirect()->back()->with('fail', 'Gagal menyimpan data');
            }

        }
    
    }
 
    public function store_bayar_mutasi(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'mutasiinden_id' => 'required',
            'no_invoice_bayar' => 'required',
            'tanggal_bayar' => 'required',
            'nominal' => 'required',
            'buktitf' => 'required|file',
            'metode' => 'required', // Validasi untuk metode pembayaran
        ]);
        
        // Validasi input dari request
        if ($validator->fails()) {
            // Mengembalikan respon jika validasi gagal
            $errors = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $errors);
        }

        $datamutasi = Mutasiindens::find($req->mutasiinden_id);

        if (!$datamutasi) {
            // Mengembalikan respon jika invoice tidak ditemukan
            return redirect()->back()->with('fail', 'Tagihan tidak ditemukan.');
        }
        if ($req->nominal > $datamutasi->sisa_bayar) {
            return redirect()->back()->with('fail', 'Nominal melebihi sisa tagihan');
        }
    
    
        if ($datamutasi->sisa_bayar === 0) {
            // Mengembalikan respon jika tagihan sudah lunas
            return redirect()->back()->with('success', 'Tagihan sudah Lunas');
        }
    
        // Mengolah metode pembayaran
        $metode = $req->input('metode');
        if (strpos($metode, 'transfer-') === 0) {
            $cara_bayar = 'transfer';
            $rekening_id = str_replace('transfer-', '', $metode);
        } else {
            $cara_bayar = $metode;
            $rekening_id = null;
        }
    
        // Menyiapkan data untuk disimpan
        $data = $req->except(['_token', '_method', 'bukti', 'status_bayar', 'metode']);
        $data['cara_bayar'] = $cara_bayar;
        $data['rekening_id'] = $rekening_id;
    
        // if ($req->hasFile('buktitf')) {
        //     // Mengunggah file bukti pembayaran
        //     $file = $req->file('buktitf');
        //     $fileName = time() . '_' . $file->getClientOriginalName();
        //     $filePath = $file->storeAs('bukti_pembayaran_mutasiinden', $fileName, 'public');
        //     $data['bukti'] = $filePath; 
        // }
        // store file
        if ($req->hasFile('buktitf')) {
            // Simpan file baru
            $file = $req->file('buktitf');
            $fileName = $datamutasi->no_mutasi . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = 'bukti_pembayaran_mutasiinden/' . $fileName;
        
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
                $data['buktitf'] = $filePath;
            } else {
                DB::rollBack();
                return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
            }
        }
    
        // Menghitung sisa tagihan setelah pembayaran
        $cekTotalTagihan = $datamutasi->sisa_bayar- $req->nominal;
        $datamutasi->update(['sisa_bayar' => $cekTotalTagihan]);

        $lastPayment = Pembayaran::where('mutasiinden_id', $req->mutasiinden_id)
        ->orderBy('created_at', 'asc')
        ->get(); // Menggunakan get() untuk mendapatkan semua hasil

        // Menghitung jumlah cicilan sebelumnya
        $cicilanSebelumnya = $lastPayment->count();

        // Menentukan status pembayaran berdasarkan sisa tagihan
        if ($cekTotalTagihan <= 0) {
            $data['status_bayar'] = 'LUNAS';
        } else {
            $data['status_bayar'] = 'Cicilan ke-' . ($cicilanSebelumnya + 1);   
        }

        // Menentukan status pembayaran berdasarkan sisa tagihan
        // $data['status_bayar'] = $cekTotalTagihan <= 0 ? 'LUNAS' : 'BELUM LUNAS';
    
        // Menyimpan data pembayaran
        $pembayaran = Pembayaran::create($data);
    
        if ($cekTotalTagihan <= 0) {
            // Mengembalikan respon jika tagihan sudah lunas setelah pembayaran
            return redirect()->back()->with('success', 'Tagihan sudah Lunas');
        }
            if ($pembayaran) {
                // Mengembalikan respon sukses jika data pembayaran berhasil disimpan
                return redirect(route('mutasiindengh.show',['mutasiIG' => $req->mutasiinden_id]))->with('success', 'Data Berhasil Disimpan');
            } else {
                // Mengembalikan respon gagal jika penyimpanan data pembayaran gagal
                return redirect()->back()->with('fail', 'Gagal menyimpan data');
            }
       
    
    }

    public function index_po(Request $req){
        // start datatable keluar
            if ($req->ajax() && $req->table == 'keluar') {
                $query = Pembayaran::where(function($query) {
                    $query->where('no_invoice_bayar', 'LIKE', '%BYPO%')
                        ->orWhere('no_invoice_bayar', 'LIKE', '%BYMI%');
                });

                if ($req->metode_keluar) {
                    $query->where('cara_bayar', $req->input('metode_keluar'));
                }
                if ($req->dateStart) {
                    $query->where('tanggal_bayar', '>=', $req->input('dateStart'));
                }
                if ($req->dateEnd) {
                    $query->where('tanggal_bayar', '<=', $req->input('dateEnd'));
                }

                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                $query->orderBy($columnName, $dir);
                $recordsFiltered = $query->count();
                $tempData = $query->offset($start)->limit($length)->get();
        
                $currentPage = ($start / $length) + 1;
                $perPage = $length;
            
                $data = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
                    $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                    $item->userRole = Auth::user()->getRoleNames()->first();
                    $item->tanggal_bayar_format = tanggalindo($item->tanggal_bayar);
                    $item->nominal_format = formatRupiah($item->nominal);
                    if ($item->po){
                        $item->no_referensi = $item->po->pembelian ? $item->po->pembelian->no_po : ($item->po->poinden ? $item->po->poinden->no_po : '');
                    } elseif($item->mutasiinden){
                        $item->no_referensi = $item->mutasiinden->no_mutasi ?? '';
                    } else {
                        $item->no_referensi = '-';
                    }
                    $item->nomor_rekening = $item->rekening->nama_akun ?? '-';
                    return $item;
                });

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $data = $data->filter(function($item) use ($search) {
                        return stripos($item->no_inv, $search) !== false
                            || stripos($item->tanggal_bayar_format, $search) !== false
                            || stripos($item->no_invoice_bayar, $search) !== false
                            || stripos($item->nominal_format, $search) !== false
                            || stripos($item->no_referensi, $search) !== false
                            || stripos($item->sisa_format, $search) !== false
                            || stripos($item->cara_bayar, $search) !== false
                            || stripos($item->status_bayar, $search) !== false
                            || stripos($item->nomor_rekening, $search) !== false;
                    });
                }

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => Pembayaran::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);
            }
        //  end datatable keluar

        // start datatable masuk
            if ($req->ajax() && $req->table == 'masuk') {
                $query2 = Pembayaran::where(function($query2) {
                    $query2->where('no_invoice_bayar', 'LIKE', '%Refundpo%')
                        ->orWhere('no_invoice_bayar', 'LIKE', '%RefundInden%');
                });

                if ($req->metode_masuk) {
                    $query2->where('cara_bayar', $req->input('metode_masuk'));
                }
                if ($req->dateStart2) {
                    $query2->where('tanggal_bayar', '>=', $req->input('dateStart2'));
                }
                if ($req->dateEnd2) {
                    $query2->where('tanggal_bayar', '<=', $req->input('dateEnd2'));
                }

                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                $query2->orderBy($columnName, $dir);
                $recordsFiltered = $query2->count();
                $tempData = $query2->offset($start)->limit($length)->get();
        
                $currentPage = ($start / $length) + 1;
                $perPage = $length;
            
                $data = $tempData->map(function($item, $index) use ($currentPage, $perPage) {
                    $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                    $item->userRole = Auth::user()->getRoleNames()->first();
                    $item->tanggal_bayar_format = tanggalindo($item->tanggal_bayar);
                    $item->nominal_format = formatRupiah($item->nominal);
                    if ($item->retur){
                        $item->no_referensi = $item->retur->no_retur;

                    } elseif($item->returinden) {
                        $item->no_referensi = $item->returinden->no_retur;
                    } else {
                        $item->no_referensi = '-';
                    }
                    $item->nomor_rekening = $item->rekening->nama_akun ?? '-';
                    return $item;
                });

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $data = $data->filter(function($item) use ($search) {
                        return stripos($item->no_inv, $search) !== false
                            || stripos($item->tanggal_bayar_format, $search) !== false
                            || stripos($item->nominal_format, $search) !== false
                            || stripos($item->no_referensi, $search) !== false
                            || stripos($item->sisa_format, $search) !== false
                            || stripos($item->cara_bayar, $search) !== false
                            || stripos($item->status_bayar, $search) !== false
                            || stripos($item->nomor_rekening, $search) !== false;
                    });
                }

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => Pembayaran::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);
            }
        // end datatable masuk
        // dd($data, $data2); // Tambahkan ini untuk debug
        return view('purchase.indexpembayaran');
    }

    public function store_po(Request $req){
        // validasi
        $validator = Validator::make($req->all(), [
            'invoice_purchase_id' => 'required',
            'no_invoice_bayar' => 'required',
            'nominal' => 'required',
            'tanggal_bayar' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'bukti']);
        $invoice_tagihan = Invoicepo::find($data['invoice_purchase_id']);
        
        if ($invoice_tagihan->dp === 0) {
            $invoice_tagihan->update(['dp' => $req->nominal]);
        }

        // cek sisa bayar
        if($invoice_tagihan->sisa > 0){
            $invoice_tagihan->sisa = intval($invoice_tagihan->sisa) - intval($data['nominal']);
            $check = $invoice_tagihan->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            // // store file
            // if ($req->hasFile('bukti')) {
            //     $file = $req->file('bukti');
            //     $fileName = $invoice_tagihan->no_inv . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            //     $filePath = $file->storeAs('bukti_pembayaran_purchase', $fileName, 'public');
            //     $data['bukti'] = $filePath;
            // }
            // store file
            if ($req->hasFile('bukti')) {
                // Simpan file baru
                $file = $req->file('bukti');
                $fileName = $invoice_tagihan->no_inv . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = 'bukti_pembayaran_purchase/' . $fileName;
            
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
                    $data['bukti'] = $filePath;
                } else {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                }
            }

            $new_invoice_tagihan = Invoicepo::find($data['invoice_purchase_id']);

            $lastPayment = Pembayaran::where('invoice_purchase_id', $req->invoice_purchase_id)
            ->orderBy('created_at', 'asc')
            ->get(); // Menggunakan get() untuk mendapatkan semua hasil

            // Menghitung jumlah cicilan sebelumnya
            $cicilanSebelumnya = $lastPayment->count();

            // Menentukan status pembayaran berdasarkan sisa tagihan
            if($new_invoice_tagihan->sisa <= 0){
                $data['status_bayar'] = 'LUNAS';
            } else {
                $data['status_bayar'] = 'Cicilan ke-' . ($cicilanSebelumnya + 1);   
            }
                
            // if($new_invoice_tagihan->sisa <= 0){
            //     $data['status_bayar'] = 'LUNAS';
            // } else {
            //     $data['status_bayar'] = 'BELUM LUNAS';
            // }

            $pembayaran = Pembayaran::create($data);

            if(!$pembayaran) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            return redirect()->back()->with('success', 'Pembayaran berhasil');
        } else {
            return redirect()->back()->withInput()->with('fail', 'Invoice sudah lunas');
        }
    }

    public function bayar_refund(Request $req){
        $validator = Validator::make($req->all(), [
            'retur_pembelian_id' => 'required',
            'no_invoice_bayar' => 'required',
            'nominal' => 'required',
            'tanggal_bayar' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);

         // Mengolah metode pembayaran
         $metode = $req->input('metode');
         if (strpos($metode, 'transfer-') === 0) {
             $cara_bayar = 'transfer';
             $rekening_id = str_replace('transfer-', '', $metode);
         } else {
             $cara_bayar = $metode;
             $rekening_id = null;
         }

        $data = $req->except(['_token', '_method', 'bukti','metode']);
    
        $tagihan_refund = Returpembelian::find($data['retur_pembelian_id']);

        // cek sisa bayar
        if($tagihan_refund->sisa > 0){
            $tagihan_refund->sisa = intval($tagihan_refund->sisa) - intval($data['nominal']);
            $check = $tagihan_refund->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            // // store file
            // if ($req->hasFile('bukti')) {
            //     $file = $req->file('bukti');
            //     $fileName = $tagihan_refund->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            //     $filePath = $file->storeAs('bukti_pembayaran_refundpurchase', $fileName, 'public');
            //     $data['bukti'] = $filePath;
            // }
            // store file
            if ($req->hasFile('bukti')) {
                // Simpan file baru
                $file = $req->file('bukti');
                $fileName = $tagihan_refund->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = 'bukti_pembayaran_refundpurchase/' . $fileName;
            
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
                    $data['bukti'] = $filePath;
                } else {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                }
            }

            $new_refund_tagihan = Returpembelian::find($data['retur_pembelian_id']);

            $lastPayment = Pembayaran::where('retur_pembelian_id', $req->retur_pembelian_id)
            ->orderBy('created_at', 'asc')
            ->get(); // Menggunakan get() untuk mendapatkan semua hasil

            // Menghitung jumlah cicilan sebelumnya
            $cicilanSebelumnya = $lastPayment->count();

            // Menentukan status pembayaran berdasarkan sisa tagihan
            if($new_refund_tagihan->sisa <= 0){
                $data['status_bayar'] = 'LUNAS';
            } else {
                $data['status_bayar'] = 'Cicilan ke-' . ($cicilanSebelumnya + 1);   
            }

            // if($new_refund_tagihan->sisa <= 0){
            //     $data['status_bayar'] = 'LUNAS';
            // } else {
            //     $data['status_bayar'] = 'BELUM LUNAS';
            // }

            $data['cara_bayar'] = $cara_bayar;
            $data['rekening_id'] = $rekening_id;

            $pembayaran = Pembayaran::create($data);

            if(!$pembayaran) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            return redirect()->back()->with('success', 'Pembayaran berhasil');
        } else {
            return redirect()->back()->withInput()->with('fail', 'Invoice sudah lunas');
        }

    }

    public function refundInden(Request $req){
        $validator = Validator::make($req->all(), [
            'returinden_id' => 'required',
            'no_invoice_bayar' => 'required',
            'nominal' => 'required',
            'mutasiinden_id' => 'required',
            'tanggal_bayar' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);

         // Mengolah metode pembayaran
         $metode = $req->input('metode');
         if (strpos($metode, 'transfer-') === 0) {
             $cara_bayar = 'transfer';
             $rekening_id = str_replace('transfer-', '', $metode);
         } else {
             $cara_bayar = $metode;
             $rekening_id = null;
         }

        $data = $req->except(['_token', '_method', 'bukti','metode']);
    
        $tagihan_refund = Returinden::find($data['returinden_id']);

        // cek sisa bayar
        if($tagihan_refund->sisa_refund > 0){
            $tagihan_refund->sisa_refund = intval($tagihan_refund->sisa_refund) - intval($data['nominal']);
            $check = $tagihan_refund->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            // // store file
            // if ($req->hasFile('bukti')) {
            //     $file = $req->file('bukti');
            //     $fileName = $tagihan_refund->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            //     $filePath = $file->storeAs('bukti_pembayaran_refundinden', $fileName, 'public');
            //     $data['bukti'] = $filePath;
            // }
            // store file
            if ($req->hasFile('bukti')) {
                // Simpan file baru
                $file = $req->file('bukti');
                $fileName = $tagihan_refund->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = 'bukti_pembayaran_refundinden/' . $fileName;
            
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
                    $data['bukti'] = $filePath;
                } else {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', 'File gagal disimpan');
                }
            }

            $new_refund_tagihan = Returinden::find($data['returinden_id']);

            $lastPayment = Pembayaran::where('returinden_id', $req->returinden_id)
            ->orderBy('created_at', 'asc')
            ->get(); // Menggunakan get() untuk mendapatkan semua hasil

            // Menghitung jumlah cicilan sebelumnya
            $cicilanSebelumnya = $lastPayment->count();

            // Menentukan status pembayaran berdasarkan sisa tagihan
            if($new_refund_tagihan->sisa_refund <= 0){
                $data['status_bayar'] = 'LUNAS';
            } else {
                $data['status_bayar'] = 'Cicilan ke-' . ($cicilanSebelumnya + 1);   
            }

            $data['cara_bayar'] = $cara_bayar;
            $data['rekening_id'] = $rekening_id;

            $pembayaran = Pembayaran::create($data);

            if(!$pembayaran) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            return redirect()->back()->with('success', 'Pembayaran berhasil');
        } else {
            return redirect()->back()->withInput()->with('fail', 'Invoice sudah lunas');
        }

    }

    public function updateStatusAll($invoice_id, $jenis)
    {
        if ($jenis == 'Sewa') {
            $pembayarans = Pembayaran::where('invoice_sewa_id', $invoice_id)->orderBy('tanggal_bayar')->get();
            $invoice = InvoiceSewa::find($invoice_id);
            $remainingBalance = $invoice->total_tagihan - $invoice->dp;

            $totalPaid = 0;
            foreach ($pembayarans as $pembayaran) {
                $totalPaid += $pembayaran->nominal;
            }

            $isFullyPaid = $totalPaid >= $remainingBalance;
            $lastPaymentIndex = $pembayarans->count() - 1;

            foreach ($pembayarans as $index => $pembayaran) {
                $remainingBalance -= $pembayaran->nominal;
                $status = 'Cicilan ke-' . ($index + 1);

                if ($isFullyPaid && $index == $lastPaymentIndex) {
                    $status = 'LUNAS';
                }

                $pembayaran->status_bayar = $status;
                $pembayaran->save();
            }
        } else {
            switch ($jenis) {
                case 'InvoicePO':
                case 'InvoiceInden':
                    $pembayarans = Pembayaran::where('invoice_purchase_id', $invoice_id)->orderBy('tanggal_bayar')->get();
                    $invoice = Invoicepo::find($invoice_id);
                    $remainingBalance = $invoice->total_tagihan - $invoice->dp;
                    // dd($remainingBalance);
                    break;
                case 'ReturPO':
                    $pembayarans = Pembayaran::where('retur_pembelian_id', $invoice_id)->orderBy('tanggal_bayar')->get();
                    $invoice = Returpembelian::find($invoice_id);
                    $remainingBalance = $invoice->subtotal;
                    break;
                case 'ReturInden':
                    $pembayarans = Pembayaran::where('returinden_id', $invoice_id)->orderBy('tanggal_bayar')->get();
                    $invoice = Returinden::find($invoice_id);
                    $remainingBalance = $invoice->refund;
                    break;
                case 'MutasiInden':
                    $pembayarans = Pembayaran::where('mutasiinden_id', $invoice_id)->orderBy('tanggal_bayar')->get();
                    $invoice = Mutasiindens::find($invoice_id);
                    $remainingBalance = $invoice->returinden ? $invoice->returinden->total_akhir : $invoice->total_biaya;
                    break;
                default:
                    return;
            }

            $totalPaid = 0;
            foreach ($pembayarans as $pembayaran) {
                $totalPaid += $pembayaran->nominal;
            }
            
            $isFullyPaid = $totalPaid >= $remainingBalance;
            $lastPaymentIndex = $pembayarans->count() - 1;

            foreach ($pembayarans as $index => $pembayaran) {
                $remainingBalance -= $pembayaran->nominal;
                $status = 'Cicilan ke-' . ($index + 1);

                if ($isFullyPaid && $index == $lastPaymentIndex) {
                    $status = 'LUNAS';
                }

                $pembayaran->status_bayar = $status;
                $pembayaran->save();
            }
        }
    }

    public function edit_pembelian(Request $req, $id)
    {
        $validator = Validator::make($req->all(), [
            'jenis' => 'required|in:InvoicePO,InvoiceInden,ReturPO,ReturInden,MutasiInden',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return response()->json('Jenis tidak valid', 400);

        $data = Pembayaran::with('po.pembelian', 'po.poinden', 'mutasiinden', 'retur', 'returinden')->find($id);
        if(!$data) return response()->json('Data tidak ditemukan', 404);
        switch ($req->jenis) {
            case 'InvoicePO':
                $data->no_referensi = $data->po->pembelian->no_po;
                $data->total_tagihan = $data->po->sisa;
                $data->invoice_id = $data->po->id;
                break;
            case 'InvoiceInden':
                $data->no_referensi = $data->po->poinden->no_po;
                $data->total_tagihan = $data->po->sisa;
                $data->invoice_id = $data->po->id;
                break;
            case 'ReturPO':
                $data->no_referensi = $data->retur->no_retur;
                $data->total_tagihan = $data->retur->subtotal;
                $data->invoice_id = $data->retur->id;
                break;
            case 'ReturInden':
                $data->no_referensi = $data->returinden->no_retur;
                $data->total_tagihan = $data->returinden->refund;
                $data->invoice_id = $data->returinden->id;
                break;
            case 'MutasiInden':
                $data->no_referensi = $data->mutasiinden->no_mutasi;
                $data->total_tagihan = $data->mutasiinden->total_biaya;
                $data->invoice_id = $data->mutasiinden->id;
                break;
            default:
                $data->no_referensi = '-';
                $data->total_tagihan = 0;
                $data->invoice_id = null;
                break;
        }
        if($data->cara_bayar == 'cash'){
            $data->metode = 'cash';
        } else {
            $data->metode = 'transfer-' . $data->rekening_id;
        }
        return response()->json($data);
    }

    public function update_pembelian(Request $req, $id) 
    {
        // Validasi
        $validator = Validator::make($req->all(), [
            'tanggal_bayar' => 'required|date',
            'metode' => 'required|string',
            'invoice_id' => 'required|integer',
            'type' => 'required|in:InvoicePO,InvoiceInden,ReturPO,ReturInden,MutasiInden',
            'nominal' => 'required|numeric',
            'bukti' => 'nullable|file|mimes:jpg,jpeg,png,gif|max:2048',
        ]);
    
        if ($validator->fails()) {
            $error = $validator->errors()->all();
            return redirect()->back()->withInput()->with('fail', $error);
        }
    
        $data = $req->except(['_token', '_method', 'bukti']);
        $pembayaran = Pembayaran::find($id);
    
        DB::beginTransaction();
    
        try {
            $fileName = '';
            $filePath = '';
    
            switch ($req->type) {
                case 'InvoicePO':
                    $invoice_tagihan = Invoicepo::find($data['invoice_id']);
                    $invoice_tagihan->sisa = intval($invoice_tagihan->sisa) + intval($pembayaran->nominal) - intval($data['nominal']);
                    
                    $awalbayar = Pembayaran::where('invoice_purchase_id',$data['invoice_id'])->get();
                    if($pembayaran->status_bayar == "Cicilan ke-1" || ($awalbayar->count() == 1 && $awalbayar->first()->status_bayar == "LUNAS")) {
                        $invoice_tagihan->dp = intval($data['nominal']);
                    }

                    if($invoice_tagihan->sisa < 0){
                        throw new \Exception('Nominal melebihi tagihan');
                    }

                    $data['status_bayar'] = $invoice_tagihan->sisa <= 0 ? 'LUNAS' : 'BELUM LUNAS';
                    
                    $fileName = 'invoice_' . $invoice_tagihan->no_invoice . '_' . date('YmdHis') . '.';
                    $filePath = 'bukti_pembayaran_purchase/';
                    
                    break;
                case 'InvoiceInden':
                    $invoice_tagihan = Invoicepo::find($data['invoice_id']);
                    $invoice_tagihan->sisa = intval($invoice_tagihan->sisa) + intval($pembayaran->nominal) - intval($data['nominal']);
                    
                    if($invoice_tagihan->sisa < 0){
                        throw new \Exception('Nominal melebihi tagihan');
                    }

                    $data['status_bayar'] = $invoice_tagihan->sisa <= 0 ? 'LUNAS' : 'BELUM LUNAS';
                    
                    $fileName = 'invoice_' . $invoice_tagihan->no_invoice . '_' . date('YmdHis') . '.';
                    $filePath = 'bukti_pembayaran_purchase/';
                    
                    break;
                case 'ReturPO':
                    $invoice_tagihan = Returpembelian::find($data['invoice_id']);
                    $invoice_tagihan->sisa = intval($invoice_tagihan->sisa) + intval($pembayaran->nominal) - intval($data['nominal']);
                    
                    if($invoice_tagihan->sisa < 0){
                        throw new \Exception('Nominal melebihi tagihan');
                    }

                    $data['status_bayar'] = $invoice_tagihan->sisa <= 0 ? 'LUNAS' : 'BELUM LUNAS';
                    
                    $fileName = 'returpo_' . $invoice_tagihan->no_retur . '_' . date('YmdHis') . '.';
                    $filePath = 'bukti_pembayaran_refundpurchase/';
                    
                    break;
                case 'ReturInden':
                    $invoice_tagihan = Returinden::find($data['invoice_id']);
                    $invoice_tagihan->sisa_refund = intval($invoice_tagihan->sisa_refund) + intval($pembayaran->nominal) - intval($data['nominal']);
                    
                    if($invoice_tagihan->sisa_refund < 0){
                        throw new \Exception('Nominal melebihi tagihan');
                    }

                    $data['status_bayar'] = $invoice_tagihan->sisa_refund <= 0 ? 'LUNAS' : 'BELUM LUNAS';
                    
                    $fileName = 'returinden_' . $invoice_tagihan->no_retur . '_' . date('YmdHis') . '.';
                    $filePath = 'bukti_pembayaran_refundinden/';
                    
                    break;
                case 'MutasiInden':
                    $invoice_tagihan = Mutasiindens::find($data['invoice_id']);
                    $invoice_tagihan->sisa_bayar = intval($invoice_tagihan->sisa_bayar) + intval($pembayaran->nominal) - intval($data['nominal']);
                    
                    if($invoice_tagihan->sisa_bayar < 0){
                        throw new \Exception('Nominal melebihi tagihan');
                    }

                    $data['status_bayar'] = $invoice_tagihan->sisa_bayar <= 0 ? 'LUNAS' : 'BELUM LUNAS';
                    
                    $fileName = 'mutasiinden_' . $invoice_tagihan->no_mutasi . '_' . date('YmdHis') . '.';
                    $filePath = 'bukti_pembayaran_mutasiinden/';
                    
                    break;
                default:
                    throw new \Exception('Tipe tidak valid');
                    break;
            }

            // Mengolah metode pembayaran
            $metode = $req->input('metode');
            if (strpos($metode, 'transfer-') === 0) {
                $cara_bayar = 'transfer';
                $rekening_id = str_replace('transfer-', '', $metode);
            } else {
                $cara_bayar = $metode;
                $rekening_id = null;
            }
        
            // Menyiapkan data untuk disimpan
            $data = $req->except(['_token', '_method', 'bukti', 'status_bayar', 'metode']);
            $data['cara_bayar'] = $cara_bayar;
            $data['rekening_id'] = $rekening_id;
    
            // Update sisa invoice
            if ($invoice_tagihan && !$invoice_tagihan->save()) {
                throw new \Exception('Gagal menyimpan data invoice.');
            }
    
            // store file
            if ($req->hasFile('bukti')) {
                $file = $req->file('bukti');
    
                // Generate nama file dengan ekstensi
                $fileName .= $file->getClientOriginalExtension();
                $filePath .= $fileName;
    
                // Optimize dan simpan file baru
                Image::make($file)->encode($file->getClientOriginalExtension(), 70)
                    ->save(storage_path('app/public/' . $filePath));
    
                // Hapus file lama
                if (!empty($pembayaran->bukti)) {
                    $oldFilePath = storage_path('app/public/' . $pembayaran->bukti);
                    if (File::exists($oldFilePath)) {
                        File::delete($oldFilePath);
                    }
                }
    
                // Verifikasi penyimpanan file baru
                if (File::exists(storage_path('app/public/' . $filePath))) {
                    $data['bukti'] = $filePath;
                } else {
                    throw new \Exception('File gagal disimpan.');
                }
            }
    
            if (!$pembayaran->update($data)) {
                throw new \Exception('Gagal menyimpan data pembayaran.');
            }

            // Update status semua pembayaran
            $this->updateStatusAll($data['invoice_id'], $req->type);
    
            DB::commit(); 
            return redirect()->back()->with('success', 'Pembayaran berhasil');
    
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}