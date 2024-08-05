<?php

namespace App\Http\Controllers;

use App\Exports\DOSewaExport;
use App\Exports\KontrakExport;
use App\Exports\RekapPergantianExport;
use App\Exports\TagihanSewaExport;
use App\Models\Customer;
use App\Models\DeliveryOrder;
use App\Models\InvoiceSewa;
use App\Models\KembaliSewa;
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
            '05' => 'Mai',
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
            '05' => 'Mai',
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
            '05' => 'Mai',
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
}
