<?php

namespace App\Http\Controllers;

use App\Exports\KontrakExport;
use App\Exports\TagihanSewaExport;
use App\Models\Customer;
use App\Models\InvoiceSewa;
use App\Models\Kontrak;
use App\Models\Lokasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function kontrak_index(Request $req)
    {
        $query = Kontrak::with(['produk.produk', 'customer'])->where('status', 'DIKONFIRMASI');

        if ($req->customer) {
            $query->where('customer_id', $req->customer);
        }
        if ($req->gallery) {
            $query->where('lokasi_id', $req->gallery);
        }
        if ($req->masa_sewa) {
            $query->where('masa_sewa', $req->masa_sewa);
        }
        if ($req->status) {
            $query->where('status', $req->status);
        }
        if ($req->dateStart) {
            $query->where('tanggal_kontrak', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kontrak', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item) {
            $tanggalMulai = Carbon::parse($item->tanggal_mulai);
            $tanggalSekarang = Carbon::now();
        
            if ($tanggalMulai->greaterThan($tanggalSekarang)) {
                $perbedaan = $tanggalSekarang->diff($tanggalMulai);
                $item->status_kontrak = 'Dimulai dalam ' . ($perbedaan->m > 0 
                    ? $perbedaan->m . ' bulan' 
                    : $perbedaan->d . ' hari');
            } else {
                $perbedaan = $tanggalMulai->diff($tanggalSekarang);
                $item->status_kontrak = 'Sudah Berjalan ' . ($perbedaan->m > 0 
                    ? $perbedaan->m . ' bulan' 
                    : $perbedaan->d . ' hari');
            }
        
            return $item;
        });
        $customer = Customer::whereHas('kontrak')->get();
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
        $masa_sewa = $data->pluck('masa_sewa')->unique();
        $statuses = $data->pluck('status')->unique();
        return view('laporan.kontrak', compact('data', 'customer', 'galleries', 'masa_sewa', 'statuses'));
    }

    public function kontrak_pdf(Request $req)
    {
        $query = Kontrak::with(['produk.produk', 'customer'])->where('status', 'DIKONFIRMASI');

        if ($req->customer) {
            $query->where('customer_id', $req->customer);
        }
        if ($req->gallery) {
            $query->where('lokasi_id', $req->gallery);
        }
        if ($req->masa_sewa) {
            $query->where('masa_sewa', $req->masa_sewa);
        }
        if ($req->status) {
            $query->where('status', $req->status);
        }
        if ($req->dateStart) {
            $query->where('tanggal_kontrak', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kontrak', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item) {
            $tanggalMulai = Carbon::parse($item->tanggal_mulai);
            $tanggalSekarang = Carbon::now();
        
            if ($tanggalMulai->greaterThan($tanggalSekarang)) {
                $perbedaan = $tanggalSekarang->diff($tanggalMulai);
                $item->status_kontrak = 'Dimulai dalam ' . ($perbedaan->m > 0 
                    ? $perbedaan->m . ' bulan' 
                    : $perbedaan->d . ' hari');
            } else {
                $perbedaan = $tanggalMulai->diff($tanggalSekarang);
                $item->status_kontrak = 'Sudah Berjalan ' . ($perbedaan->m > 0 
                    ? $perbedaan->m . ' bulan' 
                    : $perbedaan->d . ' hari');
            }
        
            return $item;
        });
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.kontrak_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('kontrak.pdf');
    }
    
    public function kontrak_excel(Request $req)
    {
        $query = Kontrak::with(['produk.produk', 'customer'])->where('status', 'DIKONFIRMASI');

        if ($req->customer) {
            $query->where('customer_id', $req->customer);
        }
        if ($req->gallery) {
            $query->where('lokasi_id', $req->gallery);
        }
        if ($req->masa_sewa) {
            $query->where('masa_sewa', $req->masa_sewa);
        }
        if ($req->status) {
            $query->where('status', $req->status);
        }
        if ($req->dateStart) {
            $query->where('tanggal_kontrak', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kontrak', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item) {
            $tanggalMulai = Carbon::parse($item->tanggal_mulai);
            $tanggalSekarang = Carbon::now();
        
            if ($tanggalMulai->greaterThan($tanggalSekarang)) {
                $perbedaan = $tanggalSekarang->diff($tanggalMulai);
                $item->status_kontrak = 'Dimulai dalam ' . ($perbedaan->m > 0 
                    ? $perbedaan->m . ' bulan' 
                    : $perbedaan->d . ' hari');
            } else {
                $perbedaan = $tanggalMulai->diff($tanggalSekarang);
                $item->status_kontrak = 'Sudah Berjalan ' . ($perbedaan->m > 0 
                    ? $perbedaan->m . ' bulan' 
                    : $perbedaan->d . ' hari');
            }
        
            return $item;
        });
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new KontrakExport($data), 'kontrak.xlsx');
    }

    public function tagihan_sewa_index(Request $req)
    {
        $query = InvoiceSewa::with(['produk.produk', 'kontrak.customer'])->where('status', 'DIKONFIRMASI');

        if ($req->customer) {
            $query->whereHas('kontrak', function($q) use($req){
                $q->where('customer_id', $req->customer);
            });
        }
        if ($req->gallery) {
            $query->whereHas('kontrak', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item){
            $item->terbayar = $item->total_tagihan - $item->sisa_bayar;
            return $item;
        });
        $customer = Customer::whereHas('kontrak')->get();
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
        return view('laporan.tagihan_sewa', compact('data', 'customer', 'galleries'));
    }

    public function tagihan_sewa_pdf(Request $req)
    {
        $query = InvoiceSewa::with(['produk.produk', 'kontrak.customer'])->where('status', 'DIKONFIRMASI');

        if ($req->customer) {
            $query->whereHas('kontrak', function($q) use($req){
                $q->where('customer_id', $req->customer);
            });
        }
        if ($req->gallery) {
            $query->whereHas('kontrak', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item){
            $item->terbayar = $item->total_tagihan - $item->sisa_bayar;
            return $item;
        });
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.tagihan_sewa_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('tagihan_sewa.pdf');
    }

    public function tagihan_sewa_excel(Request $req)
    {
        $query = InvoiceSewa::with(['produk.produk', 'kontrak.customer'])->where('status', 'DIKONFIRMASI');

        if ($req->customer) {
            $query->whereHas('kontrak', function($q) use($req){
                $q->where('customer_id', $req->customer);
            });
        }
        if ($req->gallery) {
            $query->whereHas('kontrak', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item){
            $item->terbayar = $item->total_tagihan - $item->sisa_bayar;
            return $item;
        });
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new TagihanSewaExport($data), 'tagihan_sewa.xlsx');
    }
}
