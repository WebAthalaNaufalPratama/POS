<?php

namespace App\Http\Controllers;

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
            return redirect()->back()->with('error', 'Tagihan tidak ditemukan.');
        }

        if ($pembayaran) {
            return redirect(route('penjualan.payment', ['penjualan' => $req->invoice_penjualan_id]))->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }
}
