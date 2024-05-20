<?php

namespace App\Http\Controllers;

use App\Models\Invoicepo;
use Illuminate\Http\Request;
use App\Models\Pembayaran;
use App\Models\Penjualan;
use App\Models\Rekening;
use Illuminate\Support\Facades\Validator;

class PembayaranController extends Controller
{

    public function index()
    {
        $data = Pembayaran::with('rekening')->orderBy('created_at', 'desc')->get();

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
    
        // Menentukan status pembayaran berdasarkan sisa tagihan
        $data['status_bayar'] = $cekTotalTagihan <= 0 ? 'LUNAS' : 'BELUM LUNAS';
    
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
                return redirect(route('invoice.edit', ['datapo' => $req->id_po, 'type' => $type]))->with('success', 'Data Berhasil Disimpan');
            } else {
                // Mengembalikan respon gagal jika penyimpanan data pembayaran gagal
                return redirect()->back()->with('fail', 'Gagal menyimpan data');
            }
        }elseif ($type === 'poinden') {
            if ($pembayaran) {
                // Mengembalikan respon sukses jika data pembayaran berhasil disimpan
                return redirect(route('invoice.edit', ['datapo' => $req->id_po, 'type' => $type]))->with('success', 'Data Berhasil Disimpan');
            } else {
                // Mengembalikan respon gagal jika penyimpanan data pembayaran gagal
                return redirect()->back()->with('fail', 'Gagal menyimpan data');
            }

        }
    
    }
 
}
