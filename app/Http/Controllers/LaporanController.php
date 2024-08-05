<?php

namespace App\Http\Controllers;

use App\Exports\KontrakExport;
use App\Exports\PenjualanProdukExport;
use App\Exports\PelangganExport;
use App\Exports\DeliveryOrderExport;
use App\Exports\MutasiExport;
use App\Exports\PenjualanExport;
use App\Exports\ReturPenjualanExport;
use App\Exports\PembayaranExport;
use App\Models\Customer;
use App\Models\Kontrak;
use App\Models\Lokasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\Penjualan;
use App\Models\Produk_Terjual;
use App\Models\Produk_Jual;
use App\Models\Tipe_Produk;
use App\Models\DeliveryOrder;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Pembayaran;
use App\Models\Rekening;
use App\Models\Mutasi;
use App\Models\Supplier;
use App\Models\ReturPenjualan;

class LaporanController extends Controller
{
    public function kontrak_index(Request $req)
    {
        $query = Kontrak::with(['produk.produk', 'customer']);

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
        $query = Kontrak::with(['produk.produk', 'customer']);

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
        $query = Kontrak::with(['produk.produk', 'customer']);

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

    public function penjualanproduk_index(Request $req)
    {
        $produkjual = Produk_Jual::all();
        $query = Penjualan::where('status', 'DIKONFIRMASI')
                  ->whereNotNull('dibuat_id')
                  ->whereNotNull('dibukukan_id')
                  ->whereNotNull('auditor_id')
                  ->whereNotNull('tanggal_dibuat')
                  ->whereNotNull('tanggal_dibukukan')
                  ->whereNotNull('tanggal_audit');

        if ($req->gallery) {
            $query->where('lokasi_pengirim', $req->gallery);
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $penjualan1 = $query->get();
        $galleries = Lokasi::whereIn('tipe_lokasi', [1, 2])->get();

        $arrinvoice = $penjualan1->pluck('no_invoice')->toArray();
        $produkterjual1 = Produk_Terjual::whereIn('no_invoice', $arrinvoice)->get();
        // dd($produkterjual1);
        $pojuList = []; 

        if ($produkterjual1->isNotEmpty()) {
            foreach ($produkterjual1 as $pj) {
                $projualQuery = Produk_Jual::where('id', $pj->produk_jual_id);

                if (!empty($req->produk)) {
                    $projualQuery->where('kode', $req->produk);
                }

                $pojuResult = $projualQuery->first();

                if ($pojuResult && !in_array($pojuResult->id, array_column($pojuList, 'id'))) {
                    $pojuList[] = $pojuResult; 
                }
            }
        }

        if (empty($req->produk)) {
            $produkJualIds = array_column($pojuList, 'id');
            $produkterjual = Produk_Terjual::whereIn('no_invoice', $arrinvoice)
                                            ->whereIn('produk_jual_id', $produkJualIds)
                                            ->get();
                                            
        } else {
            $produkterjual = !empty($pojuList) ? Produk_Terjual::whereIn('no_invoice', $arrinvoice)
                                                        ->where('produk_jual_id', $pojuList[0]->id)
                                                        ->get() : collect();
        }
        
        return view('laporan.penjualanproduk', compact('pojuList','produkjual', 'produkterjual', 'galleries'));        
    }
    public function penjualanproduk_pdf(Request $req)
    {
        $produkjual = Produk_Jual::all();
        $query = Penjualan::where('status', 'DIKONFIRMASI')
                  ->whereNotNull('dibuat_id')
                  ->whereNotNull('dibukukan_id')
                  ->whereNotNull('auditor_id')
                  ->whereNotNull('tanggal_dibuat')
                  ->whereNotNull('tanggal_dibukukan')
                  ->whereNotNull('tanggal_audit');

        if ($req->gallery) {
            $query->where('lokasi_pengirim', $req->gallery);
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $penjualan1 = $query->get();
        $galleries = Lokasi::whereIn('tipe_lokasi', [1, 2])->get();

        $arrinvoice = $penjualan1->pluck('no_invoice')->toArray();
        $produkterjual1 = Produk_Terjual::whereIn('no_invoice', $arrinvoice)->get();
        // dd($produkterjual1);
        $pojuList = []; 

        if ($produkterjual1->isNotEmpty()) {
            foreach ($produkterjual1 as $pj) {
                $projualQuery = Produk_Jual::where('id', $pj->produk_jual_id);

                if (!empty($req->produk)) {
                    $projualQuery->where('kode', $req->produk);
                }

                $pojuResult = $projualQuery->first();

                if ($pojuResult && !in_array($pojuResult->id, array_column($pojuList, 'id'))) {
                    $pojuList[] = $pojuResult; 
                }
            }
        }

        if (empty($req->produk)) {
            $produkJualIds = array_column($pojuList, 'id');
            $produkterjual = Produk_Terjual::whereIn('no_invoice', $arrinvoice)
                                            ->whereIn('produk_jual_id', $produkJualIds)
                                            ->get();
                                            
        } else {
            $produkterjual = !empty($pojuList) ? Produk_Terjual::whereIn('no_invoice', $arrinvoice)
                                                        ->where('produk_jual_id', $pojuList[0]->id)
                                                        ->get() : collect();
        }

        if($produkterjual->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.penjualanproduk_pdf', compact('produkterjual', 'pojuList'))->setPaper('a4', 'portrait');
        return $pdf->stream('penjualan_produk.pdf');

    }
    public function penjualanproduk_excel(Request $req)
    {
        $produkjual = Produk_Jual::all();
        $query = Penjualan::where('status', 'DIKONFIRMASI')
                  ->whereNotNull('dibuat_id')
                  ->whereNotNull('dibukukan_id')
                  ->whereNotNull('auditor_id')
                  ->whereNotNull('tanggal_dibuat')
                  ->whereNotNull('tanggal_dibukukan')
                  ->whereNotNull('tanggal_audit');

        if ($req->gallery) {
            $query->where('lokasi_pengirim', $req->gallery);
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $penjualan1 = $query->get();
        $galleries = Lokasi::whereIn('tipe_lokasi', [1, 2])->get();

        $arrinvoice = $penjualan1->pluck('no_invoice')->toArray();
        $produkterjual1 = Produk_Terjual::whereIn('no_invoice', $arrinvoice)->get();
        // dd($produkterjual1);
        $pojuList = []; 

        if ($produkterjual1->isNotEmpty()) {
            foreach ($produkterjual1 as $pj) {
                $projualQuery = Produk_Jual::where('id', $pj->produk_jual_id);

                if (!empty($req->produk)) {
                    $projualQuery->where('kode', $req->produk);
                }

                $pojuResult = $projualQuery->first();

                if ($pojuResult && !in_array($pojuResult->id, array_column($pojuList, 'id'))) {
                    $pojuList[] = $pojuResult; 
                }
            }
        }

        if (empty($req->produk)) {
            $produkJualIds = array_column($pojuList, 'id');
            $produkterjual = Produk_Terjual::whereIn('no_invoice', $arrinvoice)
                                            ->whereIn('produk_jual_id', $produkJualIds)
                                            ->get();
                                            
        } else {
            $produkterjual = !empty($pojuList) ? Produk_Terjual::whereIn('no_invoice', $arrinvoice)
                                                        ->where('produk_jual_id', $pojuList[0]->id)
                                                        ->get() : collect();
        }
        if($produkterjual->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new PenjualanProdukExport($produkterjual, $pojuList), 'penjualan_produk.xlsx');
    }
    public function pelanggan_index(Request $req)
    {
        $query = Penjualan::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('dibuat_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('auditor_id')
                    ->wherenotNull('tanggal_dibuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_audit');

        if($req->gallery) {
            $query->where('lokasi_id', $req->gallery);
        }
        if($req->customer) {
            $query->where('id_customer', $req->customer);
        }
        if($req->tempo) {
            $query->where('jatuh_tempo','=', $req->tempo);
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }
            
        $penjualan = $query->get();

        $groupedPenjualan = $penjualan->groupBy('id_customer')->map(function ($group) {
            $group[0]->no_invoice = $group->pluck('no_invoice')->implode(', ');
            $group[0]->lokasi_id = $group->pluck('lokasi_id')->implode(',');
            $group[0]->tanggal_invoice = $group->pluck('tanggal_invoice')->implode(', ');
            $group[0]->jatuh_tempo = $group->pluck('jatuh_tempo')->implode(', ');
            
            $group[0]->total_tagihan = $group->pluck('total_tagihan')->implode(', ');
            $group[0]->dp = $group->pluck('dp')->implode(', ');
            $group[0]->sisa_bayar = $group->pluck('sisa_bayar')->implode(', ');

            return $group[0];
        });

        $penjualan = $groupedPenjualan->values();
        $galleries = Lokasi::whereIn('tipe_lokasi', [1,2])->get();
        $customers = Customer::whereNotNull('tanggal_bergabung')->get();

        return view('laporan.pelanggan', compact('penjualan', 'galleries', 'customers'));
    }

    public function pelanggan_pdf(Request $req)
    {
        $query = Penjualan::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('dibuat_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('auditor_id')
                    ->wherenotNull('tanggal_dibuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_audit');

        if($req->gallery) {
            $query->where('lokasi_id', $req->gallery);
        }
        if($req->customer) {
            $query->where('id_customer', $req->customer);
        }
        if($req->tempo) {
            $query->where('jatuh_tempo','=', $req->tempo);
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }
            
        $penjualan = $query->get();

        $groupedPenjualan = $penjualan->groupBy('id_customer')->map(function ($group) {
            $group[0]->no_invoice = $group->pluck('no_invoice')->implode(', ');
            $group[0]->lokasi_id = $group->pluck('lokasi_id')->implode(',');
            $group[0]->tanggal_invoice = $group->pluck('tanggal_invoice')->implode(', ');
            $group[0]->jatuh_tempo = $group->pluck('jatuh_tempo')->implode(', ');
            
            $group[0]->total_tagihan = $group->pluck('total_tagihan')->implode(', ');
            $group[0]->dp = $group->pluck('dp')->implode(', ');
            $group[0]->sisa_bayar = $group->pluck('sisa_bayar')->implode(', ');

            return $group[0];
        });

        $penjualan = $groupedPenjualan->values();
        $galleries = Lokasi::whereIn('tipe_lokasi', [1,2])->get();
        $customers = Customer::whereNotNull('tanggal_bergabung')->get();

        if($penjualan->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.pelanggan_pdf', compact('penjualan'))->setPaper('a4', 'portrait');
        return $pdf->stream('pelanggan.pdf');

    }
    public function pelanggan_excel(Request $req)
    {
        $query = Penjualan::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('dibuat_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('auditor_id')
                    ->wherenotNull('tanggal_dibuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_audit');

        if($req->gallery) {
            $query->where('lokasi_id', $req->gallery);
        }
        if($req->customer) {
            $query->where('id_customer', $req->customer);
        }
        if($req->tempo) {
            $query->where('jatuh_tempo','=', $req->tempo);
        }
        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }
            
        $penjualan = $query->get();

        $groupedPenjualan = $penjualan->groupBy('id_customer')->map(function ($group) {
            $group[0]->no_invoice = $group->pluck('no_invoice')->implode(', ');
            $group[0]->lokasi_id = $group->pluck('lokasi_id')->implode(',');
            $group[0]->tanggal_invoice = $group->pluck('tanggal_invoice')->implode(', ');
            $group[0]->jatuh_tempo = $group->pluck('jatuh_tempo')->implode(', ');
            
            $group[0]->total_tagihan = $group->pluck('total_tagihan')->implode(', ');
            $group[0]->dp = $group->pluck('dp')->implode(', ');
            $group[0]->sisa_bayar = $group->pluck('sisa_bayar')->implode(', ');

            return $group[0];
        });

        $penjualan = $groupedPenjualan->values();
        $galleries = Lokasi::whereIn('tipe_lokasi', [1,2])->get();
        $customers = Customer::whereNotNull('tanggal_bergabung')->get();

        if($penjualan->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new PelangganExport($penjualan), 'pelanggan.xlsx');
    }

    public function pembayaran_index(Request $req)
    {
        $penjualan = Penjualan::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('dibuat_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('auditor_id')
                    ->wherenotNull('tanggal_dibuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_audit')
                    ->get();

        $duit = [];

        foreach($penjualan as $pj) {
            $query = Pembayaran::where('invoice_penjualan_id', $pj->id);
            if($req->metode) {
                $query->where('cara_bayar', $req->metode);
            }
            if($req->rekening) {
                $query->where('rekening_id', $req->rekening);
            }
            $pembayaran = $query->get();
            foreach($pembayaran as $bayar) {
                $carabayar = $bayar->rekening_id;
                if (isset($duit[$carabayar])) {
                    $duit[$carabayar] += $bayar->nominal;
                } else {
                    $duit[$carabayar] = $bayar->nominal;
                }
            }
        }

        $rekenings = Rekening::all();

        return view('laporan.pembayaran', compact('penjualan', 'pembayaran', 'duit', 'rekenings'));
    }

    public function pembayaran_pdf(Request $req)
    {
        $penjualan = Penjualan::where('status', 'DIKONFIRMASI')
                ->wherenotNull('dibuat_id')
                ->wherenotNull('dibukukan_id')
                ->wherenotNull('auditor_id')
                ->wherenotNull('tanggal_dibuat')
                ->wherenotNull('tanggal_dibukukan')
                ->wherenotNull('tanggal_audit')
                ->get();

        $duit = [];

        foreach($penjualan as $pj) {
        $query = Pembayaran::where('invoice_penjualan_id', $pj->id);
        if($req->metode) {
            $query->where('cara_bayar', $req->metode);
        }
        if($req->rekening) {
            $query->where('rekening_id', $req->rekening);
        }
        $pembayaran = $query->get();
        foreach($pembayaran as $bayar) {
            $carabayar = $bayar->rekening_id;
            if (isset($duit[$carabayar])) {
                $duit[$carabayar] += $bayar->nominal;
            } else {
                $duit[$carabayar] = $bayar->nominal;
            }
        }
        }

        $rekenings = Rekening::all();

        if($pembayaran->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.pembayaran_pdf', compact('pembayaran', 'duit'))->setPaper('a4', 'portrait');
        return $pdf->stream('pembayaran.pdf');
    }
    public function pembayaran_excel(Request $req)
    {
        $penjualan = Penjualan::where('status', 'DIKONFIRMASI')
                ->wherenotNull('dibuat_id')
                ->wherenotNull('dibukukan_id')
                ->wherenotNull('auditor_id')
                ->wherenotNull('tanggal_dibuat')
                ->wherenotNull('tanggal_dibukukan')
                ->wherenotNull('tanggal_audit')
                ->get();

        $duit = [];

        foreach($penjualan as $pj) {
        $query = Pembayaran::where('invoice_penjualan_id', $pj->id);
        if($req->metode) {
            $query->where('cara_bayar', $req->metode);
        }
        if($req->rekening) {
            $query->where('rekening_id', $req->rekening);
        }
        $pembayaran = $query->get();
        foreach($pembayaran as $bayar) {
            $carabayar = $bayar->rekening_id;
            if (isset($duit[$carabayar])) {
                $duit[$carabayar] += $bayar->nominal;
            } else {
                $duit[$carabayar] = $bayar->nominal;
            }
        }
        }

        $rekenings = Rekening::all();
        if($pembayaran->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new PembayaranExport($pembayaran, $duit), 'pembayaran.xlsx');
    }

    public function dopenjualan_index(Request $req)
    {
        $query = DeliveryOrder::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('pembuat')
                    ->wherenotNull('penyetuju')
                    ->wherenotNull('pemeriksa')
                    ->wherenotNull('tanggal_pembuat')
                    ->wherenotNull('tanggal_penyetuju')
                    ->wherenotNull('tanggal_pemeriksa');

        if($req->gallery) {
            $query->where('lokasi_pengirim', $req->gallery);
        }
        if($req->customer) {
            $query->where('customer_id', $req->customer);
        }
        if($req->tanggalkirim) {
            $query->where('tanggal_kirim', $req->tanggalkirim);
        }
        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }

        $dopenjualan = $query->get();

        $do = $dopenjualan->pluck('no_do')->toArray();
        $produkterjual = Produk_Terjual::with('komponen')->whereIn('no_do', $do)->get();

        $combinedData = [];
        
        foreach ($produkterjual as $produk) {
            $no_do = $produk->no_do;
            $penj_id = $produk->produk_jual_id;
            $penjnama = Produk_Jual::where('id', $penj_id)->first()->nama ?? '';
            
            if (!isset($combinedData[$no_do])) {
                $combinedData[$no_do] = [
                    'no_do' => $no_do,
                    'lokasi_pengirim' => $produk->do_penjualan->lokasi_kirim->nama,
                    'customer' => $produk->do_penjualan->customer->nama,
                    'penerima' => $produk->do_penjualan->penerima,
                    'tanggal_kirim' => $produk->do_penjualan->tanggal_kirim,
                    'tanggal_invoice' => $produk->do_penjualan->penjualan->tanggal_invoice,
                    'komponen' => [],
                ];
            }

            if (!isset($combinedData[$no_do]['produk_jual'][$penjnama])) {
                $combinedData[$no_do]['produk_jual'][$penjnama] = [
                    'nama_produkjual' => $penjnama,
                    'jumlahprodukjual' => $produk->jumlah,
                    'unitsatuan' => $produk->satuan,
                    'keterangan' => $produk->keterangan,
                    'komponen' => []
                ];
            }

            foreach ($produk->komponen as $komponen) {
                $kode_produk = $komponen->kode_produk;
                $kondisi_id = $komponen->kondisi;

                if (!isset($combinedData[$no_do]['produk_jual'][$penjnama]['komponen'][$kode_produk])) {
                    $combinedData[$no_do]['produk_jual'][$penjnama]['komponen'][$kode_produk] = [
                        'nama_produk' => $komponen->nama_produk,
                        'jumlah' => 0,
                        'kondisibaik' => 0,
                        'kondisiafkir' => 0,
                        'kondisibonggol' => 0
                    ];
                }

                $combinedData[$no_do]['produk_jual'][$penjnama]['komponen'][$kode_produk]['jumlah'] += $komponen->jumlah * $produk->jumlah;

                switch ($kondisi_id) {
                    case 1:
                        $combinedData[$no_do]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibaik'] += $komponen->jumlah * $produk->jumlah;
                        break;
                    case 2:
                        $combinedData[$no_do]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisiafkir'] += $komponen->jumlah * $produk->jumlah;
                        break;
                    case 3:
                        $combinedData[$no_do]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibonggol'] += $komponen->jumlah * $produk->jumlah;
                        break;
                }
            }
        }
        $galleries = Lokasi::whereIn('tipe_lokasi', [1,2])->get();
        $customers = Customer::whereNotNull('tanggal_bergabung')->get();

        return view('laporan.dopenjualan', compact('combinedData', 'galleries', 'customers'));
    }




    public function dopenjualan_pdf(Request $req)
    {
        // Query DeliveryOrder with filters
        $query = DeliveryOrder::where('status', 'DIKONFIRMASI')
            ->whereNotNull('pembuat')
            ->whereNotNull('penyetuju')
            ->whereNotNull('pemeriksa')
            ->whereNotNull('tanggal_pembuat')
            ->whereNotNull('tanggal_penyetuju')
            ->whereNotNull('tanggal_pemeriksa');

        if ($req->gallery) {
            $query->where('lokasi_pengirim', $req->gallery);
        }
        if ($req->customer) {
            $query->where('customer_id', $req->customer);
        }
        if ($req->tanggalkirim) {
            $query->where('tanggal_kirim', $req->tanggalkirim);
        }
        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }

        $dopenjualan = $query->get();

        // Retrieve Produk_Terjual with related Komponen
        $doNumbers = $dopenjualan->pluck('no_do')->toArray();
        $produkterjual = Produk_Terjual::with('komponen')->whereIn('no_do', $doNumbers)->get();

        // Combine data into a simplified array
        $combinedData = $dopenjualan->map(function ($do) use ($produkterjual) {
            $penjualan = $produkterjual->where('no_do', $do->no_do);
            $produkJualData = $penjualan->groupBy('produk_jual_id')->map(function ($group) {
                $produkJual = Produk_Jual::find($group->first()->produk_jual_id);
                $penjNama = $produkJual->nama ?? '';
                $kodeprod = $produkJual->kode ?? '';

                $komponenData = $group->flatMap(function ($produk) {
                    return $produk->komponen->map(function ($komponen) use ($produk) {
                        return [
                            'nama_produk' => $komponen->nama_produk,
                            'jumlah' => $komponen->jumlah * $produk->jumlah,
                            'kondisibaik' => $komponen->kondisi == 1 ? $komponen->jumlah * $produk->jumlah : 0,
                            'kondisiafkir' => $komponen->kondisi == 2 ? $komponen->jumlah * $produk->jumlah : 0,
                            'kondisibonggol' => $komponen->kondisi == 3 ? $komponen->jumlah * $produk->jumlah : 0,
                        ];
                    });
                })->groupBy('nama_produk')->map(function ($komponenGroup) {
                    return $komponenGroup->reduce(function ($carry, $item) {
                        $carry['jumlah'] += $item['jumlah'];
                        $carry['kondisibaik'] += $item['kondisibaik'];
                        $carry['kondisiafkir'] += $item['kondisiafkir'];
                        $carry['kondisibonggol'] += $item['kondisibonggol'];
                        return $carry;
                    }, [
                        'nama_produk' => $komponenGroup->first()['nama_produk'],
                        'jumlah' => 0,
                        'kondisibaik' => 0,
                        'kondisiafkir' => 0,
                        'kondisibonggol' => 0,
                    ]);
                });

                return [
                    'kodeprod' => $kodeprod,
                    'nama_produkjual' => $penjNama,
                    'jumlahprodukjual' => $group->sum('jumlah'),
                    'unitsatuan' => $group->first()->satuan,
                    'keterangan' => $group->first()->keterangan,
                    'komponen' => $komponenData
                ];
            })->sortBy(function ($item) {
                return strpos($item['kodeprod'], 'GFT') === 0 ? 1 : 0;
            });

            return [
                'no_do' => $do->no_do,
                'lokasi_pengirim' => $do->lokasi_kirim->nama,
                'tanggal_invoice' => $do->penjualan->tanggal_invoice,
                'customer' => $do->customer,
                'penerima' => $do->penerima,
                'tanggal_kirim' => $do->tanggal_kirim,
                'produk_jual' => $produkJualData
            ];
        });
        // dd($combinedData);
        // Load data into PDF
        $pdf = Pdf::loadView('laporan.dopenjualan_pdf', ['combinedData' => $combinedData])->setPaper('a4', 'portrait');

        // Return PDF as a download
        return $pdf->stream('dopenjualan.pdf');
    }

    public function dopenjualan_excel(Request $req)
    {
        $query = DeliveryOrder::where('status', 'DIKONFIRMASI')
            ->whereNotNull('pembuat')
            ->whereNotNull('penyetuju')
            ->whereNotNull('pemeriksa')
            ->whereNotNull('tanggal_pembuat')
            ->whereNotNull('tanggal_penyetuju')
            ->whereNotNull('tanggal_pemeriksa');

        if ($req->gallery) {
            $query->where('lokasi_pengirim', $req->gallery);
        }
        if ($req->customer) {
            $query->where('customer_id', $req->customer);
        }
        if ($req->tanggalkirim) {
            $query->where('tanggal_kirim', $req->tanggalkirim);
        }
        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }

        $dopenjualan = $query->get();

        // Retrieve Produk_Terjual with related Komponen
        $doNumbers = $dopenjualan->pluck('no_do')->toArray();
        $produkterjual = Produk_Terjual::with('komponen')->whereIn('no_do', $doNumbers)->get();

        // Combine data into a simplified array
        $combinedData = $dopenjualan->map(function ($do) use ($produkterjual) {
            $penjualan = $produkterjual->where('no_do', $do->no_do);
            $produkJualData = $penjualan->groupBy('produk_jual_id')->map(function ($group) {
                $produkJual = Produk_Jual::find($group->first()->produk_jual_id);
                $penjNama = $produkJual->nama ?? '';
                $kodeprod = $produkJual->kode ?? '';

                $komponenData = $group->flatMap(function ($produk) {
                    return $produk->komponen->map(function ($komponen) use ($produk) {
                        return [
                            'nama_produk' => $komponen->nama_produk,
                            'jumlah' => $komponen->jumlah * $produk->jumlah,
                            'kondisibaik' => $komponen->kondisi == 1 ? $komponen->jumlah * $produk->jumlah : 0,
                            'kondisiafkir' => $komponen->kondisi == 2 ? $komponen->jumlah * $produk->jumlah : 0,
                            'kondisibonggol' => $komponen->kondisi == 3 ? $komponen->jumlah * $produk->jumlah : 0,
                        ];
                    });
                })->groupBy('nama_produk')->map(function ($komponenGroup) {
                    return $komponenGroup->reduce(function ($carry, $item) {
                        $carry['jumlah'] += $item['jumlah'];
                        $carry['kondisibaik'] += $item['kondisibaik'];
                        $carry['kondisiafkir'] += $item['kondisiafkir'];
                        $carry['kondisibonggol'] += $item['kondisibonggol'];
                        return $carry;
                    }, [
                        'nama_produk' => $komponenGroup->first()['nama_produk'],
                        'jumlah' => 0,
                        'kondisibaik' => 0,
                        'kondisiafkir' => 0,
                        'kondisibonggol' => 0,
                    ]);
                });

                return [
                    'kodeprod' => $kodeprod,
                    'nama_produkjual' => $penjNama,
                    'jumlahprodukjual' => $group->sum('jumlah'),
                    'unitsatuan' => $group->first()->satuan,
                    'keterangan' => $group->first()->keterangan,
                    'komponen' => $komponenData
                ];
            })->sortBy(function ($item) {
                return strpos($item['kodeprod'], 'GFT') === 0 ? 1 : 0;
            });

            return [
                'no_do' => $do->no_do,
                'lokasi_pengirim' => $do->lokasi_kirim->nama,
                'customer' => $do->customer,
                'penerima' => $do->penerima,
                'tanggal_kirim' => $do->tanggal_kirim,
                'tanggal_invoice' => $do->penjualan->tanggal_invoice,
                'produk_jual' => $produkJualData
            ];
        });

        if($combinedData->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new DeliveryOrderExport($combinedData), 'deliveryorderpenjualan.xlsx');
    }

    public function returpenjualan_index(Request $req)
    {
        $query = ReturPenjualan::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('pembuat')
                    ->wherenotNull('pembuku')
                    ->wherenotNull('pemeriksa')
                    ->wherenotNull('tanggal_pembuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_diperiksa');

        if($req->gallery) {
            $query->where('lokasi_id', $req->gallery);
        }
        if($req->customer) {
            $query->where('customer_id', $req->customer);
        }
        if($req->supplier) {
            $query->where('supplier_id', $req->supplier);
        }
        if($req->komplain) {
            $query->where('komplain', $req->komplain);
        }
        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }

        $returpenjualan = $query->get();

        $produkterjual = collect(); 
        $penjualan = collect(); 

        foreach($returpenjualan as $retur) {
            $produkterjualItem = Produk_Terjual::where('no_retur', $retur->no_retur)->get();
            $penjualanItem = Penjualan::where('no_invoice', $retur->no_invoice)->get();
            
            $produkterjual = $produkterjual->merge($produkterjualItem);
            $penjualan = $penjualan->merge($penjualanItem);
        }
        $galleries = Lokasi::whereIn('tipe_lokasi', [1,2])->get();
        $customers = Customer::whereNotNull('tanggal_bergabung')->get();
        $supplier = Supplier::all();


        return view('laporan.returpenjualan', compact('returpenjualan', 'produkterjual', 'penjualan','galleries', 'customers', 'supplier'));
    }



    public function returpenjualan_pdf(Request $req)
    {
        $query = ReturPenjualan::where('status', 'DIKONFIRMASI')
            ->whereNotNull('pembuat')
            ->whereNotNull('pembuku')
            ->whereNotNull('pemeriksa')
            ->whereNotNull('tanggal_pembuat')
            ->whereNotNull('tanggal_dibukukan')
            ->whereNotNull('tanggal_diperiksa');

        // Apply filters based on request parameters
        if ($req->gallery) {
            $query->where('lokasi_id', $req->gallery);
        }
        if ($req->customer) {
            $query->where('customer_id', $req->customer);
        }
        if ($req->supplier) {
            $query->where('supplier_id', $req->supplier);
        }
        if ($req->komplain) {
            $query->where('komplain', $req->komplain);
        }
        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }

        // Step 2: Fetch the filtered ReturPenjualan records
        $returpenjualan = $query->get();

        // Step 3: Fetch related data for each ReturPenjualan
        $combinedData = $returpenjualan->map(function ($retur) {
            return [
                'retur' => $retur,
                'produkterjual' => Produk_Terjual::where('no_retur', $retur->no_retur)->get(),
                'penjualan' => Penjualan::where('no_invoice', $retur->no_invoice)->get(),
            ];
        });

        // dd($combinedData);

        $pdf = Pdf::loadView('laporan.returpenjualan_pdf', compact('combinedData'))->setPaper('a4', 'portrait');
        return $pdf->stream('returpenjualan.pdf');
    }

    public function returpenjualan_excel(Request $req)
    {
        $query = ReturPenjualan::where('status', 'DIKONFIRMASI')
            ->whereNotNull('pembuat')
            ->whereNotNull('pembuku')
            ->whereNotNull('pemeriksa')
            ->whereNotNull('tanggal_pembuat')
            ->whereNotNull('tanggal_dibukukan')
            ->whereNotNull('tanggal_diperiksa');

        // Apply filters based on request parameters
        if ($req->gallery) {
            $query->where('lokasi_id', $req->gallery);
        }
        if ($req->customer) {
            $query->where('customer_id', $req->customer);
        }
        if ($req->supplier) {
            $query->where('supplier_id', $req->supplier);
        }
        if ($req->komplain) {
            $query->where('komplain', $req->komplain);
        }
        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }

        // Step 2: Fetch the filtered ReturPenjualan records
        $returpenjualan = $query->get();

        // Step 3: Fetch related data for each ReturPenjualan
        $combinedData = $returpenjualan->map(function ($retur) {
            return [
                'retur' => $retur,
                'produkterjual' => Produk_Terjual::where('no_retur', $retur->no_retur)->get(),
                'penjualan' => Penjualan::where('no_invoice', $retur->no_invoice)->get(),
            ];
        });
        if($combinedData->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new ReturPenjualanExport($combinedData), 'returpenjualan.xlsx');
    }

    public function penjualan_index(Request $req)
    {
        $querypenj = Penjualan::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('dibuat_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('auditor_id')
                    ->wherenotNull('tanggal_dibuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_audit');

        if($req->gallery) {
            $querypenj->where('lokasi_id', $req->gallery);
        }
        if($req->customer) {
            $querypenj->where('id_customer', $req->customer);
        }
        if($req->sales) {
            $querypenj->where('employee_id', $req->sales);
        }
        if($req->tempo) {
            $querypenj->where('jatuh_tempo','=', $req->tempo);
        }
        if ($req->dateStart) {
            $querypenj->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $querypenj->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $penjualan = $querypenj->get();
        

        $invoice = $penjualan->pluck('no_invoice')->toArray();
        $produkterjual = Produk_Terjual::with('komponen')->whereIn('no_invoice', $invoice)->get();

        $combinedData = [];
        
        foreach ($produkterjual as $produk) {
            $no_invoice = $produk->no_invoice;
            $penj_id = $produk->produk_jual_id;
            $penjnama = Produk_Jual::where('id', $penj_id)->first()->nama ?? '';
            
            if (!isset($combinedData[$no_invoice])) {
                $combinedData[$no_invoice] = [
                    'no_invoice' => $no_invoice,
                    'lokasi_pengirim' => $produk->penjualan->lokasi->nama,
                    'customer' => $produk->penjualan->customer->nama,
                    'tanggal_invoice' => $produk->penjualan->tanggal_invoice,
                    'jatuh_tempo' => $produk->penjualan->jatuh_tempo,
                    'sales' => $produk->penjualan->karyawan->nama,
                    'sub_total' => $produk->penjualan->sub_total,
                    'jumlah_ppn' => $produk->penjualan->jumlah_ppn,
                    'biaya_pengiriman' => $produk->penjualan->biaya_ongkir,
                    'total_tagihan' => $produk->penjualan->total_tagihan,
                    'dp' => $produk->penjualan->dp,
                    'sisa_bayar' => $produk->penjualan->sisa_bayar,
                    'komponen' => [],
                ];
            }

            if (!isset($combinedData[$no_invoice]['produk_jual'][$penjnama])) {
                $combinedData[$no_invoice]['produk_jual'][$penjnama] = [
                    'nama_produkjual' => $penjnama,
                    'jumlahprodukjual' => $produk->jumlah,
                    'harga' => $produk->harga,
                    'diskon' => $produk->diskon,
                    'jumlah_harga' => $produk->harga_jual,
                    'komponen' => []
                ];
            }

            foreach ($produk->komponen as $komponen) {
                $kode_produk = $komponen->kode_produk;
                $kondisi_id = $komponen->kondisi;

                if (!isset($combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk])) {
                    $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk] = [
                        'nama_produk' => $komponen->nama_produk,
                        'jumlah' => 0,
                        'kondisibaik' => 0,
                        'kondisiafkir' => 0,
                        'kondisibonggol' => 0
                    ];
                }

                $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['jumlah'] += $komponen->jumlah * $produk->jumlah;

                switch ($kondisi_id) {
                    case 1:
                        $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibaik'] += $komponen->jumlah * $produk->jumlah;
                        break;
                    case 2:
                        $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisiafkir'] += $komponen->jumlah * $produk->jumlah;
                        break;
                    case 3:
                        $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibonggol'] += $komponen->jumlah * $produk->jumlah;
                        break;
                }
            }
        }
        $galleries = Lokasi::whereIn('tipe_lokasi', [1,2])->get();
        $customers = Customer::whereNotNull('tanggal_bergabung')->get();
        $sales  = Karyawan::where('jabatan', 'Sales')->get();

        return view('laporan.penjualan', compact('combinedData', 'galleries', 'customers', 'sales'));
    }



    public function penjualan_pdf(Request $req)
    {
        $querypenj = Penjualan::where('status', 'DIKONFIRMASI')
                ->wherenotNull('dibuat_id')
                ->wherenotNull('dibukukan_id')
                ->wherenotNull('auditor_id')
                ->wherenotNull('tanggal_dibuat')
                ->wherenotNull('tanggal_dibukukan')
                ->wherenotNull('tanggal_audit');

        if($req->gallery) {
        $querypenj->where('lokasi_id', $req->gallery);
        }
        if($req->customer) {
        $querypenj->where('id_customer', $req->customer);
        }
        if($req->sales) {
        $querypenj->where('employee_id', $req->sales);
        }
        if($req->tempo) {
        $querypenj->where('jatuh_tempo','=', $req->tempo);
        }
        if ($req->dateStart) {
        $querypenj->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
        $querypenj->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $penjualan = $querypenj->get();


        $invoice = $penjualan->pluck('no_invoice')->toArray();
        $produkterjual = Produk_Terjual::with('komponen')->whereIn('no_invoice', $invoice)->get();

        $combinedData = [];

        foreach ($produkterjual as $produk) {
            $no_invoice = $produk->no_invoice;
            $penj_id = $produk->produk_jual_id;
            $penjnama = Produk_Jual::where('id', $penj_id)->first()->nama ?? '';
            $penjkode = Produk_Jual::where('id', $penj_id)->first()->kode ?? '';

            if (!isset($combinedData[$no_invoice])) {
                $combinedData[$no_invoice] = [
                    'no_invoice' => $no_invoice,
                    'lokasi_pengirim' => $produk->penjualan->lokasi->nama,
                    'customer' => $produk->penjualan->customer->nama,
                    'tanggal_invoice' => $produk->penjualan->tanggal_invoice,
                    'jatuh_tempo' => $produk->penjualan->jatuh_tempo,
                    'sales' => $produk->penjualan->karyawan->nama,
                    'sub_total' => $produk->penjualan->sub_total,
                    'jumlah_ppn' => $produk->penjualan->jumlah_ppn,
                    'biaya_pengiriman' => $produk->penjualan->biaya_ongkir,
                    'total_tagihan' => $produk->penjualan->total_tagihan,
                    'dp' => $produk->penjualan->dp,
                    'sisa_bayar' => $produk->penjualan->sisa_bayar,
                    'komponen' => [],
                ];
            }

            if (!isset($combinedData[$no_invoice]['produk_jual'][$penjnama])) {
                $combinedData[$no_invoice]['produk_jual'][$penjnama] = [
                    'kode_produkjual' => $penjkode,
                    'nama_produkjual' => $penjnama,
                    'jumlahprodukjual' => $produk->jumlah,
                    'harga' => $produk->harga,
                    'diskon' => $produk->diskon,
                    'jumlah_harga' => $produk->harga_jual,
                    'komponen' => []
                ];
            }

            foreach ($produk->komponen as $komponen) {
                $kode_produk = $komponen->kode_produk;
                $kondisi_id = $komponen->kondisi;

                if (!isset($combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk])) {
                    $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk] = [
                        'kode_produk' =>$komponen->kode_produk,
                        'nama_produk' => $komponen->nama_produk,
                        'jumlah' => 0,
                        'kondisibaik' => 0,
                        'kondisiafkir' => 0,
                        'kondisibonggol' => 0
                    ];
                }

                $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['jumlah'] += $komponen->jumlah * $produk->jumlah;

                switch ($kondisi_id) {
                    case 1:
                        $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibaik'] += $komponen->jumlah * $produk->jumlah;
                        break;
                    case 2:
                        $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisiafkir'] += $komponen->jumlah * $produk->jumlah;
                        break;
                    case 3:
                        $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibonggol'] += $komponen->jumlah * $produk->jumlah;
                        break;
                }
            }
        }
        foreach ($combinedData as &$data) {
            uasort($data['produk_jual'], function($a, $b) {
                $aIsGFT = substr($a['kode_produkjual'], 0, 3) === 'GFT';
                $bIsGFT = substr($b['kode_produkjual'], 0, 3) === 'GFT';
        
                if ($aIsGFT && !$bIsGFT) {
                    return 1; // $a should come after $b
                } elseif (!$aIsGFT && $bIsGFT) {
                    return -1; // $a should come before $b
                } else {
                    return 0; // keep order
                }
            });
        }
        // dd($combinedData);
        $pdf = Pdf::loadView('laporan.penjualan_pdf', compact('combinedData'))->setPaper('a4', 'portrait');
        return $pdf->stream('penjualan.pdf');
    }
    public function penjualan_excel(Request $req)
    {
        $querypenj = Penjualan::where('status', 'DIKONFIRMASI')
                ->wherenotNull('dibuat_id')
                ->wherenotNull('dibukukan_id')
                ->wherenotNull('auditor_id')
                ->wherenotNull('tanggal_dibuat')
                ->wherenotNull('tanggal_dibukukan')
                ->wherenotNull('tanggal_audit');

        if($req->gallery) {
        $querypenj->where('lokasi_id', $req->gallery);
        }
        if($req->customer) {
        $querypenj->where('id_customer', $req->customer);
        }
        if($req->sales) {
        $querypenj->where('employee_id', $req->sales);
        }
        if($req->tempo) {
        $querypenj->where('jatuh_tempo','=', $req->tempo);
        }
        if ($req->dateStart) {
        $querypenj->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
        $querypenj->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $penjualan = $querypenj->get();


        $invoice = $penjualan->pluck('no_invoice')->toArray();
        $produkterjual = Produk_Terjual::with('komponen')->whereIn('no_invoice', $invoice)->get();

        $combinedData = [];

        foreach ($produkterjual as $produk) {
            $no_invoice = $produk->no_invoice;
            $penj_id = $produk->produk_jual_id;
            $penjnama = Produk_Jual::where('id', $penj_id)->first()->nama ?? '';
            $penjkode = Produk_Jual::where('id', $penj_id)->first()->kode ?? '';

            if (!isset($combinedData[$no_invoice])) {
                $combinedData[$no_invoice] = [
                    'no_invoice' => $no_invoice,
                    'lokasi_pengirim' => $produk->penjualan->lokasi->nama,
                    'customer' => $produk->penjualan->customer->nama,
                    'tanggal_invoice' => $produk->penjualan->tanggal_invoice,
                    'jatuh_tempo' => $produk->penjualan->jatuh_tempo,
                    'sales' => $produk->penjualan->karyawan->nama,
                    'sub_total' => $produk->penjualan->sub_total,
                    'jumlah_ppn' => $produk->penjualan->jumlah_ppn,
                    'biaya_pengiriman' => $produk->penjualan->biaya_ongkir,
                    'total_tagihan' => $produk->penjualan->total_tagihan,
                    'dp' => $produk->penjualan->dp,
                    'sisa_bayar' => $produk->penjualan->sisa_bayar,
                    'komponen' => [],
                ];
            }

            if (!isset($combinedData[$no_invoice]['produk_jual'][$penjnama])) {
                $combinedData[$no_invoice]['produk_jual'][$penjnama] = [
                    'kode_produkjual' => $penjkode,
                    'nama_produkjual' => $penjnama,
                    'jumlahprodukjual' => $produk->jumlah,
                    'harga' => $produk->harga,
                    'diskon' => $produk->diskon,
                    'jumlah_harga' => $produk->harga_jual,
                    'komponen' => []
                ];
            }

            foreach ($produk->komponen as $komponen) {
                $kode_produk = $komponen->kode_produk;
                $kondisi_id = $komponen->kondisi;

                if (!isset($combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk])) {
                    $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk] = [
                        'kode_produk' =>$komponen->kode_produk,
                        'nama_produk' => $komponen->nama_produk,
                        'jumlah' => 0,
                        'kondisibaik' => 0,
                        'kondisiafkir' => 0,
                        'kondisibonggol' => 0
                    ];
                }

                $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['jumlah'] += $komponen->jumlah * $produk->jumlah;

                switch ($kondisi_id) {
                    case 1:
                        $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibaik'] += $komponen->jumlah * $produk->jumlah;
                        break;
                    case 2:
                        $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisiafkir'] += $komponen->jumlah * $produk->jumlah;
                        break;
                    case 3:
                        $combinedData[$no_invoice]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibonggol'] += $komponen->jumlah * $produk->jumlah;
                        break;
                }
            }
        }
        foreach ($combinedData as &$data) {
            uasort($data['produk_jual'], function($a, $b) {
                $aIsGFT = substr($a['kode_produkjual'], 0, 3) === 'GFT';
                $bIsGFT = substr($b['kode_produkjual'], 0, 3) === 'GFT';
        
                if ($aIsGFT && !$bIsGFT) {
                    return 1; // $a should come after $b
                } elseif (!$aIsGFT && $bIsGFT) {
                    return -1; // $a should come before $b
                } else {
                    return 0; // keep order
                }
            });
        }
        if($produkterjual->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new PenjualanExport($combinedData), 'penjualan.xlsx');
    }
    public function mutasi_index(Request $req)
    {
        $query = Mutasi::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('pembuat_id')
                    ->wherenotNull('penerima_id')
                    ->wherenotNull('diperiksa_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('tanggal_pembuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_diperiksa')
                    ->wherenotNull('tanggal_penerima');
                    
        if ($req->pengirim) {
            $query->where('pengirim', $req->input('pengirim'));
        }
        if ($req->penerima) {
            $query->where('penerima', $req->input('penerima'));
        }
        if ($req->jenismutasi) {
            $query->where('no_mutasi', 'LIKE', $req->input('jenismutasi'));
        }
        if ($req->tanggalkirim) {
            $query->where('tanggal_kirim', '=', $req->input('tanggalkirim'));
        }
        if ($req->tanggalditerima) {
            $query->where('tanggal_diterima', '=', $req->input('tanggalditerima'));
        }
        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }

        $penjualan = $query->get();

        $mutasi = $penjualan->pluck('no_mutasi')->toArray();
        $produkterjual = collect();
        $combinedData = [];

        foreach ($mutasi as $nomorMutasi) {

            $prefix = substr($nomorMutasi, 0, 3);
            $produkTerjualItems = collect(); 
        
            switch ($prefix) {
                case 'MGO':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasigo', $nomorMutasi)->get();
                    break;
                case 'MOG':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasiog', $nomorMutasi)->get();
                    break;
                case 'MGG':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasigg', $nomorMutasi)->get();
                    break;
                case 'MGA':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasigag', $nomorMutasi)->get();
                    break;
                default:
                    continue 2;
            }
        
            $produkterjual = $produkterjual->merge($produkTerjualItems);
        
            foreach ($produkterjual as $produk) {
                switch ($prefix) {
                    case 'MGO':
                        $no_mutasi = $produk->no_mutasigo;
                        break;
                    case 'MOG':
                        $no_mutasi = $produk->no_mutasiog;
                        break;
                    case 'MGG':
                        $no_mutasi = $produk->no_mutasigg;
                        break;
                    case 'MGA':
                        $no_mutasi = $produk->no_mutasigag;
                        break;
                }
        
                // Jika no_mutasi kosong atau null, lewati entri ini
                if (empty($no_mutasi)) {
                    continue;
                }
        
                $penj_id = $produk->produk_jual_id;
                $penjnama = Produk_Jual::where('id', $penj_id)->first()->nama ?? '';
        
                if (!isset($combinedData[$no_mutasi])) {
                    $combinedData[$no_mutasi] = [
                        'no_mutasi' => $no_mutasi,
                        'lokasi_pengirim' => '',
                        'lokasi_penerima' => '',
                        'tanggal_pengiriman' => '',
                        'tanggal_diterima' => '',
                        'biaya_pengiriman' => '',
                        'rekening' => '',
                        'total_biaya' => '',
                        'produk_jual' => [],
                    ];
                }
        
                switch ($prefix) {
                    case 'MGO':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasi->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasi->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasi->tanggal_kirim ?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasi->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasi->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasi->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasi->total_biaya ?? '';
                        break;
                    case 'MOG':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasiog->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasiog->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasiog->tanggal_kirim ?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasiog->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasiog->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasiog->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasiog->total_biaya ?? '';
                        break;
                    case 'MGG':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasigg->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasigg->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasigg->tanggal_kirim ?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasigg->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasigg->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasigg->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasigg->total_biaya ?? '';
                        break;
                    case 'MGA':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasigag->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasigag->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasigag->tanggal_kirim?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasigag->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasigag->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasigag->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasigag->total_biaya ?? '';
                        break;
                }
        
                if (!isset($combinedData[$no_mutasi]['produk_jual'][$penjnama])) {
                    $combinedData[$no_mutasi]['produk_jual'][$penjnama] = [
                        'nama_produkjual' => $penjnama,
                        'jumlahprodukjual' => 0,
                        'jumlah_diterima' => 0,
                        'komponen' => [],
                    ];
                }
        
                $combinedData[$no_mutasi]['produk_jual'][$penjnama]['jumlahprodukjual'] += $produk->jumlah;
                $combinedData[$no_mutasi]['produk_jual'][$penjnama]['jumlah_diterima'] += $produk->jumlah_diterima;
        
                foreach ($produk->komponen as $komponen) {
                    $kode_produk = $komponen->kode_produk;
                    $kondisi_id = $komponen->kondisi;
        
                    if (!isset($combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk])) {
                        $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk] = [
                            'nama_produk' => $komponen->nama_produk,
                            'kondisi' => $komponen->data_kondisi->nama,
                            'kondisi_diterima' => $komponen->kondisi_dit,
                            'kondisibaik' => 0,
                            'kondisiafkir' => 0,
                            'kondisibonggol' => 0,
                            'jumlah' => 0,
                        ];
                    }
        
                    $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['jumlah'] += $komponen->jumlah * $produk->jumlah;
        
                    switch ($kondisi_id) {
                        case 1:
                            $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibaik'] += $komponen->jumlah * $produk->jumlah;
                            break;
                        case 2:
                            $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisiafkir'] += $komponen->jumlah * $produk->jumlah;
                            break;
                        case 3:
                            $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibonggol'] += $komponen->jumlah * $produk->jumlah;
                            break;
                    }
                }
            }
        }
        
        $galleries = Lokasi::all();


        return view('laporan.mutasi', compact('combinedData', 'galleries'));
    }

