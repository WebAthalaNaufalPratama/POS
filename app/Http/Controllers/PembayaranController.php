<?php

namespace App\Http\Controllers;

use App\Models\Invoicepo;
use App\Models\InvoiceSewa;
use App\Models\Mutasiindens;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use App\Models\Rekening;
use App\Models\Karyawan;
use App\Models\Lokasi;
use App\Models\Returinden;
use App\Models\Returpembelian;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{

    public function index(Request $req)
    {
        $user = Auth::user();
        $lokasi = Karyawan::where('user_id', $user->id)->first();
        $userroles = Auth::user()->roles()->value('name');
        // dd($user);
        if($lokasi->lokasi->tipe_lokasi == 2){
            $penjualan = Penjualan::where('no_invoice', 'LIKE', 'IPO%')->where('lokasi_id', $lokasi->lokasi_id)->get();
            $penjualanIds = $penjualan->pluck('id')->toArray();
            // dd($penjualanIds);
            $query = Pembayaran::whereNotNull('invoice_penjualan_id')->whereIn('invoice_penjualan_id', $penjualanIds)->where('no_invoice_bayar', 'LIKE', 'BOT%');
        }elseif($lokasi->lokasi->tipe_lokasi == 1 ){
            $penjualan = Penjualan::where('no_invoice', 'LIKE', 'INV%')->where('lokasi_id', $lokasi->lokasi_id)->get();
            $penjualanIds = $penjualan->pluck('id')->toArray();
            // dd($penjualan);
            $query = Pembayaran::whereNotNull('invoice_penjualan_id')->whereIn('invoice_penjualan_id', $penjualanIds)->where('no_invoice_bayar', 'LIKE', 'BYR%');
            // dd($query);
        }else{
            $query = Penjualan::with('karyawan')->whereNotNull('no_invoice');
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
        $data = $query->orderByDesc('id')->get();
        // $data = Pembayaran::with('rekening')->orderBy('created_at', 'desc')->get();

        return view('pembayaran.index', compact('data'));
    }

    public function edit($pembayaran)
    {
        // dd($pembayaran);
        $bankpens = Rekening::all();
        $data = Pembayaran::with('rekening')->find($pembayaran);
        // dd($data);
        return view('pembayaran.edit', compact('data', 'bankpens'));
    }

    public function update(Request $req, $pembayaran)
    {
        // dd($pembayaran);
        // dd($req->hasFile('bukti'));
        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = $req->no_invoice_bayar . date('YmdHis') . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
            $data['bukti'] = $filePath;

            $do = Pembayaran::find($pembayaran);

            $penjualan = Penjualan::find($do->invoice_penjualan_id);

            // dd($penjualan);
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

        // dd($data);

        if ($req->hasFile('bukti')) {
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_pembayaran_penjualan', $fileName, 'public');
            $data['bukti'] = $filePath;
        }

        $penjualan = Penjualan::find($req->invoice_penjualan_id);

        // dd($penjualan);
        if ($penjualan) {
            $cekTotalTagihan = $penjualan->sisa_bayar - $req->nominal;
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
        $query = Pembayaran::whereNotNull('invoice_sewa_id');
        if(Auth::user()->karyawans){
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
        $data = $query->orderByDesc('id')->get();
        $bankpens = Rekening::get();
        return view('pembayaran_sewa.index', compact('data', 'bankpens'));
    }

    public function store_sewa(Request $req){
        // validasi
        $validator = Validator::make($req->all(), [
            'invoice_sewa_id' => 'required',
            'no_invoice_bayar' => 'required',
            'nominal' => 'required',
            'tanggal_bayar' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'bukti']);
        $invoice_tagihan = InvoiceSewa::find($data['invoice_sewa_id']);

        // cek sisa bayar
        if($invoice_tagihan->sisa_bayar > 0){
            $invoice_tagihan->sisa_bayar = intval($invoice_tagihan->sisa_bayar) - intval($data['nominal']);
            $check = $invoice_tagihan->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            // store file
            if ($req->hasFile('bukti')) {
                $file = $req->file('bukti');
                $fileName = $invoice_tagihan->no_invoice . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('bukti_pembayaran_sewa', $fileName, 'public');
                $data['bukti'] = $filePath;
            }

            $new_invoice_tagihan = InvoiceSewa::find($data['invoice_sewa_id']);
            if($new_invoice_tagihan->sisa_bayar <= 0){
                $data['status_bayar'] = 'LUNAS';
            } else {
                $data['status_bayar'] = 'BELUM LUNAS';
            }
            $pembayaran = Pembayaran::create($data);

            if(!$pembayaran) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            return redirect()->back()->with('success', 'Pembayaran berhasil');
        } else {
            return redirect()->back()->withInput()->with('fail', 'Invoice sudah lunas');
        }
    }

    public function show_sewa(Request $req, $id)
    {
        $data = Pembayaran::with('rekening', 'sewa', 'sewa.kontrak')->find($id);
        if(!$data) return response()->json('Data tidak ditemukan', 404);
        return response()->json($data);
    }

    public function update_sewa(Request $req, $id){
        // validasi
        $validator = Validator::make($req->all(), [
            'invoice_sewa_id' => 'required',
            'no_invoice_bayar' => 'required',
            'nominal' => 'required',
            'tanggal_bayar' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'bukti']);
        $invoice_tagihan = InvoiceSewa::find($data['invoice_sewa_id']);
        $pembayaran = Pembayaran::find($id);

        // cek sisa bayar
        // if($invoice_tagihan->sisa_bayar > 0){
            $invoice_tagihan->sisa_bayar = intval($invoice_tagihan->sisa_bayar) + intval($pembayaran->nominal) - intval($data['nominal']);
            $check = $invoice_tagihan->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            // store file
            if ($req->hasFile('bukti')) {
                $file = $req->file('bukti');
                $fileName = $invoice_tagihan->no_invoice . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('bukti_pembayaran_sewa', $fileName, 'public');
                $data['bukti'] = $filePath;
            }

            $new_invoice_tagihan = InvoiceSewa::find($data['invoice_sewa_id']);
            if($new_invoice_tagihan->sisa_bayar <= 0){
                $data['status_bayar'] = 'LUNAS';
            } else {
                $data['status_bayar'] = 'BELUM LUNAS';
            }
            $update = $pembayaran->update($data);

            if(!$update) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            return redirect()->back()->with('success', 'Pembayaran berhasil');
        // } else {
        //     return redirect()->back()->withInput()->with('fail', 'Invoice sudah lunas');
        // }
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
    
        if ($req->hasFile('bukti')) {
            // Mengunggah file bukti pembayaran
            $file = $req->file('bukti');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_pembayaran_purchase', $fileName, 'public');
            $data['bukti'] = $filePath;
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
    
        if ($req->hasFile('buktitf')) {
            // Mengunggah file bukti pembayaran
            $file = $req->file('buktitf');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('bukti_pembayaran_mutasiinden', $fileName, 'public');
            $data['bukti'] = $filePath;
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
        $query = Pembayaran::whereNotNull('invoice_purchase_id');
        if ($req->metode_keluar) {
            $query->where('cara_bayar', $req->input('metode_keluar'));
        }
        if ($req->dateStart) {
            $query->where('tanggal_bayar', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_bayar', '<=', $req->input('dateEnd'));
        }
        $data = $query->orderByDesc('id')->get();

        $query2 = Pembayaran::where('no_invoice_bayar', 'LIKE', '%Refundpo%');
        if ($req->metode_masuk) {
            $query2->where('cara_bayar', $req->input('metode_masuk'));
        }
        if ($req->dateStart2) {
            $query2->where('tanggal_bayar', '>=', $req->input('dateStart2'));
        }
        if ($req->dateEnd2) {
            $query2->where('tanggal_bayar', '<=', $req->input('dateEnd2'));
        }
        $data2 = $query2->orderByDesc('id')->get();
        return view('purchase.indexpembayaran', compact('data', 'data2'));
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
        

        // cek sisa bayar
        if($invoice_tagihan->sisa > 0){
            $invoice_tagihan->sisa = intval($invoice_tagihan->sisa) - intval($data['nominal']);
            $check = $invoice_tagihan->update();
            if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

            // store file
            if ($req->hasFile('bukti')) {
                $file = $req->file('bukti');
                $fileName = $invoice_tagihan->no_inv . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('bukti_pembayaran_purchase', $fileName, 'public');
                $data['bukti'] = $filePath;
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

            if ($invoice_tagihan->dp === 0) {
                $invoice_tagihan->update(['dp' => $req->nominal]);
            }

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

            // store file
            if ($req->hasFile('bukti')) {
                $file = $req->file('bukti');
                $fileName = $tagihan_refund->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('bukti_pembayaran_refundpurchase', $fileName, 'public');
                $data['bukti'] = $filePath;
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

            // store file
            if ($req->hasFile('bukti')) {
                $file = $req->file('bukti');
                $fileName = $tagihan_refund->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
                $filePath = $file->storeAs('bukti_pembayaran_refundinden', $fileName, 'public');
                $data['bukti'] = $filePath;
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

}
