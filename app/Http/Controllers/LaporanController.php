<?php

namespace App\Http\Controllers;

use App\Exports\DOSewaExport;
use App\Exports\KasGalleryExport;
use App\Exports\KasPusatExport;
use App\Exports\KontrakExport;
use App\Exports\RekapPergantianExport;
use App\Exports\TagihanSewaExport;
use App\Exports\PenjualanProdukExport;
use App\Exports\PelangganExport;
use App\Exports\DeliveryOrderExport;
use App\Exports\HutangSupplierExport;
use App\Exports\MutasiExport;
use App\Exports\MutasiindenExport;
use App\Exports\OmsetExport;
use App\Exports\PenjualanExport;
use App\Exports\ReturPenjualanExport;
use App\Exports\PembayaranExport;
use App\Exports\PembelianExport;
use App\Exports\PembelianIndenExport;
use App\Exports\PromoExport;
use App\Exports\ReturPembelianExport;
use App\Exports\ReturPembelianIndenExport;
use App\Exports\StokIndenExport;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\Invoicepo;
use App\Models\InventoryInden;
use App\Models\InvoiceSewa;
use App\Models\KembaliSewa;
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
use App\Models\Komponen_Produk_Terjual;
use App\Models\Pembayaran;
use App\Models\Rekening;
use App\Models\Mutasi;
use App\Models\Supplier;
use App\Models\ReturPenjualan;
use App\Models\Mutasiindens;
use App\Models\Pembelian;
use App\Models\Produk;
use App\Models\ProdukMutasiInden;
use App\Models\Returinden;
use App\Models\Returpembelian;
use App\Models\TransaksiKas;

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
            $item->total_sebelum_diskon = $item->subtotal + $item->total_promo;
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
            $item->total_sebelum_diskon = $item->subtotal + $item->total_promo;
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
            $item->total_sebelum_diskon = $item->subtotal + $item->total_promo;
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

    public function do_sewa_index(Request $req)
    {
        $query = DeliveryOrder::with(['produk' => function($q) {
            $q->whereNull('no_kembali_sewa');
            },'produk.produk', 'produk.komponen.produk', 'produk.komponen.data_kondisi', 'kontrak.customer', 'data_driver'])->where('jenis_do', 'SEWA')->where('status', 'DIKONFIRMASI');

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
            $query->where('tanggal_kirim', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kirim', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        $customer = Customer::whereHas('kontrak')->get();
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
        return view('laporan.do_sewa', compact('data', 'customer', 'galleries'));
    }

    public function do_sewa_pdf(Request $req)
    {
        $query = DeliveryOrder::with(['produk' => function($q) {
            $q->whereNull('no_kembali_sewa');
            },'produk.produk', 'produk.komponen.produk', 'produk.komponen.data_kondisi', 'kontrak.customer', 'data_driver'])->where('jenis_do', 'SEWA')->where('status', 'DIKONFIRMASI');

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
            $query->where('tanggal_kirim', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kirim', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.do_sewa_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('do_sewa.pdf');
    }

    public function do_sewa_excel(Request $req)
    {
        $query = DeliveryOrder::with(['produk' => function($q) {
            $q->whereNull('no_kembali_sewa');
            },'produk.produk', 'produk.komponen.produk', 'produk.komponen.data_kondisi', 'kontrak.customer', 'data_driver'])->where('jenis_do', 'SEWA')->where('status', 'DIKONFIRMASI');

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
            $query->where('tanggal_kirim', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_kirim', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new DOSewaExport($data), 'do_sewa.xlsx');
    }

    public function pergantian_sewa_index(Request $req)
    {
        $query = Kontrak::with(['produk.produk', 'customer'])
            ->where('status', 'DIKONFIRMASI');
        $tahun = $query->pluck('tanggal_kontrak')
            ->map(function ($date) {
                return date('Y', strtotime($date));
            })
            ->unique()
            ->sort()
            ->values();
        if($req->customer){
            $query->where('customer_id', $req->customer);
        }
        if($req->gallery){
            $query->where('lokasi_id', $req->gallery);
        }
        if($req->bulan){
            $query->whereMonth('tanggal_kontrak', $req->bulan);
        }
        if($req->tahun){
            $query->whereYear('tanggal_kontrak', $req->tahun);
        }
        $kontrak = $query->get();
    
        $data = [];
        
        $deliveryOrders = DeliveryOrder::with('produk')
            ->whereIn('no_referensi', $kontrak->pluck('no_kontrak'))
            ->where('status', 'DIKONFIRMASI')
            ->get()
            ->groupBy('no_referensi');
            
        $returns = KembaliSewa::with('produk')
            ->whereIn('no_sewa', $kontrak->pluck('no_kontrak'))
            ->where('status', 'DIKONFIRMASI')
            ->get()
            ->groupBy('no_sewa');
    
        foreach ($kontrak as $item) {
            $produk_list = [];
            
            $dataKirim = $deliveryOrders->get($item->no_kontrak, collect());
            $dataKembali = $returns->get($item->no_kontrak, collect());
    
            $kirimProdukMap = [];
            foreach ($dataKirim as $do) {
                foreach ($do->produk as $produk_terjual_do) {
                    if (is_null($produk_terjual_do->no_kembali_sewa)) {
                        $kode = $produk_terjual_do->produk->kode;
                        if (!isset($kirimProdukMap[$kode])) {
                            $kirimProdukMap[$kode] = 0;
                        }
                        $kirimProdukMap[$kode] += $produk_terjual_do->jumlah;
                    }
                }
            }
    
            $kembaliProdukMap = [];
            foreach ($dataKembali as $ks) {
                foreach ($ks->produk as $produk_terjual_ks) {
                    if (!is_null($produk_terjual_ks->no_kembali_sewa)) {
                        $kode = $produk_terjual_ks->produk->kode;
                        if (!isset($kembaliProdukMap[$kode])) {
                            $kembaliProdukMap[$kode] = 0;
                        }
                        $kembaliProdukMap[$kode] += $produk_terjual_ks->jumlah;
                    }
                }
            }
    
            $productMap = [];
    
            foreach ($item->produk as $produk_terjual) {
                $kode = $produk_terjual->produk->kode;
    
                if (!isset($productMap[$kode])) {
                    $productMap[$kode] = [
                        'nama_produk' => $produk_terjual->produk->nama,
                        'jumlah_sewa' => 0,
                        'jumlah_dikirim' => 0,
                        'jumlah_kembali' => 0,
                    ];
                    $productMap[$kode]['jumlah_sewa'] += $produk_terjual->jumlah;
                    $productMap[$kode]['jumlah_dikirim'] += $kirimProdukMap[$kode] ?? 0;
                    $productMap[$kode]['jumlah_kembali'] += $kembaliProdukMap[$kode] ?? 0;
                } else {
                    $productMap[$kode]['jumlah_sewa'] += $produk_terjual->jumlah;
                }
    
            }

            $produk_list = array_values($productMap);
    
            $data[$item->no_kontrak] = [
                'nama_customer' => $item->customer->nama,
                'produk_list' => $produk_list,
            ];
        }
    
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $customer = Customer::whereHas('kontrak')->get();
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
    
        return view('laporan.pergantian_sewa', compact('data', 'customer', 'galleries', 'bulan', 'tahun'));
    }

    public function pergantian_sewa_pdf(Request $req)
    {
        $query = Kontrak::with(['produk.produk', 'customer'])
            ->where('status', 'DIKONFIRMASI');
        $tahun = $query->pluck('tanggal_kontrak')
            ->map(function ($date) {
                return date('Y', strtotime($date));
            })
            ->unique()
            ->sort()
            ->values();
        if($req->customer){
            $query->where('customer_id', $req->customer);
        }
        if($req->gallery){
            $query->where('lokasi_id', $req->gallery);
        }
        if($req->bulan){
            $query->whereMonth('tanggal_kontrak', $req->bulan);
        }
        if($req->tahun){
            $query->whereYear('tanggal_kontrak', $req->tahun);
        }
        $kontrak = $query->get();
    
        $data = [];
        
        $deliveryOrders = DeliveryOrder::with('produk')
            ->whereIn('no_referensi', $kontrak->pluck('no_kontrak'))
            ->where('status', 'DIKONFIRMASI')
            ->get()
            ->groupBy('no_referensi');
            
        $returns = KembaliSewa::with('produk')
            ->whereIn('no_sewa', $kontrak->pluck('no_kontrak'))
            ->where('status', 'DIKONFIRMASI')
            ->get()
            ->groupBy('no_sewa');
    
        foreach ($kontrak as $item) {
            $produk_list = [];
            
            $dataKirim = $deliveryOrders->get($item->no_kontrak, collect());
            $dataKembali = $returns->get($item->no_kontrak, collect());
    
            $kirimProdukMap = [];
            foreach ($dataKirim as $do) {
                foreach ($do->produk as $produk_terjual_do) {
                    if (is_null($produk_terjual_do->no_kembali_sewa)) {
                        $kode = $produk_terjual_do->produk->kode;
                        if (!isset($kirimProdukMap[$kode])) {
                            $kirimProdukMap[$kode] = 0;
                        }
                        $kirimProdukMap[$kode] += $produk_terjual_do->jumlah;
                    }
                }
            }
    
            $kembaliProdukMap = [];
            foreach ($dataKembali as $ks) {
                foreach ($ks->produk as $produk_terjual_ks) {
                    if (!is_null($produk_terjual_ks->no_kembali_sewa)) {
                        $kode = $produk_terjual_ks->produk->kode;
                        if (!isset($kembaliProdukMap[$kode])) {
                            $kembaliProdukMap[$kode] = 0;
                        }
                        $kembaliProdukMap[$kode] += $produk_terjual_ks->jumlah;
                    }
                }
            }
    
            $productMap = [];
    
            foreach ($item->produk as $produk_terjual) {
                $kode = $produk_terjual->produk->kode;
    
                if (!isset($productMap[$kode])) {
                    $productMap[$kode] = [
                        'nama_produk' => $produk_terjual->produk->nama,
                        'jumlah_sewa' => 0,
                        'jumlah_dikirim' => 0,
                        'jumlah_kembali' => 0,
                    ];
                    $productMap[$kode]['jumlah_sewa'] += $produk_terjual->jumlah;
                    $productMap[$kode]['jumlah_dikirim'] += $kirimProdukMap[$kode] ?? 0;
                    $productMap[$kode]['jumlah_kembali'] += $kembaliProdukMap[$kode] ?? 0;
                } else {
                    $productMap[$kode]['jumlah_sewa'] += $produk_terjual->jumlah;
                }
    
            }

            $produk_list = array_values($productMap);
    
            $data[$item->no_kontrak] = [
                'nama_customer' => $item->customer->nama,
                'produk_list' => $produk_list,
            ];
        }
    
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        if(empty($data)) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.pergantian_sewa_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('pergantian_sewa.pdf');
    }

    public function pergantian_sewa_excel(Request $req)
    {
        $query = Kontrak::with(['produk.produk', 'customer'])
            ->where('status', 'DIKONFIRMASI');
        $tahun = $query->pluck('tanggal_kontrak')
            ->map(function ($date) {
                return date('Y', strtotime($date));
            })
            ->unique()
            ->sort()
            ->values();
        if($req->customer){
            $query->where('customer_id', $req->customer);
        }
        if($req->gallery){
            $query->where('lokasi_id', $req->gallery);
        }
        if($req->bulan){
            $query->whereMonth('tanggal_kontrak', $req->bulan);
        }
        if($req->tahun){
            $query->whereYear('tanggal_kontrak', $req->tahun);
        }
        $kontrak = $query->get();
    
        $data = [];
        
        $deliveryOrders = DeliveryOrder::with('produk')
            ->whereIn('no_referensi', $kontrak->pluck('no_kontrak'))
            ->where('status', 'DIKONFIRMASI')
            ->get()
            ->groupBy('no_referensi');
            
        $returns = KembaliSewa::with('produk')
            ->whereIn('no_sewa', $kontrak->pluck('no_kontrak'))
            ->where('status', 'DIKONFIRMASI')
            ->get()
            ->groupBy('no_sewa');
    
        foreach ($kontrak as $item) {
            $produk_list = [];
            
            $dataKirim = $deliveryOrders->get($item->no_kontrak, collect());
            $dataKembali = $returns->get($item->no_kontrak, collect());
    
            $kirimProdukMap = [];
            foreach ($dataKirim as $do) {
                foreach ($do->produk as $produk_terjual_do) {
                    if (is_null($produk_terjual_do->no_kembali_sewa)) {
                        $kode = $produk_terjual_do->produk->kode;
                        if (!isset($kirimProdukMap[$kode])) {
                            $kirimProdukMap[$kode] = 0;
                        }
                        $kirimProdukMap[$kode] += $produk_terjual_do->jumlah;
                    }
                }
            }
    
            $kembaliProdukMap = [];
            foreach ($dataKembali as $ks) {
                foreach ($ks->produk as $produk_terjual_ks) {
                    if (!is_null($produk_terjual_ks->no_kembali_sewa)) {
                        $kode = $produk_terjual_ks->produk->kode;
                        if (!isset($kembaliProdukMap[$kode])) {
                            $kembaliProdukMap[$kode] = 0;
                        }
                        $kembaliProdukMap[$kode] += $produk_terjual_ks->jumlah;
                    }
                }
            }
    
            $productMap = [];
    
            foreach ($item->produk as $produk_terjual) {
                $kode = $produk_terjual->produk->kode;
    
                if (!isset($productMap[$kode])) {
                    $productMap[$kode] = [
                        'nama_produk' => $produk_terjual->produk->nama,
                        'jumlah_sewa' => 0,
                        'jumlah_dikirim' => 0,
                        'jumlah_kembali' => 0,
                    ];
                    $productMap[$kode]['jumlah_sewa'] += $produk_terjual->jumlah;
                    $productMap[$kode]['jumlah_dikirim'] += $kirimProdukMap[$kode] ?? 0;
                    $productMap[$kode]['jumlah_kembali'] += $kembaliProdukMap[$kode] ?? 0;
                } else {
                    $productMap[$kode]['jumlah_sewa'] += $produk_terjual->jumlah;
                }
    
            }

            $produk_list = array_values($productMap);
    
            $data[$item->no_kontrak] = [
                'nama_customer' => $item->customer->nama,
                'produk_list' => $produk_list,
            ];
        }
    
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        if(empty($data)) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new RekapPergantianExport($data), 'pergantian_sewa_sewa.xlsx');
    }

    public function kas_gallery_index(Request $req)
    {
        $query = TransaksiKas::with(['lok_penerima', 'lok_pengirim', 'rek_penerima', 'rek_pengirim'])
            ->where('status', 'DIKONFIRMASI');
        $tahun = $query->pluck('tanggal')
            ->map(function ($date) {
                return date('Y', strtotime($date));
            })
            ->unique()
            ->sort()
            ->values();
        
        if($req->gallery){
            $query->where('lokasi_penerima', $req->gallery)
            ->orWhere('lokasi_pengirim', $req->gallery);
            $id_galleries = $req->gallery;
        } else {
           $id_galleries = Lokasi::where('tipe_lokasi', 1)->pluck('id')->toArray();

            $query->where(function($query) use ($id_galleries) {
                $query->whereIn('lokasi_penerima', $id_galleries)
                    ->orWhereIn('lokasi_pengirim', $id_galleries);
            });
        }
        
        if($req->bulan){
            $query->whereMonth('tanggal', $req->bulan);
            $thisMonth = $req->bulan;
        } else {
            $query->whereMonth('tanggal', now()->month);
            $thisMonth = sprintf('%02d', now()->month);
        }
        if($req->tahun){
            $query->whereYear('tanggal', $req->tahun);
            $thisYear = $req->tahun;
        } else {
            $query->whereYear('tanggal', now()->year);
            $thisYear = now()->year;
        }
        $startDate = $thisYear . '-' . $thisMonth . '-01';
        $saldo = TransaksiKas::getSaldoLokasi($id_galleries, $startDate);
        $tempSaldo = $saldo;
        $saldoRekening = 0;
        $saldoCash = 0;
        $data = $query->get()->map(function($item) use(&$tempSaldo, &$saldoRekening, &$saldoCash, $id_galleries) {
            if($item->lokasi_penerima == $id_galleries) {
                $tempSaldo += $item->nominal;
                if($item->metode == 'Transfer'){
                    $saldoRekening += $item->nominal;
                } else {
                    $saldoCash += $item->nominal;
                }
            } 
            if($item->lokasi_pengirim == $id_galleries) {
                $tempSaldo -= ($item->nominal + $item->biaya_lain);
                if($item->metode == 'Transfer'){
                    $saldoRekening -= ($item->nominal + $item->biaya_lain);
                } else {
                    $saldoCash -= ($item->nominal + $item->biaya_lain);
                }
                $item->nominal = ($item->nominal + $item->biaya_lain);
            }
            $item->dateNumber = Carbon::parse($item->tanggal)->format('d');
            $item->saldo = $tempSaldo;
            return $item;
        })->sortBy('tanggal');
        $lastItem = $data->last();
        $totalSaldo = $lastItem ? $lastItem->saldo : 0;
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $thisMonth = $bulan['' . $thisMonth . ''];
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
    
        return view('laporan.kas_gallery', compact('data', 'galleries', 'bulan', 'tahun', 'thisMonth', 'thisYear', 'saldo', 'totalSaldo', 'saldoRekening', 'saldoCash', 'id_galleries'));
    }

    public function kas_gallery_pdf(Request $req)
    {
        $query = TransaksiKas::with(['lok_penerima', 'lok_pengirim', 'rek_penerima', 'rek_pengirim'])
            ->where('status', 'DIKONFIRMASI');

        if($req->gallery){
            $query->where('lokasi_penerima', $req->gallery)
            ->orWhere('lokasi_pengirim', $req->gallery);
            $id_galleries = $req->gallery;
        } else {
           $id_galleries = Lokasi::where('tipe_lokasi', 1)->pluck('id')->toArray();

            $query->where(function($query) use ($id_galleries) {
                $query->whereIn('lokasi_penerima', $id_galleries)
                    ->orWhereIn('lokasi_pengirim', $id_galleries);
            });
        }
        if($req->bulan){
            $query->whereMonth('tanggal', $req->bulan);
            $thisMonth = $req->bulan;
        } else {
            $query->whereMonth('tanggal', now()->month);
            $thisMonth = sprintf('%02d', now()->month);
        }
        if($req->tahun){
            $query->whereYear('tanggal', $req->tahun);
            $thisYear = $req->tahun;
        } else {
            $query->whereYear('tanggal', now()->year);
            $thisYear = now()->year;
        }
        $startDate = $thisYear . '-' . $thisMonth . '-01';
        $saldo = TransaksiKas::getSaldoLokasi($id_galleries, $startDate);
        $tempSaldo = $saldo;
        $saldoRekening = 0;
        $saldoCash = 0;
        $data = $query->get()->map(function($item) use(&$tempSaldo, &$saldoRekening, &$saldoCash, $id_galleries) {
            if($item->lokasi_penerima == $id_galleries) {
                $tempSaldo += $item->nominal;
                if($item->metode == 'Transfer'){
                    $saldoRekening += $item->nominal;
                } else {
                    $saldoCash += $item->nominal;
                }
            } 
            if($item->lokasi_pengirim == $id_galleries) {
                $tempSaldo -= ($item->nominal + $item->biaya_lain);
                if($item->metode == 'Transfer'){
                    $saldoRekening -= ($item->nominal + $item->biaya_lain);
                } else {
                    $saldoCash -= ($item->nominal + $item->biaya_lain);
                }
                $item->nominal = ($item->nominal + $item->biaya_lain);
            }
            $item->dateNumber = Carbon::parse($item->tanggal)->format('d');
            $item->saldo = $tempSaldo;
            return $item;
        })->sortBy('tanggal');
        $lastItem = $data->last();
        $totalSaldo = $lastItem ? $lastItem->saldo : 0;
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $thisMonth = $bulan['' . $thisMonth . ''];

        if(empty($data)) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.kas_gallery_pdf', compact('data', 'thisMonth', 'thisYear', 'saldo', 'totalSaldo', 'saldoRekening', 'saldoCash', 'id_galleries'))->setPaper('a4', 'landscape');;
        return $pdf->stream('kas_gallery.pdf');
    }

    public function kas_gallery_excel(Request $req)
    {
        $query = TransaksiKas::with(['lok_penerima', 'lok_pengirim', 'rek_penerima', 'rek_pengirim'])
            ->where('status', 'DIKONFIRMASI');

        if($req->gallery){
            $query->where('lokasi_penerima', $req->gallery)
            ->orWhere('lokasi_pengirim', $req->gallery);
            $id_galleries = $req->gallery;
        } else {
           $id_galleries = Lokasi::where('tipe_lokasi', 1)->pluck('id')->toArray();

            $query->where(function($query) use ($id_galleries) {
                $query->whereIn('lokasi_penerima', $id_galleries)
                    ->orWhereIn('lokasi_pengirim', $id_galleries);
            });
        }
        if($req->bulan){
            $query->whereMonth('tanggal', $req->bulan);
            $thisMonth = $req->bulan;
        } else {
            $query->whereMonth('tanggal', now()->month);
            $thisMonth = sprintf('%02d', now()->month);
        }
        if($req->tahun){
            $query->whereYear('tanggal', $req->tahun);
            $thisYear = $req->tahun;
        } else {
            $query->whereYear('tanggal', now()->year);
            $thisYear = now()->year;
        }
        $startDate = $thisYear . '-' . $thisMonth . '-01';
        $saldo = TransaksiKas::getSaldoLokasi($id_galleries, $startDate);
        $tempSaldo = $saldo;
        $saldoRekening = 0;
        $saldoCash = 0;
        $data = $query->get()->map(function($item) use(&$tempSaldo, &$saldoRekening, &$saldoCash, $id_galleries) {
            if($item->lokasi_penerima == $id_galleries) {
                $tempSaldo += $item->nominal;
                if($item->metode == 'Transfer'){
                    $saldoRekening += $item->nominal;
                } else {
                    $saldoCash += $item->nominal;
                }
            } 
            if($item->lokasi_pengirim == $id_galleries) {
                $tempSaldo -= ($item->nominal + $item->biaya_lain);
                if($item->metode == 'Transfer'){
                    $saldoRekening -= ($item->nominal + $item->biaya_lain);
                } else {
                    $saldoCash -= ($item->nominal + $item->biaya_lain);
                }
                $item->nominal = ($item->nominal + $item->biaya_lain);
            }
            $item->dateNumber = Carbon::parse($item->tanggal)->format('d');
            $item->saldo = $tempSaldo;
            return $item;
        })->sortBy('tanggal');
        $lastItem = $data->last();
        $totalSaldo = $lastItem ? $lastItem->saldo : 0;
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $thisMonth = $bulan['' . $thisMonth . ''];

        if(empty($data)) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new KasGalleryExport($data, $thisMonth, $thisYear, $saldo, $totalSaldo, $saldoRekening, $saldoCash, $id_galleries), 'kas_gallery.xlsx');
    }

    public function kas_pusat_index(Request $req)
    {
        $query = TransaksiKas::with(['lok_penerima', 'lok_pengirim', 'rek_penerima', 'rek_pengirim'])
            ->where('status', 'DIKONFIRMASI');
        $tahun = $query->pluck('tanggal')
            ->map(function ($date) {
                return date('Y', strtotime($date));
            })
            ->unique()
            ->sort()
            ->values();

        // if($req->gallery){
        //     $query->where('lokasi_penerima', $req->gallery)
        //     ->orWhere('lokasi_pengirim', $req->gallery);
        //     $id_galleries = $req->gallery;
        // } else {
        $id_galleries = Lokasi::where('tipe_lokasi', 5)->pluck('id')->toArray();

        $query->where(function($query) use ($id_galleries) {
            $query->whereIn('lokasi_penerima', $id_galleries)
                ->orWhereIn('lokasi_pengirim', $id_galleries);
        });
        // }
        if($req->bulan){
            $query->whereMonth('tanggal', $req->bulan);
            $thisMonth = $req->bulan;
        } else {
            $query->whereMonth('tanggal', now()->month);
            $thisMonth = sprintf('%02d', now()->month);
        }
        if($req->tahun){
            $query->whereYear('tanggal', $req->tahun);
            $thisYear = $req->tahun;
        } else {
            $query->whereYear('tanggal', now()->year);
            $thisYear = now()->year;
        }
        $startDate = $thisYear . '-' . $thisMonth . '-01';
        $saldo = TransaksiKas::getSaldoLokasi($id_galleries, $startDate);
        $tempSaldo = $saldo;
        $saldoRekening = 0;
        $saldoCash = 0;
        $data = $query->get()->map(function($item) use(&$tempSaldo, &$saldoRekening, &$saldoCash, $id_galleries) {
            if (in_array($item->lokasi_penerima, $id_galleries)) {
                $tempSaldo += $item->nominal;
                if($item->metode == 'Transfer'){
                    $saldoRekening += $item->nominal;
                } else {
                    $saldoCash += $item->nominal;
                }
            } 
            if (in_array($item->lokasi_pengirim, $id_galleries)) {
                $tempSaldo -= ($item->nominal + $item->biaya_lain);
                if($item->metode == 'Transfer'){
                    $saldoRekening -= ($item->nominal + $item->biaya_lain);
                } else {
                    $saldoCash -= ($item->nominal + $item->biaya_lain);
                }
                $item->nominal = ($item->nominal + $item->biaya_lain);
            }
            $item->dateNumber = Carbon::parse($item->tanggal)->format('d');
            $item->saldo = $tempSaldo;
            return $item;
        })->sortBy('tanggal');
        $lastItem = $data->last();
        $totalSaldo = $lastItem ? $lastItem->saldo : 0;
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $thisMonth = $bulan['' . $thisMonth . ''];
        $galleries = Lokasi::where('tipe_lokasi', 5)->get();
    
        return view('laporan.kas_pusat', compact('data', 'galleries', 'bulan', 'tahun', 'thisMonth', 'thisYear', 'saldo', 'totalSaldo', 'saldoRekening', 'saldoCash', 'id_galleries'));
    }

    public function kas_pusat_pdf(Request $req)
    {
        $query = TransaksiKas::with(['lok_penerima', 'lok_pengirim', 'rek_penerima', 'rek_pengirim'])
            ->where('status', 'DIKONFIRMASI');

        // if($req->gallery){
        //     $query->where('lokasi_penerima', $req->gallery)
        //     ->orWhere('lokasi_pengirim', $req->gallery);
        //     $id_galleries = $req->gallery;
        // } else {
           $id_galleries = Lokasi::where('tipe_lokasi', 5)->pluck('id')->toArray();

            $query->where(function($query) use ($id_galleries) {
                $query->whereIn('lokasi_penerima', $id_galleries)
                    ->orWhereIn('lokasi_pengirim', $id_galleries);
            });
        // }
        if($req->bulan){
            $query->whereMonth('tanggal', $req->bulan);
            $thisMonth = $req->bulan;
        } else {
            $query->whereMonth('tanggal', now()->month);
            $thisMonth = sprintf('%02d', now()->month);
        }
        if($req->tahun){
            $query->whereYear('tanggal', $req->tahun);
            $thisYear = $req->tahun;
        } else {
            $query->whereYear('tanggal', now()->year);
            $thisYear = now()->year;
        }
        $startDate = $thisYear . '-' . $thisMonth . '-01';
        $saldo = TransaksiKas::getSaldoLokasi($id_galleries, $startDate);
        $tempSaldo = $saldo;
        $saldoRekening = 0;
        $saldoCash = 0;
        $data = $query->get()->map(function($item) use(&$tempSaldo, &$saldoRekening, &$saldoCash, $id_galleries) {
            if (in_array($item->lokasi_penerima, $id_galleries)) {
                $tempSaldo += $item->nominal;
                if($item->metode == 'Transfer'){
                    $saldoRekening += $item->nominal;
                } else {
                    $saldoCash += $item->nominal;
                }
            } 
            if (in_array($item->lokasi_pengirim, $id_galleries)) {
                $tempSaldo -= ($item->nominal + $item->biaya_lain);
                if($item->metode == 'Transfer'){
                    $saldoRekening -= ($item->nominal + $item->biaya_lain);
                } else {
                    $saldoCash -= ($item->nominal + $item->biaya_lain);
                }
                $item->nominal = ($item->nominal + $item->biaya_lain);
            }
            $item->dateNumber = Carbon::parse($item->tanggal)->format('d');
            $item->saldo = $tempSaldo;
            return $item;
        })->sortBy('tanggal');
        $lastItem = $data->last();
        $totalSaldo = $lastItem ? $lastItem->saldo : 0;
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $thisMonth = $bulan['' . $thisMonth . ''];

        if(empty($data)) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.kas_pusat_pdf', compact('data', 'thisMonth', 'thisYear', 'saldo', 'totalSaldo', 'saldoRekening', 'saldoCash', 'id_galleries'))->setPaper('a4', 'landscape');;
        return $pdf->stream('kas_pusat.pdf');
    }

    public function kas_pusat_excel(Request $req)
    {
        $query = TransaksiKas::with(['lok_penerima', 'lok_pengirim', 'rek_penerima', 'rek_pengirim'])
            ->where('status', 'DIKONFIRMASI');

        // if($req->gallery){
        //     $query->where('lokasi_penerima', $req->gallery)
        //     ->orWhere('lokasi_pengirim', $req->gallery);
        //     $id_galleries = $req->gallery;
        // } else {
           $id_galleries = Lokasi::where('tipe_lokasi', 5)->pluck('id')->toArray();

            $query->where(function($query) use ($id_galleries) {
                $query->whereIn('lokasi_penerima', $id_galleries)
                    ->orWhereIn('lokasi_pengirim', $id_galleries);
            });
        // }
        if($req->bulan){
            $query->whereMonth('tanggal', $req->bulan);
            $thisMonth = $req->bulan;
        } else {
            $query->whereMonth('tanggal', now()->month);
            $thisMonth = sprintf('%02d', now()->month);
        }
        if($req->tahun){
            $query->whereYear('tanggal', $req->tahun);
            $thisYear = $req->tahun;
        } else {
            $query->whereYear('tanggal', now()->year);
            $thisYear = now()->year;
        }
        $startDate = $thisYear . '-' . $thisMonth . '-01';
        $saldo = TransaksiKas::getSaldoLokasi($id_galleries, $startDate);
        $tempSaldo = $saldo;
        $saldoRekening = 0;
        $saldoCash = 0;
        $data = $query->get()->map(function($item) use(&$tempSaldo, &$saldoRekening, &$saldoCash, $id_galleries) {
            if (in_array($item->lokasi_penerima, $id_galleries)) {
                $tempSaldo += $item->nominal;
                if($item->metode == 'Transfer'){
                    $saldoRekening += $item->nominal;
                } else {
                    $saldoCash += $item->nominal;
                }
            } 
            if (in_array($item->lokasi_pengirim, $id_galleries)) {
                $tempSaldo -= ($item->nominal + $item->biaya_lain);
                if($item->metode == 'Transfer'){
                    $saldoRekening -= ($item->nominal + $item->biaya_lain);
                } else {
                    $saldoCash -= ($item->nominal + $item->biaya_lain);
                }
            }
            $item->nominal = ($item->nominal + $item->biaya_lain);
            $item->dateNumber = Carbon::parse($item->tanggal)->format('d');
            $item->saldo = $tempSaldo;
            return $item;
        })->sortBy('tanggal');
        $lastItem = $data->last();
        $totalSaldo = $lastItem ? $lastItem->saldo : 0;
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $thisMonth = $bulan['' . $thisMonth . ''];

        if(empty($data)) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new KasPusatExport($data, $thisMonth, $thisYear, $saldo, $totalSaldo, $saldoRekening, $saldoCash, $id_galleries), 'kas_pusat.xlsx');
    }

    public function pembelian_index(Request $req)
    {
        $query = Invoicepo::with(['pembelian.produkbeli'])->whereHas('pembelian')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->supplier) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('supplier_id', $req->supplier);
            });
        }
        if ($req->gallery) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        $supplier = Supplier::whereHas('pembelian')->get();
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
        return view('laporan.pembelian', compact('data', 'supplier', 'galleries'));
    }

    public function pembelian_pdf(Request $req)
    {
        $query = Invoicepo::with(['pembelian.produkbeli'])->whereHas('pembelian')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->supplier) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('supplier_id', $req->supplier);
            });
        }
        if ($req->gallery) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.pembelian_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('pembelian.pdf');
    }

    public function pembelian_excel(Request $req)
    {
        $query = Invoicepo::with(['pembelian.produkbeli'])->whereHas('pembelian')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->supplier) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('supplier_id', $req->supplier);
            });
        }
        if ($req->gallery) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new PembelianExport($data), 'pembelian.xlsx');
    }

    public function pembelian_inden_index(Request $req)
    {
        $query = Invoicepo::with(['poinden.produkbeli'])->whereHas('poinden')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->supplier) {
            $query->whereHas('poinden', function($q) use($req){
                $q->where('supplier_id', $req->supplier);
            });
        }
        if ($req->gallery) {
            $query->whereHas('poinden', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        $supplier = Supplier::whereHas('poinden')->get();
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
        return view('laporan.pembelian_inden', compact('data', 'supplier', 'galleries'));
    }

    public function pembelian_inden_pdf(Request $req)
    {
        $query = Invoicepo::with(['poinden.produkbeli'])->whereHas('poinden')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->supplier) {
            $query->whereHas('poinden', function($q) use($req){
                $q->where('supplier_id', $req->supplier);
            });
        }
        if ($req->gallery) {
            $query->whereHas('poinden', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.pembelian_inden_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('pembelian_inden.pdf');
    }

    public function pembelian_inden_excel(Request $req)
    {
        $query = Invoicepo::with(['poineden.produkbeli'])->whereHas('poinden')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->supplier) {
            $query->whereHas('poinden', function($q) use($req){
                $q->where('supplier_id', $req->supplier);
            });
        }
        if ($req->gallery) {
            $query->whereHas('poinden', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new PembelianIndenExport($data), 'pembelian_inden.xlsx');
    }

    public function stok_inden_index(Request $req)
    {
        $query = InventoryInden::with(['produk', 'supplier']);

        $allData = $query->get();

        $tahun = $allData->map(function($item) {
            $bulan_inden = explode('-', $item->bulan_inden);
            return count($bulan_inden) == 2 ? $bulan_inden[1] : null;
        })->filter()->unique();

        $supplier = $allData->mapWithKeys(function($item) {
            return [$item->supplier->id => $item->supplier->nama];
        })->unique();

        if ($req->supplier) {
            $query->where('supplier_id', $req->supplier);
        }
        if ($req->tahun) {
            $query->where('bulan_inden', 'LIKE', '%-' . $req->input('tahun'));
        }

        $result = $query->get();

        $produk = $result->mapWithKeys(function($item) {
            return [$item->produk->kode => $item->produk->nama];
        })->unique();

        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $data = array_fill_keys($bulan, array_fill_keys($produk->values()->toArray(), 0));

        foreach ($result as $item) {
            $bulan_inden = explode('-', $item->bulan_inden);
            if (count($bulan_inden) == 2) {
                $bulan_key = $bulan_inden[0];
                
                $bulan_key = array_search($bulan_key, $bulan);
                if ($bulan_key !== false) {
                    $bulan_name = $bulan[$bulan_key];
                    
                    $nama_produk = $item->produk->nama;
                    if (array_key_exists($nama_produk, $data[$bulan_name])) {
                        $data[$bulan_name][$nama_produk] += $item->jumlah;
                    }
                }
            }
        }

        $total = array_fill_keys($produk->values()->toArray(), 0);

        foreach ($data as $key => $value) {
            foreach ($value as $key1 => $item1) {
                $total[$key1] += $item1;
            }
        }

        $totalSisaBunga = array_sum($total);

        return view('laporan.stok_inden', compact('data', 'supplier', 'bulan', 'tahun', 'produk', 'total', 'totalSisaBunga'));
    }

    public function stok_inden_pdf(Request $req)
    {
        $query = InventoryInden::with(['produk', 'supplier']);

        if ($req->supplier) {
            $query->where('supplier_id', $req->supplier);
        }
        if ($req->tahun) {
            $query->where('bulan_inden', 'LIKE', '%-' . $req->input('tahun'));
        }

        $result = $query->get();

        $produk = $result->mapWithKeys(function($item) {
            return [$item->produk->kode => $item->produk->nama];
        })->unique();

        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $data = array_fill_keys($bulan, array_fill_keys($produk->values()->toArray(), 0));

        foreach ($result as $item) {
            $bulan_inden = explode('-', $item->bulan_inden);
            if (count($bulan_inden) == 2) {
                $bulan_key = $bulan_inden[0];
                
                $bulan_key = array_search($bulan_key, $bulan);
                if ($bulan_key !== false) {
                    $bulan_name = $bulan[$bulan_key];
                    
                    $nama_produk = $item->produk->nama;
                    if (array_key_exists($nama_produk, $data[$bulan_name])) {
                        $data[$bulan_name][$nama_produk] += $item->jumlah;
                    }
                }
            }
        }

        $total = array_fill_keys($produk->values()->toArray(), 0);

        foreach ($data as $key => $value) {
            foreach ($value as $key1 => $item1) {
                $total[$key1] += $item1;
            }
        }

        $totalSisaBunga = array_sum($total);

        if(empty($data)) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.stok_inden_pdf', compact('data', 'produk', 'total', 'totalSisaBunga'))->setPaper('a4', 'landscape');;
        return $pdf->stream('stok_inden.pdf');
    }

    public function stok_inden_excel(Request $req)
    {
        $query = InventoryInden::with(['produk', 'supplier']);

        if ($req->supplier) {
            $query->where('supplier_id', $req->supplier);
        }
        if ($req->tahun) {
            $query->where('bulan_inden', 'LIKE', '%-' . $req->input('tahun'));
        }

        $result = $query->get();

        $produk = $result->mapWithKeys(function($item) {
            return [$item->produk->kode => $item->produk->nama];
        })->unique();

        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];

        $data = array_fill_keys($bulan, array_fill_keys($produk->values()->toArray(), 0));

        foreach ($result as $item) {
            $bulan_inden = explode('-', $item->bulan_inden);
            if (count($bulan_inden) == 2) {
                $bulan_key = $bulan_inden[0];
                
                $bulan_key = array_search($bulan_key, $bulan);
                if ($bulan_key !== false) {
                    $bulan_name = $bulan[$bulan_key];
                    
                    $nama_produk = $item->produk->nama;
                    if (array_key_exists($nama_produk, $data[$bulan_name])) {
                        $data[$bulan_name][$nama_produk] += $item->jumlah;
                    }
                }
            }
        }

        $total = array_fill_keys($produk->values()->toArray(), 0);

        foreach ($data as $key => $value) {
            foreach ($value as $key1 => $item1) {
                $total[$key1] += $item1;
            }
        }

        $totalSisaBunga = array_sum($total);

        if(empty($data)) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new StokIndenExport($data, $produk, $total, $totalSisaBunga), 'pembelian_inden.xlsx');
    }

    public function hutang_supplier_index(Request $req)
    {
        $query = Invoicepo::where('status_dibuat', 'DIKONFIRMASI');

        $allData = $query->get();

        if ($req->supplier) {
            $query->where(function($q) use($req) {
                $q->whereHas('poinden', function($q) use($req) {
                    $q->where('supplier_id', $req->supplier);
                })
                ->orWhereHas('pembelian', function($q) use($req) {
                    $q->where('supplier_id', $req->supplier);
                });
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item){
            $supplier = null;
            $supplierName = null;
            $item->terbayar = $item->total_tagihan - $item->dp - $item->sisa;
            if ($item->poinden && $item->poinden->supplier) {
                $supplier = $item->poinden->supplier->id;
                $supplierName = $item->poinden->supplier->nama;
            }
        
            if (!$supplier && $item->pembelian && $item->pembelian->supplier) {
                $supplier = $item->pembelian->supplier->id;
                $supplierName = $item->pembelian->supplier->nama;
            }

            $item->supplier_nama = $supplierName;
            return $item;
        });

        $totalTagihan = $data->sum('sisa');

        $supplier = $allData->flatMap(function($item) {
            $supplier_id = null;
            $supplierName = null;
        
            if ($item->poinden && $item->poinden->supplier) {
                $supplier_id = $item->poinden->supplier_id;
                $supplierName = $item->poinden->supplier->nama;
            }
        
            if (!$supplier_id && $item->pembelian && $item->pembelian->supplier) {
                $supplier_id = $item->pembelian->supplier_id;
                $supplierName = $item->pembelian->supplier->nama;
            }
            return $supplier_id ? [$supplier_id => $supplierName] : [];
        })->unique();

        return view('laporan.hutang_supplier', compact('data', 'supplier', 'totalTagihan'));
    }

    public function hutang_supplier_pdf(Request $req)
    {
        $query = Invoicepo::with(['poinden.produkbeli', 'pembelian.produkbeli'])
            ->where('status_dibuat', 'DIKONFIRMASI');

        $allData = $query->get();

        if ($req->supplier) {
            $query->where(function($q) use($req) {
                $q->whereHas('poinden', function($q) use($req) {
                    $q->where('supplier_id', $req->supplier);
                })
                ->orWhereHas('pembelian', function($q) use($req) {
                    $q->where('supplier_id', $req->supplier);
                });
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item){
            $supplier = null;
            $supplierName = null;
            $item->terbayar = $item->total_tagihan - $item->dp - $item->sisa;
            if ($item->poinden && $item->poinden->supplier) {
                $supplier = $item->poinden->supplier->id;
                $supplierName = $item->poinden->supplier->nama;
            }
        
            if (!$supplier && $item->pembelian && $item->pembelian->supplier) {
                $supplier = $item->pembelian->supplier->id;
                $supplierName = $item->pembelian->supplier->nama;
            }

            $item->supplier_nama = $supplierName;
            return $item;
        });

        $totalTagihan = $data->sum('sisa');

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.hutang_supplier_pdf', compact('data', 'totalTagihan'))->setPaper('a4', 'landscape');;
        return $pdf->stream('hutang_supplier.pdf');
    }

    public function hutang_supplier_excel(Request $req)
    {
        $query = Invoicepo::with(['poinden.produkbeli', 'pembelian.produkbeli'])
            ->where('status_dibuat', 'DIKONFIRMASI');

        $allData = $query->get();

        if ($req->supplier) {
            $query->where(function($q) use($req) {
                $q->whereHas('poinden', function($q) use($req) {
                    $q->where('supplier_id', $req->supplier);
                })
                ->orWhereHas('pembelian', function($q) use($req) {
                    $q->where('supplier_id', $req->supplier);
                });
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item){
            $supplier = null;
            $supplierName = null;
            $item->terbayar = $item->total_tagihan - $item->dp - $item->sisa;
            if ($item->poinden && $item->poinden->supplier) {
                $supplier = $item->poinden->supplier->id;
                $supplierName = $item->poinden->supplier->nama;
            }
        
            if (!$supplier && $item->pembelian && $item->pembelian->supplier) {
                $supplier = $item->pembelian->supplier->id;
                $supplierName = $item->pembelian->supplier->nama;
            }

            $item->supplier_nama = $supplierName;
            return $item;
        });

        $totalTagihan = $data->sum('sisa');

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new HutangSupplierExport($data, $totalTagihan), 'hutang_supplier.xlsx');
    }

    public function retur_pembelian_index(Request $req)
    {
        $queryPO = Returpembelian::with('invoice.pembelian.lokasi', 'invoice.pembelian.supplier')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->dateStart) {
            $queryPO->where('tgl_retur', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $queryPO->where('tgl_retur', '<=', $req->input('dateEnd'));
        }

        $data = $queryPO->get()->map(function($item){
            $item->no_po = $item->invoice->pembelian->no_po;
            $item->supplier_nama = $item->invoice->pembelian->supplier->nama;
            $item->gallery_nama = $item->invoice->pembelian->lokasi->nama;
            return $item;
        });

        return view('laporan.retur_pembelian', compact('data'));
    }

    public function retur_pembelian_pdf(Request $req)
    {
        $queryPO = Returpembelian::with('invoice.pembelian.lokasi', 'invoice.pembelian.supplier')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->dateStart) {
            $queryPO->where('tgl_retur', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $queryPO->where('tgl_retur', '<=', $req->input('dateEnd'));
        }

        $data = $queryPO->get()->map(function($item){
            $item->no_po = $item->invoice->pembelian->no_po;
            $item->supplier_nama = $item->invoice->pembelian->supplier->nama;
            $item->gallery_nama = $item->invoice->pembelian->lokasi->nama;
            return $item;
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.retur_pembelian_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('retur_pembelian.pdf');
    }

    public function retur_pembelian_excel(Request $req)
    {
        $queryPO = Returpembelian::with('invoice.pembelian.lokasi', 'invoice.pembelian.supplier')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->dateStart) {
            $queryPO->where('tgl_retur', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $queryPO->where('tgl_retur', '<=', $req->input('dateEnd'));
        }

        $data = $queryPO->get()->map(function($item){
            $item->no_po = $item->invoice->pembelian->no_po;
            $item->supplier_nama = $item->invoice->pembelian->supplier->nama;
            $item->gallery_nama = $item->invoice->pembelian->lokasi->nama;
            return $item;
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new ReturPembelianExport($data), 'retur_pembelian.xlsx');
    }

    public function retur_pembelian_inden_index(Request $req)
    {
        $queryInden = Returinden::with('mutasiinden.lokasi', 'mutasiinden.supplier')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->dateStart) {
            $queryInden->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $queryInden->where('created_at', '<=', $req->input('dateEnd'));
        }

        $data = $queryInden->get()->map(function($item){
            $item->no_po = $item->mutasiinden->no_mutasi;
            $item->supplier_nama = $item->mutasiinden->supplier->nama;
            $item->gallery_nama = $item->mutasiinden->lokasi->nama;
            return $item;
        });

        return view('laporan.retur_pembelian_inden', compact('data'));
    }

    public function retur_pembelian_inden_pdf(Request $req)
    {
        $queryInden = Returinden::with('mutasiinden.lokasi', 'mutasiinden.supplier')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->dateStart) {
            $queryInden->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $queryInden->where('created_at', '<=', $req->input('dateEnd'));
        }

        $data = $queryInden->get()->map(function($item){
            $item->no_po = $item->mutasiinden->no_mutasi;
            $item->supplier_nama = $item->mutasiinden->supplier->nama;
            $item->gallery_nama = $item->mutasiinden->lokasi->nama;
            return $item;
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.retur_pembelian_inden_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('retur_pembelian_inden.pdf');
    }

    public function retur_pembelian_inden_excel(Request $req)
    {
        $queryInden = Returinden::with('mutasiinden.lokasi', 'mutasiinden.supplier')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->dateStart) {
            $queryInden->where('created_at', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $queryInden->where('created_at', '<=', $req->input('dateEnd'));
        }

        $data = $queryInden->get()->map(function($item){
            $item->no_po = $item->mutasiinden->no_mutasi;
            $item->supplier_nama = $item->mutasiinden->supplier->nama;
            $item->gallery_nama = $item->mutasiinden->lokasi->nama;
            return $item;
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new ReturPembelianIndenExport($data), 'retur_pembelian_inden.xlsx');
    }

    public function mergeItems($item, $collection) {
        $existingItem = $collection->firstWhere('no_invoice', $item->no_invoice);
        
        if (!$existingItem) {
            $collection->push($item);
        }
    }

    public function omset_index(Request $req)
    {
        // Eager load relationships and filter by status
        $querySewa = InvoiceSewa::with('kontrak.customer', 'kontrak.lokasi', 'data_sales', 'pembayaran', 'produk.produk')
            ->where('status', 'DIKONFIRMASI');
        $queryPenjualan = Penjualan::with('customer', 'lokasi', 'karyawan', 'pembayaran', 'produk.produk', 'ongkir')
            ->where('status', 'DIKONFIRMASI');


        if ($req->tipe_penjualan === 'Tradisional') {
            $querySewa->whereRaw('1 = 0');
        } elseif ($req->tipe_penjualan === 'Sewa') {
            $queryPenjualan->whereRaw('1 = 0');
        }

        // Retrieve data
        $allDataSewa = $querySewa->get();
        $allDataPenjualan = $queryPenjualan->get();

        // Extract unique product names
        $produkSewa = collect();
        $produkPenjualan = collect();

        foreach ($allDataSewa as $item) {
            if (isset($item->produk) && $item->produk->isNotEmpty()) {
                foreach ($item->produk as $produkItem) {
                    if (isset($produkItem->produk->id) && isset($produkItem->produk->nama)) {
                        $produkSewa->put($produkItem->produk->id, $produkItem->produk->nama);
                    }
                }
            }
        }
        
        foreach ($allDataPenjualan as $item) {
            if (isset($item->produk) && $item->produk->isNotEmpty()) {
                foreach ($item->produk as $produkItem) {
                    if (isset($produkItem->produk->id) && isset($produkItem->produk->nama)) {
                        $produkPenjualan->put($produkItem->produk->id, $produkItem->produk->nama);
                    }
                }
            }
        }

        $produkSewa = $produkSewa->unique();
        $produkPenjualan = $produkPenjualan->unique();

        $combinedProduk = $produkSewa->concat($produkPenjualan);

        $produk = $combinedProduk->unique(function ($item, $key) {
            return $item;
        });

        $produk = $produk->mapWithKeys(function ($item, $key) {
            return [$key => $item];
        });

        // Extract unique sales names
        $salesSewa = collect();
        $salesPenjualan = collect();

        foreach ($allDataSewa as $item) {
            if (isset($item->data_sales) && isset($item->data_sales->id) && isset($item->data_sales->nama)) {
                $salesSewa->put($item->data_sales->id, $item->data_sales->nama);
            }
        }
        foreach ($allDataPenjualan as $item) {
            if (isset($item->karyawan) && isset($item->karyawan->id) && isset($item->karyawan->nama)) {
                $salesPenjualan->put($item->karyawan->id, $item->karyawan->nama);
            }
        }

        $salesSewa = $salesSewa->unique();
        $salesPenjualan = $salesPenjualan->unique();
        
        $combinedSales = $salesSewa->concat($salesPenjualan);

        $sales = $combinedSales->unique(function ($item, $key) {
            return $item;
        });

        $sales = $sales->mapWithKeys(function ($item, $key) {
            return [$key => $item];
        });

        // Apply filters based on request
        if ($req->tanggal_invoice) {
            $querySewa->whereDate('tanggal_invoice', $req->tanggal_invoice);
            $queryPenjualan->whereDate('tanggal_invoice', $req->tanggal_invoice);
        }
        if ($req->tanggal_pembayaran) {
            $querySewa->whereHas('pembayaran', function($q) use($req) {
                $q->where('tanggal_bayar', $req->tanggal_pembayaran);
            });
            $queryPenjualan->whereHas('pembayaran', function($q) use($req) {
                $q->where('tanggal_bayar', $req->tanggal_pembayaran);
            });
        }
        if ($req->produk) {
            $querySewa->whereHas('produk', function($q) use($req) {
                $q->where('produk_jual_id', $req->produk);
            });
            $queryPenjualan->whereHas('produk', function($q) use($req) {
                $q->where('produk_jual_id', $req->produk);
            });
        }
        if ($req->sales) {
            $querySewa->where('sales', $req->input('sales'));
            $queryPenjualan->where('employee_id', $req->input('sales'));
        }
        if ($req->status == 'Sudah Dibayar') {
            $querySewa->whereHas('pembayaran');
            $queryPenjualan->whereHas('pembayaran');
        } elseif ($req->status == 'Belum Dibayar') {
            $querySewa->whereDoesntHave('pembayaran');
            $queryPenjualan->whereDoesntHave('pembayaran');
        }

        // Map data and format fields
        $dataSewa = $querySewa->get()->map(function($item) {
            $item->nama_sales = $item->data_sales->nama;
            $item->nama_customer = $item->kontrak->customer->nama;
            $item->jumlah = formatRupiah(($item->subtotal + $item->total_promo));
            if ($item->pembayaran->isEmpty()) {
                $metode = 'Belum ada pembayaran';
            } else {
                $metode = $item->pembayaran->pluck('cara_bayar')
                    ->map(function($caraBayar) {
                        return ucfirst($caraBayar);
                    })
                    ->unique()
                    ->implode(', ');
            }
            $item->metode = $metode;
            $item->ppn_nominal = formatRupiah($item->ppn_nominal);
            $item->pph_nominal = formatRupiah($item->pph_nominal);
            $item->ongkir_nominal = formatRupiah($item->ongkir_nominal);
            $item->total_promo = formatRupiah($item->total_promo);
            $item->total_tagihan = formatRupiah($item->total_tagihan);
            return $item;
        });
        $dataPenjualan = $queryPenjualan->get()->map(function($item) {
            $item->nama_sales = $item->karyawan->nama;
            $item->nama_customer = $item->customer->nama;
            $item->jumlah = formatRupiah($item->sub_total);
            if ($item->pembayaran->isEmpty()) {
                $metode = 'Belum ada pembayaran';
            } else {
                $metode = $item->pembayaran->pluck('cara_bayar')
                    ->map(function($caraBayar) {
                        return ucfirst($caraBayar);
                    })
                    ->unique()
                    ->implode(', ');
            }
            $item->metode = $metode;
            $item->ppn_nominal = formatRupiah($item->jumlah_ppn);
            $item->pph_nominal = formatRupiah(0);
            $item->ongkir_nominal = formatRupiah($item->biaya_ongkir);
            $item->total_promo = formatRupiah($item->total_promo);
            $item->total_tagihan = formatRupiah($item->total_tagihan);
            return $item;
        });
        $data = collect();

        // Add items from $dataSewa
        $dataSewa->each(function ($item) use ($data) {
            $this->mergeItems($item, $data);
        });

        // Add items from $dataPenjualan
        $dataPenjualan->each(function ($item) use ($data) {
            $this->mergeItems($item, $data);
        });
        
        return view('laporan.omset', compact('data', 'sales', 'produk'));
    }

    public function omset_pdf(Request $req)
    {
        // Eager load relationships and filter by status
        $querySewa = InvoiceSewa::with('kontrak.customer', 'kontrak.lokasi', 'data_sales', 'pembayaran', 'produk.produk')
            ->where('status', 'DIKONFIRMASI');
        $queryPenjualan = Penjualan::with('customer', 'lokasi', 'karyawan', 'pembayaran', 'produk.produk', 'ongkir')
            ->where('status', 'DIKONFIRMASI');


        if ($req->tipe_penjualan === 'Tradisional') {
            $querySewa->whereRaw('1 = 0');
        } elseif ($req->tipe_penjualan === 'Sewa') {
            $queryPenjualan->whereRaw('1 = 0');
        }

        // Retrieve data
        $allDataSewa = $querySewa->get();
        $allDataPenjualan = $queryPenjualan->get();

        // Extract unique product names
        $produkSewa = collect();
        $produkPenjualan = collect();

        foreach ($allDataSewa as $item) {
            if (isset($item->produk) && $item->produk->isNotEmpty()) {
                foreach ($item->produk as $produkItem) {
                    if (isset($produkItem->produk->id) && isset($produkItem->produk->nama)) {
                        $produkSewa->put($produkItem->produk->id, $produkItem->produk->nama);
                    }
                }
            }
        }
        
        foreach ($allDataPenjualan as $item) {
            if (isset($item->produk) && $item->produk->isNotEmpty()) {
                foreach ($item->produk as $produkItem) {
                    if (isset($produkItem->produk->id) && isset($produkItem->produk->nama)) {
                        $produkPenjualan->put($produkItem->produk->id, $produkItem->produk->nama);
                    }
                }
            }
        }

        $produkSewa = $produkSewa->unique();
        $produkPenjualan = $produkPenjualan->unique();

        $combinedProduk = $produkSewa->concat($produkPenjualan);

        $produk = $combinedProduk->unique(function ($item, $key) {
            return $item;
        });

        $produk = $produk->mapWithKeys(function ($item, $key) {
            return [$key => $item];
        });

        // Extract unique sales names
        $salesSewa = collect();
        $salesPenjualan = collect();

        foreach ($allDataSewa as $item) {
            if (isset($item->data_sales) && isset($item->data_sales->id) && isset($item->data_sales->nama)) {
                $salesSewa->put($item->data_sales->id, $item->data_sales->nama);
            }
        }
        foreach ($allDataPenjualan as $item) {
            if (isset($item->karyawan) && isset($item->karyawan->id) && isset($item->karyawan->nama)) {
                $salesPenjualan->put($item->karyawan->id, $item->karyawan->nama);
            }
        }

        $salesSewa = $salesSewa->unique();
        $salesPenjualan = $salesPenjualan->unique();
        
        $combinedSales = $salesSewa->concat($salesPenjualan);

        $sales = $combinedSales->unique(function ($item, $key) {
            return $item;
        });

        $sales = $sales->mapWithKeys(function ($item, $key) {
            return [$key => $item];
        });

        // Apply filters based on request
        if ($req->tanggal_invoice) {
            $querySewa->whereDate('tanggal_invoice', $req->tanggal_invoice);
            $queryPenjualan->whereDate('tanggal_invoice', $req->tanggal_invoice);
        }
        if ($req->tanggal_pembayaran) {
            $querySewa->whereHas('pembayaran', function($q) use($req) {
                $q->where('tanggal_bayar', $req->tanggal_pembayaran);
            });
            $queryPenjualan->whereHas('pembayaran', function($q) use($req) {
                $q->where('tanggal_bayar', $req->tanggal_pembayaran);
            });
        }
        if ($req->produk) {
            $querySewa->whereHas('produk', function($q) use($req) {
                $q->where('produk_jual_id', $req->produk);
            });
            $queryPenjualan->whereHas('produk', function($q) use($req) {
                $q->where('produk_jual_id', $req->produk);
            });
        }
        if ($req->sales) {
            $querySewa->where('sales', $req->input('sales'));
            $queryPenjualan->where('employee_id', $req->input('sales'));
        }
        if ($req->status == 'Sudah Dibayar') {
            $querySewa->whereHas('pembayaran');
            $queryPenjualan->whereHas('pembayaran');
        } elseif ($req->status == 'Belum Dibayar') {
            $querySewa->whereDoesntHave('pembayaran');
            $queryPenjualan->whereDoesntHave('pembayaran');
        }

        // Map data and format fields
        $dataSewa = $querySewa->get()->map(function($item) {
            $item->nama_sales = $item->data_sales->nama;
            $item->nama_customer = $item->kontrak->customer->nama;
            $item->jumlah = formatRupiah(($item->subtotal + $item->total_promo));
            if ($item->pembayaran->isEmpty()) {
                $metode = 'Belum ada pembayaran';
            } else {
                $metode = $item->pembayaran->pluck('cara_bayar')
                    ->map(function($caraBayar) {
                        return ucfirst($caraBayar);
                    })
                    ->unique()
                    ->implode(', ');
            }
            $item->metode = $metode;
            $item->ppn_nominal = formatRupiah($item->ppn_nominal);
            $item->pph_nominal = formatRupiah($item->pph_nominal);
            $item->ongkir_nominal = formatRupiah($item->ongkir_nominal);
            $item->total_promo = formatRupiah($item->total_promo);
            $item->total_tagihan = formatRupiah($item->total_tagihan);
            return $item;
        });
        $dataPenjualan = $queryPenjualan->get()->map(function($item) {
            $item->nama_sales = $item->karyawan->nama;
            $item->nama_customer = $item->customer->nama;
            $item->jumlah = formatRupiah($item->sub_total);
            if ($item->pembayaran->isEmpty()) {
                $metode = 'Belum ada pembayaran';
            } else {
                $metode = $item->pembayaran->pluck('cara_bayar')
                    ->map(function($caraBayar) {
                        return ucfirst($caraBayar);
                    })
                    ->unique()
                    ->implode(', ');
            }
            $item->metode = $metode;
            $item->ppn_nominal = formatRupiah($item->jumlah_ppn);
            $item->pph_nominal = formatRupiah(0);
            $item->ongkir_nominal = formatRupiah($item->biaya_ongkir);
            $item->total_promo = formatRupiah($item->total_promo);
            $item->total_tagihan = formatRupiah($item->total_tagihan);
            return $item;
        });
        $data = collect();

        // Add items from $dataSewa
        $dataSewa->each(function ($item) use ($data) {
            $this->mergeItems($item, $data);
        });

        // Add items from $dataPenjualan
        $dataPenjualan->each(function ($item) use ($data) {
            $this->mergeItems($item, $data);
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.omset_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('omset.pdf');
    }

    public function omset_excel(Request $req)
    {
        // Eager load relationships and filter by status
        $querySewa = InvoiceSewa::with('kontrak.customer', 'kontrak.lokasi', 'data_sales', 'pembayaran', 'produk.produk')
            ->where('status', 'DIKONFIRMASI');
        $queryPenjualan = Penjualan::with('customer', 'lokasi', 'karyawan', 'pembayaran', 'produk.produk', 'ongkir')
            ->where('status', 'DIKONFIRMASI');


        if ($req->tipe_penjualan === 'Tradisional') {
            $querySewa->whereRaw('1 = 0');
        } elseif ($req->tipe_penjualan === 'Sewa') {
            $queryPenjualan->whereRaw('1 = 0');
        }

        // Retrieve data
        $allDataSewa = $querySewa->get();
        $allDataPenjualan = $queryPenjualan->get();

        // Extract unique product names
        $produkSewa = collect();
        $produkPenjualan = collect();

        foreach ($allDataSewa as $item) {
            if (isset($item->produk) && $item->produk->isNotEmpty()) {
                foreach ($item->produk as $produkItem) {
                    if (isset($produkItem->produk->id) && isset($produkItem->produk->nama)) {
                        $produkSewa->put($produkItem->produk->id, $produkItem->produk->nama);
                    }
                }
            }
        }
        
        foreach ($allDataPenjualan as $item) {
            if (isset($item->produk) && $item->produk->isNotEmpty()) {
                foreach ($item->produk as $produkItem) {
                    if (isset($produkItem->produk->id) && isset($produkItem->produk->nama)) {
                        $produkPenjualan->put($produkItem->produk->id, $produkItem->produk->nama);
                    }
                }
            }
        }

        $produkSewa = $produkSewa->unique();
        $produkPenjualan = $produkPenjualan->unique();

        $combinedProduk = $produkSewa->concat($produkPenjualan);

        $produk = $combinedProduk->unique(function ($item, $key) {
            return $item;
        });

        $produk = $produk->mapWithKeys(function ($item, $key) {
            return [$key => $item];
        });

        // Extract unique sales names
        $salesSewa = collect();
        $salesPenjualan = collect();

        foreach ($allDataSewa as $item) {
            if (isset($item->data_sales) && isset($item->data_sales->id) && isset($item->data_sales->nama)) {
                $salesSewa->put($item->data_sales->id, $item->data_sales->nama);
            }
        }
        foreach ($allDataPenjualan as $item) {
            if (isset($item->karyawan) && isset($item->karyawan->id) && isset($item->karyawan->nama)) {
                $salesPenjualan->put($item->karyawan->id, $item->karyawan->nama);
            }
        }

        $salesSewa = $salesSewa->unique();
        $salesPenjualan = $salesPenjualan->unique();
        
        $combinedSales = $salesSewa->concat($salesPenjualan);

        $sales = $combinedSales->unique(function ($item, $key) {
            return $item;
        });

        $sales = $sales->mapWithKeys(function ($item, $key) {
            return [$key => $item];
        });

        // Apply filters based on request
        if ($req->tanggal_invoice) {
            $querySewa->whereDate('tanggal_invoice', $req->tanggal_invoice);
            $queryPenjualan->whereDate('tanggal_invoice', $req->tanggal_invoice);
        }
        if ($req->tanggal_pembayaran) {
            $querySewa->whereHas('pembayaran', function($q) use($req) {
                $q->where('tanggal_bayar', $req->tanggal_pembayaran);
            });
            $queryPenjualan->whereHas('pembayaran', function($q) use($req) {
                $q->where('tanggal_bayar', $req->tanggal_pembayaran);
            });
        }
        if ($req->produk) {
            $querySewa->whereHas('produk', function($q) use($req) {
                $q->where('produk_jual_id', $req->produk);
            });
            $queryPenjualan->whereHas('produk', function($q) use($req) {
                $q->where('produk_jual_id', $req->produk);
            });
        }
        if ($req->sales) {
            $querySewa->where('sales', $req->input('sales'));
            $queryPenjualan->where('employee_id', $req->input('sales'));
        }
        if ($req->status == 'Sudah Dibayar') {
            $querySewa->whereHas('pembayaran');
            $queryPenjualan->whereHas('pembayaran');
        } elseif ($req->status == 'Belum Dibayar') {
            $querySewa->whereDoesntHave('pembayaran');
            $queryPenjualan->whereDoesntHave('pembayaran');
        }

        // Map data and format fields
        $dataSewa = $querySewa->get()->map(function($item) {
            $item->nama_sales = $item->data_sales->nama;
            $item->nama_customer = $item->kontrak->customer->nama;
            $item->jumlah = formatRupiah(($item->subtotal + $item->total_promo));
            if ($item->pembayaran->isEmpty()) {
                $metode = 'Belum ada pembayaran';
            } else {
                $metode = $item->pembayaran->pluck('cara_bayar')
                    ->map(function($caraBayar) {
                        return ucfirst($caraBayar);
                    })
                    ->unique()
                    ->implode(', ');
            }
            $item->metode = $metode;
            $item->ppn_nominal = formatRupiah($item->ppn_nominal);
            $item->pph_nominal = formatRupiah($item->pph_nominal);
            $item->ongkir_nominal = formatRupiah($item->ongkir_nominal);
            $item->total_promo = formatRupiah($item->total_promo);
            $item->total_tagihan = formatRupiah($item->total_tagihan);
            return $item;
        });
        $dataPenjualan = $queryPenjualan->get()->map(function($item) {
            $item->nama_sales = $item->karyawan->nama;
            $item->nama_customer = $item->customer->nama;
            $item->jumlah = formatRupiah($item->sub_total);
            if ($item->pembayaran->isEmpty()) {
                $metode = 'Belum ada pembayaran';
            } else {
                $metode = $item->pembayaran->pluck('cara_bayar')
                    ->map(function($caraBayar) {
                        return ucfirst($caraBayar);
                    })
                    ->unique()
                    ->implode(', ');
            }
            $item->metode = $metode;
            $item->ppn_nominal = formatRupiah($item->jumlah_ppn);
            $item->pph_nominal = formatRupiah(0);
            $item->ongkir_nominal = formatRupiah($item->biaya_ongkir);
            $item->total_promo = formatRupiah($item->total_promo);
            $item->total_tagihan = formatRupiah($item->total_tagihan);
            return $item;
        });
        $data = collect();

        // Add items from $dataSewa
        $dataSewa->each(function ($item) use ($data) {
            $this->mergeItems($item, $data);
        });

        // Add items from $dataPenjualan
        $dataPenjualan->each(function ($item) use ($data) {
            $this->mergeItems($item, $data);
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new OmsetExport($data), 'omset.xlsx');
    }

    public function promo_index(Request $req)
    {
        $query = Penjualan::with('customer', 'karyawan', 'promo')
        ->whereHas('promo')->where('status', 'DIKONFIRMASI');

        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item){
            $item->total_with_diskon = $item->sub_total - $item->total_promo;
            return $item;
        });
        return view('laporan.promo', compact('data'));
    }

    public function promo_pdf(Request $req)
    {
        $query = Penjualan::with('customer', 'karyawan', 'promo')
        ->whereHas('promo')->where('status', 'DIKONFIRMASI');

        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item){
            $item->total_with_diskon = $item->sub_total - $item->total_promo;
            return $item;
        });
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.promo_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('promo.pdf');
    }

    public function promo_excel(Request $req)
    {
        $query = Penjualan::with('customer', 'karyawan', 'promo')
        ->whereHas('promo')->where('status', 'DIKONFIRMASI');

        if ($req->dateStart) {
            $query->where('tanggal_invoice', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tanggal_invoice', '<=', $req->input('dateEnd'));
        }

        $data = $query->get()->map(function($item){
            $item->total_with_diskon = $item->sub_total - $item->total_promo;
            return $item;
        });
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new PromoExport($data), 'promo.xlsx');
    }

    public function stok_gallery_index(Request $req)
    {
        $bulan = [
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ];
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
        
        $thisLokasi = $req->gallery ?? $galleries->first()->id;
        $thisMonth = $req->bulan ?? sprintf('%02d', now()->month);
        $thisYear = $req->tahun ?? now()->year;

        $lokasi = $req->gallery ? $galleries->where('id', $req->gallery)->first() : $galleries->first();
        $listDate = $this->listDatePerMonth($thisMonth, $thisYear);

        // data do sewa
        $DOSewa = DeliveryOrder::where('jenis_do', 'SEWA')->where('status', 'DIKONFIRMASI')->with('produk.komponen.produk')->whereYear('tanggal_kirim', $thisYear)
            ->whereMonth('tanggal_kirim', $thisMonth)
            ->where(function($q) use($thisLokasi){
                $q->whereHas('kontrak', function($r) use($thisLokasi){
                    $r->where('lokasi_id', $thisLokasi);
            });
        })->get();

        // data kembali sewa
        $KembaliSewa = KembaliSewa::where('status', 'DIKONFIRMASI')->with('produk.komponen.produk')->whereYear('tanggal_kembali', $thisYear)
            ->whereMonth('tanggal_kembali', $thisMonth)
            ->where(function($q) use($thisLokasi){
                $q->whereHas('sewa', function($r) use($thisLokasi){
                    $r->where('lokasi_id', $thisLokasi);
            });
        })->get();

        // data do penjualan
        $DOPenjualan = DeliveryOrder::where('status', 'DIKONFIRMASI')->where('jenis_do', 'PENJUALAN')->with('produk.komponen.produk')->whereYear('tanggal_kirim', $thisYear)
            ->whereMonth('tanggal_kirim', $thisMonth)
            ->where(function($q) use($thisLokasi){
                $q->whereHas('penjualan', function($r) use($thisLokasi){
                    $r->where('lokasi_id', $thisLokasi);
            });
        })->get();

        // data ambil langsung penjualan
        $ambilLangsungPenjualan = Penjualan::where('status', 'DIKONFIRMASI')
            ->with('produk.komponen.produk')
            ->where('distribusi', 'Diambil')
            ->where('lokasi_id', $thisLokasi)
            ->whereYear('tanggal_invoice', $thisYear)
            ->whereMonth('tanggal_invoice', $thisMonth)
        ->get();

        // data mutasi
        $mutasi = Mutasi::where('status', 'DIKONFIRMASI')
            ->where(function($q) use($thisLokasi) {
                $q->where('penerima', $thisLokasi)
                ->orWhere('pengirim', $thisLokasi);
            })
            ->whereYear('tanggal_kirim', $thisYear)
            ->whereMonth('tanggal_kirim', $thisMonth)
            ->with([
                'produkMutasi.komponen',
                'produkMutasiOutlet.komponen',
                'produkMutasiGG.komponen',
                'produkMutasiGAG.komponen'
            ])
        ->get();

        // data pembelian
        $pembelian = Pembelian::where('status_dibuat', 'DIKONFIRMASI')
            ->where('lokasi_id', $thisLokasi)
            ->whereYear('tgl_diterima', $thisYear)
            ->whereMonth('tgl_diterima', $thisMonth)
            ->whereNotNull('penerima')
            ->with([
                'produkbeli.produk'
            ])
        ->get();

        // data retur pembelian
        $returPembelian = Returpembelian::where('status_dibuat', 'DIKONFIRMASI')
            ->whereHas('invoice', function($q) use($thisLokasi){
                $q->whereHas('pembelian', function($r) use($thisLokasi){
                    $r->where('lokasi_id', $thisLokasi);
                });
            })
            ->whereYear('tgl_retur', $thisYear)
            ->whereMonth('tgl_retur', $thisMonth)
            ->with([
                'produkretur.produkbeli.produk',
                'invoice.pembelian'
            ])
        ->get();

        // data mutasi inden
        $mutasiInden = Mutasiindens::where('status_diterima', 'DIKONFIRMASI')
            ->whereYear('tgl_diterima', $thisYear)
            ->whereMonth('tgl_diterima', $thisMonth)
            ->where('lokasi_id', $thisLokasi)
            ->with([
                'produkmutasi.produk.produk'
            ])
        ->get();

        // data return mutasi inden
        $returMutasiInden = Returinden::where('status_dibukukan', 'DIKONFIRMASI')
            ->whereYear('tgl_dibukukan', $thisYear)
            ->whereMonth('tgl_dibukukan', $thisMonth)
            ->whereHas('mutasiinden', function($q) use($thisLokasi){
                $q->where('lokasi_id', $thisLokasi);
            })
            ->with([
                'produkreturinden.produk.produk.produk'
            ])
        ->get();

        // tahun dari data DO Sewa
        $years = $DOSewa->pluck('tanggal_kirim')->map(function($date) {
            return Carbon::parse($date)->year;
        });

        // tahun dari data Kembali Sewa
        $years = $years->merge($KembaliSewa->pluck('tanggal_kembali')->map(function($date) {
            return Carbon::parse($date)->year;
        }));

        // tahun dari data DO Penjualan
        $years = $years->merge($DOPenjualan->pluck('tanggal_kirim')->map(function($date) {
            return Carbon::parse($date)->year;
        }));

        // tahun dari data Ambil Langsung Penjualan
        $years = $years->merge($ambilLangsungPenjualan->pluck('tanggal_invoice')->map(function($date) {
            return Carbon::parse($date)->year;
        }));

        // tahun dari data mutasi
        $years = $years->merge($mutasi->pluck('tanggal_kirim')->map(function($date) {
            return Carbon::parse($date)->year;
        }));

        // tahun dari data pembelian
        $years = $years->merge($pembelian->pluck('tgl_diterima')->map(function($date) {
            return Carbon::parse($date)->year;
        }));

        // tahun dari data retur pembelian
        $years = $years->merge($returPembelian->pluck('tgl_retur')->map(function($date) {
            return Carbon::parse($date)->year;
        }));

        // tahun dari data mutasi inden
        $years = $years->merge($returPembelian->pluck('tgl_diterima')->map(function($date) {
            return Carbon::parse($date)->year;
        }));

        // tahun dari data retur mutasi inden
        $years = $years->merge($returMutasiInden->pluck('tgl_dibukukan')->map(function($date) {
            return Carbon::parse($date)->year;
        }));

        // Ambil tahun yang unik dan urutkan
        $tahun = $years->unique()->sort()->values();

        // integrate data
        $data = Produk::all()->map(function($item) use($listDate, $thisLokasi, $DOSewa, $KembaliSewa, $DOPenjualan, $ambilLangsungPenjualan, $mutasi, $pembelian, $returPembelian, $mutasiInden, $returMutasiInden){
            // Inisialisasi list data dengan saldo awal 0
            $item->dates = collect($listDate)->mapWithKeys(function($date) {
                return [
                    $date => [
                        'stok_masuk' => 0,
                        'stok_keluar' => 0,
                        'stok_retur' => 0,
                        'saldo' => 0
                    ]
                ];
            });
        
            // Fungsi untuk memperbarui stok dan saldo
            $updateSaldo = function($date, $stokKeluar, $stokMasuk, $stokRetur) use(&$item) {
                $current = $item->dates[$date];
                $previousDate = Carbon::parse($date)->subDay()->format('Y-m-d');
        
                // Jika ada tanggal sebelumnya, ambil saldo sebelumnya
                $previousSaldo = $item->dates->has($previousDate) ? $item->dates[$previousDate]['saldo'] : 0;
        
                $current['stok_keluar'] += $stokKeluar;
                $current['stok_masuk'] += $stokMasuk;
                $current['stok_retur'] += $stokRetur;
                $current['saldo'] = $previousSaldo + $current['stok_masuk'] - $current['stok_keluar'] + $current['stok_retur'];
        
                $item->dates = $item->dates->put($date, $current);
            };
        
            // Proses DO Sewa
            $DOSewa->each(function($order) use($item, $updateSaldo) {
                $order->produk->each(function($product) use($item, $order, $updateSaldo) {
                    if($product->no_kembali_sewa == null){
                        $product->komponen->each(function($komponen) use($item, $order, $product, $updateSaldo) {
                            if ($komponen->kode_produk == $item->kode) {
                                $date = Carbon::parse($order->tanggal_kirim)->format('Y-m-d');
                                $updateSaldo($date, ($komponen->jumlah * $product->jumlah), 0, 0);
                            }
                        });
                    }
                });
            });
        
            // Proses Kembali Sewa
            $KembaliSewa->each(function($order) use($item, $updateSaldo) {
                $order->produk->each(function($product) use($item, $order, $updateSaldo) {
                    $product->komponen->each(function($komponen) use($item, $order, $product, $updateSaldo) {
                        if ($komponen->kode_produk == $item->kode) {
                            $date = Carbon::parse($order->tanggal_kembali)->format('Y-m-d');
                            $updateSaldo($date, 0, 0, ($komponen->jumlah * $product->jumlah));
                        }
                    });
                });
            });
        
            // Proses DO Penjualan
            $DOPenjualan->each(function($order) use($item, $updateSaldo) {
                $order->produk->each(function($product) use($item, $order, $updateSaldo) {
                    $product->komponen->each(function($komponen) use($item, $order, $product, $updateSaldo) {
                        if ($komponen->kode_produk == $item->kode) {
                            $date = Carbon::parse($order->tanggal_kirim)->format('Y-m-d');
                            $updateSaldo($date, ($komponen->jumlah * $product->jumlah), 0, 0);
                        }
                    });
                });
            });
        
            // Proses Ambil Langsung Penjualan
            $ambilLangsungPenjualan->each(function($order) use($item, $updateSaldo) {
                $order->produk->each(function($product) use($item, $order, $updateSaldo) {
                    $product->komponen->each(function($komponen) use($item, $order, $product, $updateSaldo) {
                        if ($komponen->kode_produk == $item->kode) {
                            $date = Carbon::parse($order->tanggal_invoice)->format('Y-m-d');
                            $updateSaldo($date, ($komponen->jumlah * $product->jumlah), 0, 0);
                        }
                    });
                });
            });

            // Proses Mutasi
            $mutasi->each(function($order) use($item, $thisLokasi, $updateSaldo) {
                if($item->penerima == $thisLokasi){
                    $order->produkMutasiOutlet->each(function($product) use($item, $order, $updateSaldo) {
                        $product->komponen->each(function($komponen) use($item, $order, $product, $updateSaldo) {
                            if ($komponen->kode_produk == $item->kode) {
                                $date = Carbon::parse($order->tanggal_invoice)->format('Y-m-d');
                                $updateSaldo($date, 0, ($komponen->jumlah * $product->jumlah), 0);
                            }
                        });
                    });
                    $order->produkMutasiGG->each(function($product) use($item, $order, $updateSaldo) {
                        $product->komponen->each(function($komponen) use($item, $order, $product, $updateSaldo) {
                            if ($komponen->kode_produk == $item->kode) {
                                $date = Carbon::parse($order->tanggal_invoice)->format('Y-m-d');
                                $updateSaldo($date, 0, ($komponen->jumlah * $product->jumlah), 0);
                            }
                        });
                    });
                    $order->produkMutasiGAG->each(function($product) use($item, $order, $updateSaldo) {
                        $product->komponen->each(function($komponen) use($item, $order, $product, $updateSaldo) {
                            if ($komponen->kode_produk == $item->kode) {
                                $date = Carbon::parse($order->tanggal_invoice)->format('Y-m-d');
                                $updateSaldo($date, 0, ($komponen->jumlah * $product->jumlah), 0);
                            }
                        });
                    });
                }
                if($item->pengirim == $thisLokasi){
                    $order->produkMutasi->each(function($product) use($item, $order, $updateSaldo) {
                        $product->komponen->each(function($komponen) use($item, $order, $product, $updateSaldo) {
                            if ($komponen->kode_produk == $item->kode) {
                                $date = Carbon::parse($order->tanggal_invoice)->format('Y-m-d');
                                $updateSaldo($date, ($komponen->jumlah * $product->jumlah), 0, 0);
                            }
                        });
                    });
                    $order->produkMutasiGAG->each(function($product) use($item, $order, $updateSaldo) {
                        $product->komponen->each(function($komponen) use($item, $order, $product, $updateSaldo) {
                            if ($komponen->kode_produk == $item->kode) {
                                $date = Carbon::parse($order->tanggal_invoice)->format('Y-m-d');
                                $updateSaldo($date, ($komponen->jumlah * $product->jumlah), 0, 0);
                            }
                        });
                    });
                }
            });

            // Proses Pembelian
            $pembelian->each(function($order) use($item, $updateSaldo) {
                $order->produkbeli->each(function($product) use($item, $order, $updateSaldo) {
                    if ($product->id == $item->id) {
                        $date = Carbon::parse($order->tgl_diterima)->format('Y-m-d');
                        $updateSaldo($date, 0, $product->jml_diterima, 0);
                    }
                });
            });

            // Proses Retur Pembelian
            $returPembelian->each(function($order) use($item, $updateSaldo) {
                $order->produkretur->each(function($product) use($item, $order, $updateSaldo) {
                    if ($product->produkbeli->produk_id == $item->id) {
                        $date = Carbon::parse($order->tgl_retur)->format('Y-m-d');
                        $updateSaldo($date, $product->jumlah, 0, 0);
                    }
                });
            });
            
            // Proses Mutasi Inden
            $mutasiInden->each(function($order) use($item, $updateSaldo) {
                $order->produkmutasi->each(function($product) use($item, $order, $updateSaldo) {
                    if ($product->produk->produk->id == $item->id) {
                        $date = Carbon::parse($order->tgl_diterima)->format('Y-m-d');
                        $updateSaldo($date, 0, $product->jml_diterima, 0);
                    }
                });
            });
            
            // Proses Retur Mutasi Inden
            $returMutasiInden->each(function($order) use($item, $updateSaldo) {
                $order->produkreturinden->each(function($product) use($item, $order, $updateSaldo) {
                    if ($product->produk->produk->produk->id == $item->id) {
                        $date = Carbon::parse($order->tgl_dibukukan)->format('Y-m-d');
                        $updateSaldo($date, 0, $product->jml_diterima, 0);
                    }
                });
            });

            // Update saldo untuk hari-hari yang tidak ada transaksi
            foreach ($listDate as $index => $date) {
                if ($index > 0) {
                    $previousDate = $listDate[$index - 1];
                    if ($item->dates[$date]['saldo'] == 0) {

                        $previousSaldo = $item->dates[$previousDate]['saldo'];
                        $current = $item->dates[$date];
                        $current['saldo'] = $previousSaldo;
        
                        $item->dates = $item->dates->put($date, $current);
                    }
                }
            }
        
            $item->totalMasuk = $item->dates->sum('stok_masuk');
            $item->totalKeluar = $item->dates->sum('stok_keluar');
            $item->totalRetur = $item->dates->sum('stok_retur');
            return $item;
        });
        
        return view('laporan.stok_gallery', compact('data', 'listDate', 'bulan', 'galleries', 'lokasi', 'tahun'));
    }

    public function stok_gallery_pdf(Request $req)
    {
        $query = Invoicepo::with(['pembelian.produkbeli'])->whereHas('pembelian')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->supplier) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('supplier_id', $req->supplier);
            });
        }
        if ($req->gallery) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('laporan.pembelian_pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('pembelian.pdf');
    }

    public function stok_gallery_excel(Request $req)
    {
        $query = Invoicepo::with(['pembelian.produkbeli'])->whereHas('pembelian')->where('status_dibuat', 'DIKONFIRMASI');

        if ($req->supplier) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('supplier_id', $req->supplier);
            });
        }
        if ($req->gallery) {
            $query->whereHas('pembelian', function($q) use($req){
                $q->where('lokasi_id', $req->gallery);
            });
        }
        if ($req->dateStart) {
            $query->where('tgl_inv', '>=', $req->input('dateStart'));
        }
        if ($req->dateEnd) {
            $query->where('tgl_inv', '<=', $req->input('dateEnd'));
        }

        $data = $query->get();
        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new PembelianExport($data), 'pembelian.xlsx');
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

    public function mutasiinden_index(Request $req) 
    {
        $querymutasi = ProdukMutasiInden::query();

        if ($req->bulan) {
            $querymutasi->where('inventoryinden_id', $req->bulan);
        }
        
        $produkmutasiinden = $querymutasi->get();
        
        if ($produkmutasiinden->isEmpty()) {
            return view('laporan.mutasiinden', [
                'produkterjual' => collect(),
                'mutasiinden' => collect(),
                'suppliers' => Supplier::all(),
                'galleries' => Lokasi::where('tipe_lokasi', 1)->get(),
                'inventorys' => InventoryInden::all()
            ]);
        }

        $mutasiindenQuery = Mutasiindens::whereNotNull('pembuat_id')
            ->whereNotNull('pembuku_id')
            ->whereNotNull('penerima_id')
            ->whereNotNull('pemeriksa_id')
            ->whereNotNull('tgl_dibuat')
            ->whereNotNull('tgl_dibukukan')
            ->whereNotNull('tgl_diterima_ttd')
            ->whereNotNull('tgl_diperiksa')
            ->where('status_dibuat', 'DIKONFIRMASI')
            ->where('status_diterima', 'DIKONFIRMASI')
            ->where('status_dibukukan', 'DIKONFIRMASI')
            ->where('status_diperiksa', 'DIKONFIRMASI');

        if ($req->pengirim) {
            $mutasiindenQuery->where('supplier_id', $req->pengirim);
        }
        if ($req->penerima) {
            $mutasiindenQuery->where('lokasi_id', $req->penerima);
        }
        if ($req->tanggaldikirim) {
            $mutasiindenQuery->where('tgl_dikirim', $req->tanggaldikirim);
        }
        if ($req->tanggalditerima) {
            $mutasiindenQuery->where('tgl_diterima', $req->tanggalditerima);
        }

        $mutasiinden = $mutasiindenQuery->get();

        $produkterjual = collect();
        foreach ($mutasiinden as $mutasi) {
            $querymutasi = ProdukMutasiInden::where('mutasiinden_id', $mutasi->id);
            if ($req->bulan) {
                $querymutasi->where('inventoryinden_id', $req->bulan);
            }
            $produkmutasiinden = $querymutasi->get();
            $produkterjual = $produkterjual->merge($produkmutasiinden);
        }
        
        $suppliers = Supplier::all();
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
        $inventorys = InventoryInden::all();

        return view('laporan.mutasiinden', compact('produkterjual', 'mutasiinden', 'suppliers', 'galleries', 'inventorys'));
    }


    public function mutasiinden_pdf(Request $req) 
    {
        $querymutasi = ProdukMutasiInden::query();

        if ($req->bulan) {
            $querymutasi->where('inventoryinden_id', $req->bulan);
        }

        $produkmutasiinden = $querymutasi->get();

        if ($produkmutasiinden->isEmpty()) {
            return view('laporan.mutasiinden', [
                'produkterjual' => collect(),
                'mutasiinden' => collect(),
                'suppliers' => Supplier::all(),
                'galleries' => Lokasi::where('tipe_lokasi', 1)->get(),
                'inventorys' => InventoryInden::all()
            ]);
        }

        $mutasiinden = Mutasiindens::whereNotNull('pembuat_id')
            ->whereNotNull('pembuku_id')
            ->whereNotNull('penerima_id')
            ->whereNotNull('pemeriksa_id')
            ->whereNotNull('tgl_dibuat')
            ->whereNotNull('tgl_dibukukan')
            ->whereNotNull('tgl_diterima_ttd')
            ->whereNotNull('tgl_diperiksa')
            ->where('status_dibuat', 'DIKONFIRMASI')
            ->where('status_diterima', 'DIKONFIRMASI')
            ->where('status_dibukukan', 'DIKONFIRMASI')
            ->where('status_diperiksa', 'DIKONFIRMASI');

        if ($req->pengirim) {
            $mutasiinden->where('supplier_id', $req->pengirim);
        }
        if ($req->penerima) {
            $mutasiinden->where('lokasi_id', $req->penerima);
        }
        if ($req->tanggaldikirim) {
            $mutasiinden->where('tgl_dikirim', $req->tanggaldikirim);
        }
        if ($req->tanggalditerima) {
            $mutasiinden->where('tgl_diterima', $req->tanggalditerima);
        }

        $mutasiindenRecords = $mutasiinden->get();

        $produkterjual = $mutasiindenRecords->map(function ($mutasi) use ($req) {
            return ProdukMutasiInden::where('mutasiinden_id', $mutasi->id)
                ->when($req->bulan, function ($query) use ($req) {
                    return $query->where('inventoryinden_id', $req->bulan);
                })
                ->get();
        })->flatten();
        // dd($produkterjual);
        
        $pdf = Pdf::loadView('laporan.mutasiinden_pdf', compact('produkterjual', 'mutasiindenRecords'))->setPaper('a4', 'portrait');
        return $pdf->stream('mutasiinden.pdf');

    }

    public function mutasiinden_excel(Request $req) 
    {
        $querymutasi = ProdukMutasiInden::query();

        if ($req->bulan) {
            $querymutasi->where('inventoryinden_id', $req->bulan);
        }

        $produkmutasiinden = $querymutasi->get();

        if ($produkmutasiinden->isEmpty()) {
            return view('laporan.mutasiinden', [
                'produkterjual' => collect(),
                'mutasiinden' => collect(),
                'suppliers' => Supplier::all(),
                'galleries' => Lokasi::where('tipe_lokasi', 1)->get(),
                'inventorys' => InventoryInden::all()
            ]);
        }

        $mutasiinden = Mutasiindens::whereNotNull('pembuat_id')
            ->whereNotNull('pembuku_id')
            ->whereNotNull('penerima_id')
            ->whereNotNull('pemeriksa_id')
            ->whereNotNull('tgl_dibuat')
            ->whereNotNull('tgl_dibukukan')
            ->whereNotNull('tgl_diterima_ttd')
            ->whereNotNull('tgl_diperiksa')
            ->where('status_dibuat', 'DIKONFIRMASI')
            ->where('status_diterima', 'DIKONFIRMASI')
            ->where('status_dibukukan', 'DIKONFIRMASI')
            ->where('status_diperiksa', 'DIKONFIRMASI');

        if ($req->pengirim) {
            $mutasiinden->where('supplier_id', $req->pengirim);
        }
        if ($req->penerima) {
            $mutasiinden->where('lokasi_id', $req->penerima);
        }
        if ($req->tanggaldikirim) {
            $mutasiinden->where('tgl_dikirim', $req->tanggaldikirim);
        }
        if ($req->tanggalditerima) {
            $mutasiinden->where('tgl_diterima', $req->tanggalditerima);
        }

        $mutasiindenRecords = $mutasiinden->get();

        $produkterjual = $mutasiindenRecords->map(function ($mutasi) use ($req) {
            return ProdukMutasiInden::where('mutasiinden_id', $mutasi->id)
                ->when($req->bulan, function ($query) use ($req) {
                    return $query->where('inventoryinden_id', $req->bulan);
                })
                ->get();
        })->flatten();

        if($produkterjual->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new MutasiindenExport($produkterjual, $mutasiindenRecords), 'mutasi.xlsx');
    }
    
    public function listDatePerMonth($month = null, $year = null)
    {
        $month = $month ?? date('m');
        $year = $year ?? date('Y');

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $dates = [];

        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }
}