    public function mutasi_pdf(Request $req)
    {
        $query = Mutasi::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('pembuat_id')
                    ->wherenotNull('penerima_id')
                    ->wherenotNull('diperiksa_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('tanggal_pembuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_diperiksa')
                    ->wherenotNull('tanggal_penerima');
                    
        if ($req->pengirim) {
            $query->where('pengirim', $req->input('pengirim'));
        }
        if ($req->penerima) {
            $query->where('penerima', $req->input('penerima'));
        }
        if ($req->jenismutasi) {
            $query->where('no_mutasi', 'LIKE', $req->input('jenismutasi'));
        }
        if ($req->tanggalkirim) {
            $query->where('tanggal_kirim', '=', $req->input('tanggalkirim'));
        }
        if ($req->tanggalditerima) {
            $query->where('tanggal_diterima', '=', $req->input('tanggalditerima'));
        }
        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }

        $penjualan = $query->get();

        $mutasi = $penjualan->pluck('no_mutasi')->toArray();
        $produkterjual = collect();
        $combinedData = [];

        foreach ($mutasi as $nomorMutasi) {

            $prefix = substr($nomorMutasi, 0, 3);
            $produkTerjualItems = collect(); 
        
            switch ($prefix) {
                case 'MGO':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasigo', $nomorMutasi)->get();
                    break;
                case 'MOG':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasiog', $nomorMutasi)->get();
                    break;
                case 'MGG':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasigg', $nomorMutasi)->get();
                    break;
                case 'MGA':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasigag', $nomorMutasi)->get();
                    break;
                default:
                    continue 2;
            }
        
            $produkterjual = $produkterjual->merge($produkTerjualItems);
        
            foreach ($produkterjual as $produk) {
                switch ($prefix) {
                    case 'MGO':
                        $no_mutasi = $produk->no_mutasigo;
                        break;
                    case 'MOG':
                        $no_mutasi = $produk->no_mutasiog;
                        break;
                    case 'MGG':
                        $no_mutasi = $produk->no_mutasigg;
                        break;
                    case 'MGA':
                        $no_mutasi = $produk->no_mutasigag;
                        break;
                }
        
                // Jika no_mutasi kosong atau null, lewati entri ini
                if (empty($no_mutasi)) {
                    continue;
                }
        
                $penj_id = $produk->produk_jual_id;
                $penjnama = Produk_Jual::where('id', $penj_id)->first()->nama ?? '';
        
                if (!isset($combinedData[$no_mutasi])) {
                    $combinedData[$no_mutasi] = [
                        'no_mutasi' => $no_mutasi,
                        'lokasi_pengirim' => '',
                        'lokasi_penerima' => '',
                        'tanggal_pengiriman' => '',
                        'tanggal_diterima' => '',
                        'biaya_pengiriman' => '',
                        'rekening' => '',
                        'total_biaya' => '',
                        'produk_jual' => [],
                    ];
                }
        
                switch ($prefix) {
                    case 'MGO':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasi->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasi->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasi->tanggal_kirim ?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasi->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasi->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasi->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasi->total_biaya ?? '';
                        break;
                    case 'MOG':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasiog->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasiog->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasiog->tanggal_kirim ?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasiog->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasiog->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasiog->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasiog->total_biaya ?? '';
                        break;
                    case 'MGG':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasigg->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasigg->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasigg->tanggal_kirim ?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasigg->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasigg->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasigg->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasigg->total_biaya ?? '';
                        break;
                    case 'MGA':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasigag->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasigag->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasigag->tanggal_kirim?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasigag->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasigag->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasigag->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasigag->total_biaya ?? '';
                        break;
                }
        
                if (!isset($combinedData[$no_mutasi]['produk_jual'][$penjnama])) {
                    $combinedData[$no_mutasi]['produk_jual'][$penjnama] = [
                        'nama_produkjual' => $penjnama,
                        'jumlahprodukjual' => 0,
                        'jumlah_diterima' => 0,
                        'komponen' => [],
                    ];
                }
        
                $combinedData[$no_mutasi]['produk_jual'][$penjnama]['jumlahprodukjual'] += $produk->jumlah;
                $combinedData[$no_mutasi]['produk_jual'][$penjnama]['jumlah_diterima'] += $produk->jumlah_diterima;
        
                foreach ($produk->komponen as $komponen) {
                    $kode_produk = $komponen->kode_produk;
                    $kondisi_id = $komponen->kondisi;
        
                    if (!isset($combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk])) {
                        $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk] = [
                            'nama_produk' => $komponen->nama_produk,
                            'kondisi' => $komponen->data_kondisi->nama,
                            'kondisi_diterima' => $komponen->kondisi_dit,
                            'kondisibaik' => 0,
                            'kondisiafkir' => 0,
                            'kondisibonggol' => 0,
                            'jumlah' => 0,
                        ];
                    }
        
                    $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['jumlah'] += $komponen->jumlah * $produk->jumlah;
        
                    switch ($kondisi_id) {
                        case 1:
                            $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibaik'] += $komponen->jumlah * $produk->jumlah;
                            break;
                        case 2:
                            $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisiafkir'] += $komponen->jumlah * $produk->jumlah;
                            break;
                        case 3:
                            $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibonggol'] += $komponen->jumlah * $produk->jumlah;
                            break;
                    }
                }
            }
        }
        // dd($combinedData);
        $pdf = Pdf::loadView('laporan.mutasi_pdf', compact('combinedData'))->setPaper('a4', 'portrait');
        return $pdf->stream('mutasi.pdf');
    }
    public function mutasi_excel(Request $req)
    {
        $query = Mutasi::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('pembuat_id')
                    ->wherenotNull('penerima_id')
                    ->wherenotNull('diperiksa_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('tanggal_pembuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_diperiksa')
                    ->wherenotNull('tanggal_penerima');
                    
        if ($req->pengirim) {
            $query->where('pengirim', $req->input('pengirim'));
        }
        if ($req->penerima) {
            $query->where('penerima', $req->input('penerima'));
        }
        if ($req->jenismutasi) {
            $query->where('no_mutasi', 'LIKE', $req->input('jenismutasi'));
        }
        if ($req->tanggalkirim) {
            $query->where('tanggal_kirim', '=', $req->input('tanggalkirim'));
        }
        if ($req->tanggalditerima) {
            $query->where('tanggal_diterima', '=', $req->input('tanggalditerima'));
        }
        if ($req->dateStart) {
            $query->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('created_at', '<=', $req->input('dateEnd'));
        }

        $penjualan = $query->get();

        $mutasi = $penjualan->pluck('no_mutasi')->toArray();
        $produkterjual = collect();
        $combinedData = [];

        foreach ($mutasi as $nomorMutasi) {

            $prefix = substr($nomorMutasi, 0, 3);
            $produkTerjualItems = collect(); 
        
            switch ($prefix) {
                case 'MGO':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasigo', $nomorMutasi)->get();
                    break;
                case 'MOG':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasiog', $nomorMutasi)->get();
                    break;
                case 'MGG':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasigg', $nomorMutasi)->get();
                    break;
                case 'MGA':
                    $produkTerjualItems = Produk_Terjual::with('komponen')->where('no_mutasigag', $nomorMutasi)->get();
                    break;
                default:
                    continue 2;
            }
        
            $produkterjual = $produkterjual->merge($produkTerjualItems);
        
            foreach ($produkterjual as $produk) {
                switch ($prefix) {
                    case 'MGO':
                        $no_mutasi = $produk->no_mutasigo;
                        break;
                    case 'MOG':
                        $no_mutasi = $produk->no_mutasiog;
                        break;
                    case 'MGG':
                        $no_mutasi = $produk->no_mutasigg;
                        break;
                    case 'MGA':
                        $no_mutasi = $produk->no_mutasigag;
                        break;
                }
        
                // Jika no_mutasi kosong atau null, lewati entri ini
                if (empty($no_mutasi)) {
                    continue;
                }
        
                $penj_id = $produk->produk_jual_id;
                $penjnama = Produk_Jual::where('id', $penj_id)->first()->nama ?? '';
        
                if (!isset($combinedData[$no_mutasi])) {
                    $combinedData[$no_mutasi] = [
                        'no_mutasi' => $no_mutasi,
                        'lokasi_pengirim' => '',
                        'lokasi_penerima' => '',
                        'tanggal_pengiriman' => '',
                        'tanggal_diterima' => '',
                        'biaya_pengiriman' => '',
                        'rekening' => '',
                        'total_biaya' => '',
                        'produk_jual' => [],
                    ];
                }
        
                switch ($prefix) {
                    case 'MGO':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasi->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasi->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasi->tanggal_kirim ?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasi->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasi->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasi->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasi->total_biaya ?? '';
                        break;
                    case 'MOG':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasiog->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasiog->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasiog->tanggal_kirim ?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasiog->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasiog->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasiog->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasiog->total_biaya ?? '';
                        break;
                    case 'MGG':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasigg->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasigg->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasigg->tanggal_kirim ?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasigg->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasigg->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasigg->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasigg->total_biaya ?? '';
                        break;
                    case 'MGA':
                        $combinedData[$no_mutasi]['lokasi_pengirim'] = $produk->mutasigag->lokasi->nama ?? '';
                        $combinedData[$no_mutasi]['lokasi_penerima'] = $produk->mutasigag->lokasi_penerima->nama ?? '';
                        $combinedData[$no_mutasi]['tanggal_pengiriman'] = $produk->mutasigag->tanggal_kirim?? '';
                        $combinedData[$no_mutasi]['tanggal_diterima'] = $produk->mutasigag->tanggal_diterima ?? '';
                        $combinedData[$no_mutasi]['biaya_pengiriman'] = $produk->mutasigag->biaya_pengiriman ?? '';
                        $combinedData[$no_mutasi]['rekening'] = $produk->mutasigag->rekening->nama_akun ?? '';
                        $combinedData[$no_mutasi]['total_biaya'] = $produk->mutasigag->total_biaya ?? '';
                        break;
                }
        
                if (!isset($combinedData[$no_mutasi]['produk_jual'][$penjnama])) {
                    $combinedData[$no_mutasi]['produk_jual'][$penjnama] = [
                        'nama_produkjual' => $penjnama,
                        'jumlahprodukjual' => 0,
                        'jumlah_diterima' => 0,
                        'komponen' => [],
                    ];
                }
        
                $combinedData[$no_mutasi]['produk_jual'][$penjnama]['jumlahprodukjual'] += $produk->jumlah;
                $combinedData[$no_mutasi]['produk_jual'][$penjnama]['jumlah_diterima'] += $produk->jumlah_diterima;
        
                foreach ($produk->komponen as $komponen) {
                    $kode_produk = $komponen->kode_produk;
                    $kondisi_id = $komponen->kondisi;
        
                    if (!isset($combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk])) {
                        $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk] = [
                            'nama_produk' => $komponen->nama_produk,
                            'kondisi' => $komponen->data_kondisi->nama,
                            'kondisi_diterima' => $komponen->kondisi_dit,
                            'kondisibaik' => 0,
                            'kondisiafkir' => 0,
                            'kondisibonggol' => 0,
                            'jumlah' => 0,
                        ];
                    }
        
                    $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['jumlah'] += $komponen->jumlah * $produk->jumlah;
        
                    switch ($kondisi_id) {
                        case 1:
                            $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibaik'] += $komponen->jumlah * $produk->jumlah;
                            break;
                        case 2:
                            $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisiafkir'] += $komponen->jumlah * $produk->jumlah;
                            break;
                        case 3:
                            $combinedData[$no_mutasi]['produk_jual'][$penjnama]['komponen'][$kode_produk]['kondisibonggol'] += $komponen->jumlah * $produk->jumlah;
                            break;
                    }
                }
            }
        }

        if($produkterjual->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new MutasiExport($combinedData), 'mutasi.xlsx');

    }

    
}
