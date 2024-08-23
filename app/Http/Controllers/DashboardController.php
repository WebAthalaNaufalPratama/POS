<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lokasi;
use App\Models\Produk_Terjual;
use App\Models\Produk;
use App\Models\Penjualan;
use App\Models\Pembayaran;
use App\Models\Customer;
use App\Models\Produk_Jual;
use App\Models\ReturPenjualan;
use App\Models\Promo;
use Carbon\Carbon;
use App\Models\Pembelian;
use App\Models\Produkbeli;
use App\Models\Returpembelian;
use App\Models\ProdukMutasiInden;
use App\Models\InventoryOutlet;
use App\Models\Produkreturinden;
use App\Models\Returinden;
use App\Models\InventoryInden;
use App\Models\InventoryGudang;
use App\Models\InventoryGreenHouse;
use App\Models\InventoryGallery;
use App\Models\Invoicepo;
use App\Models\Kondisi;
use App\Models\Rekening;
use App\Models\TransaksiKas;
use DateTime;
use IntlDateFormatter;

class DashboardController extends Controller
{
    public function index(Request $req)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();

        if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery', 'Purchasing'])) {
            $lokasiId = $karyawan->lokasi->id;
            $lokasinama = Lokasi::where('id', $lokasiId)->value('tipe_lokasi');
        } else {
            $lokasiId = $req->query('lokasi_id');
            $lokasinama = Lokasi::where('id', $lokasiId)->value('tipe_lokasi');
        }

        if($lokasinama != 5) {
            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
    
            $jumlahpenjualan = Penjualan::where('lokasi_id', $lokasiId)->where('status', 'DIKONFIRMASI')
                                ->whereNotNull('tanggal_dibuat')
                                ->whereNotNull('tanggal_dibukukan')
                                ->whereNotNull('tanggal_audit')
                                ->whereNotNull('dibuat_id')
                                ->whereNotNull('dibukukan_id')
                                ->whereNotNull('auditor_id')
                                ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                    $query->where('created_at', '>=', $startOfMonth)
                                            ->orWhere('created_at', '<=', $endOfMonth);
                                })
                                ->count();
            $batalpenjualan = Penjualan::where('lokasi_id', $lokasiId)->where('status', 'DIBATALKAN')
                                ->whereNotNull('tanggal_dibuat')
                                ->whereNotNull('dibuat_id')
                                ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                    $query->where('created_at', '>=', $startOfMonth)
                                            ->orWhere('created_at', '<=', $endOfMonth);
                                })
                                ->count();
            $returpenjualan = ReturPenjualan::where('lokasi_id', $lokasiId)->where('status', 'DIKONFIRMASI')
                                ->whereNotNull('tanggal_pembuat')
                                ->whereNotNull('pembuat')
                                ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                    $query->where('created_at', '>=', $startOfMonth)
                                            ->orWhere('created_at', '<=', $endOfMonth);
                                })
                                ->count();
        
            $customerslama = Customer::where('lokasi_id', $karyawan->lokasi_id)->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->where('tanggal_bergabung', '<', $startOfMonth)
                        ->orWhere('tanggal_bergabung', '>', $endOfMonth);
            })->get();
            $customersbaru = Customer::where('lokasi_id', $karyawan->lokasi_id)->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->where('tanggal_bergabung', '>=', $startOfMonth)
                        ->orWhere('tanggal_bergabung', '<=', $endOfMonth);
            })->get();
            $arrcustomerbaru = $customersbaru->pluck('id')->toArray();
            $arrcustomerlama = $customerslama->pluck('id')->toArray();
            $penjualanlama = Penjualan::whereIn('id_customer',$arrcustomerlama)->where('lokasi_id', $lokasiId)
                            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('created_at', '>=', $startOfMonth)
                                        ->orWhere('created_at', '<=', $endOfMonth);
                            })->count();
            $penjualanbaru = Penjualan::whereIn('id_customer',$arrcustomerbaru)->where('lokasi_id', $lokasiId)
                            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('created_at', '>=', $startOfMonth)
                                        ->orWhere('created_at', '<=', $endOfMonth);
                            })->count();
            $penjualanList = Penjualan::where('lokasi_id', $lokasiId)->get();
            $penjualanIds = $penjualanList->pluck('id');
            $prefixes = ['BYR', 'BOT'];
            $pembayaranList = Pembayaran::where(function ($query) use ($prefixes) {
                foreach ($prefixes as $prefix) {
                    $query->orWhere('no_invoice_bayar', 'LIKE', $prefix . '%');
                }
            })->get();

    
            $pemasukan = 0;
            foreach ($pembayaranList as $pembayaran) {
                $nominal = $pembayaran->nominal;
                $pemasukan += $nominal;
            }
    
            $lokasis = Lokasi::all();
            return view('dashboard.index', compact('lokasis', 'jumlahpenjualan', 'pemasukan', 'batalpenjualan', 'returpenjualan', 'penjualanbaru', 'penjualanlama'));
        }else{
            if ($user->hasRole(['Purchasing'])) {
                $lokasiId = $karyawan->lokasi->id;
            } else {
                $lokasiId = $req->query('lokasi_id');
            }

            $startOfMonth = Carbon::now()->startOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
    
            $jumlahpenjualan = Pembelian::where('status_dibuat', 'DIKONFIRMASI')
                                ->where('status_diperiksa', 'DIKONFIRMASI')
                                ->whereNotNull('tgl_dibuat')
                                ->whereNotNull('tgl_diperiksa')
                                ->whereNotNull('pembuat')
                                ->whereNotNull('pemeriksa')
                                ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                    $query->where('created_at', '>=', $startOfMonth)
                                            ->orWhere('created_at', '<=', $endOfMonth);
                                })
                                ->count();
            $batalpenjualan = Pembelian::where('status_dibuat', 'BATAL')
                                ->whereNotNull('tgl_dibuat')
                                ->whereNotNull('pembuat')
                                ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                    $query->where('created_at', '>=', $startOfMonth)
                                            ->orWhere('created_at', '<=', $endOfMonth);
                                })
                                ->count();
            $pembelian = Pembelian::where('status_dibuat', 'DIKONFIRMASI')
                    ->where('status_diperiksa', 'DIKONFIRMASI')
                    ->where(function($query) use ($startOfMonth, $endOfMonth) {
                        $query->where('created_at', '>=', $startOfMonth)
                                ->orWhere('created_at', '<=', $endOfMonth);
                    })->get();
            $arrpembelian = $pembelian->pluck('id')->toArray();
            $invoicepo = Invoicepo::whereIn('pembelian_id', $arrpembelian)->get();
            $arrinvoicepo = $invoicepo->pluck('id')->toArray();
            $returpenjualan = Returpembelian::where('invoicepo_id', $arrinvoicepo)
                                ->where('status_dibuat', 'DIKONFIRMASI')
                                ->where('status_dibuku', 'DIKONFIRMASI')                                ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                    $query->where('created_at', '>=', $startOfMonth)
                                            ->orWhere('created_at', '<=', $endOfMonth);
                                })
                                ->count();
        
            $barangmasuk = Produkbeli::whereIn('pembelian_id', $arrpembelian)->get();
            $hitungmasuk = 0;
            foreach($barangmasuk as $masuk) {
                $brgmasuk = $masuk->jml_diterima;
                $hitungmasuk += $brgmasuk;
            }

            $penjualanlama = $hitungmasuk;
                                                            
            $returpem = Returinden::where('status_dibuat', 'DIKONFIRMASI')
                    ->where('status_dibukukan', 'DIKONFIRMASI')
                    ->whereNotNull('tgl_dibuat')
                    ->whereNotNull('tgl_dibukukan')
                    ->whereNotNull('pembuat_id')
                    ->whereNotNull('pembuku_id')
                    ->where(function($query) use ($startOfMonth, $endOfMonth) {
                        $query->where('created_at', '>=', $startOfMonth)
                                ->orWhere('created_at', '<=', $endOfMonth);
                    })->get();
            
            $arrreturpem = $returpem->pluck('id')->toArray();
            $produkretur = ProdukReturInden::where('returinden_id', $arrreturpem)->get();
            $arrprodukmutasi = $produkretur->pluck('produk_mutasi_inden_id')->toArray();
            $produkmutasi = ProdukMutasiInden::whereIn('id',$arrprodukmutasi)->get();
            $hitungkeluar = 0;
            foreach($produkmutasi as $keluar) {
                $brgkeluar = $keluar->jml_diterima;
                $hitungkeluar += $brgkeluar;
            }

            $penjualanbaru = $hitungmasuk;


            $pembayaranList = Pembayaran::where('no_invoice_bayar', 'LIKE', 'BYMI%')
                            ->orwhere('no_invoice_bayar', 'LIKE', 'BYPO%')                
                            ->get();
            $pengeluaranList = Pembayaran::where('no_invoice_bayar', 'LIKE', 'RefundInden%')
                            ->orwhere('no_invoice_bayar', 'LIKE', 'Refundpo%')                
                            ->get();
    
            $pemasukan = 0;
            foreach ($pembayaranList as $pembayaran) {
                $nominal = $pembayaran->nominal;
                $pemasukan += $nominal;
            }

            $pembayaranList = Returpembelian::whereIn('invoicepo_id', $arrpembelian)
                        ->where('komplain', 'LIKE', 'Refund')
                        ->get();
    
            $pengeluaran = 0;
            foreach ($pengeluaranList as $pembayaran) {
                $nominal = $pembayaran->nominal;
                $pengeluaran += $nominal;
            }
    
            $lokasis = Lokasi::all();

            if($user->hasRole('Finance')){
                // saldo rekening
                $rekenings = Rekening::when($req->lokasi_id, function($q) use($req){
                    $q->where('lokasi_id', $req->lokasi_id);
                })->get();
                
                $balance = 0;
                if($req->rekening_id){
                    $balance = TransaksiKas::getSaldo($req->rekening_id);
                }
                return view('dashboard.index_purchase', compact('lokasis', 'jumlahpenjualan', 'pemasukan', 'batalpenjualan', 'returpenjualan', 'penjualanbaru', 'penjualanlama', 'pengeluaran', 'rekenings', 'balance'));
            }

            return view('dashboard.index_purchase', compact('lokasis', 'jumlahpenjualan', 'pemasukan', 'batalpenjualan', 'returpenjualan', 'penjualanbaru', 'penjualanlama', 'pengeluaran'));
        }
        
    }

    public function update_auditor(Request $req){

        $user = Auth::user();
        if ($user) {
            $karyawan = Karyawan::where('user_id', $user->id)->first();

            if ($karyawan) {
                $update = $karyawan->update([
                    'lokasi_id' => $req->lokasi_id,
                ]);
                if ($update) {
                    echo 'berhasil';
                } else {
                    echo 'gagal';
                }
            } else {
                $save = Karyawan::create([
                    'user_id' => $user->id,
                    'nama' => $user->name,
                    'jabatan' => 'auditor',
                    'lokasi_id' => $req->lokasi_id,
                    'handphone' => 0,
                    'alamat' => '-',
                ]);
                if ($save) {
                    echo 'berhasil';
                } else {
                    echo 'gagal';
                }
            }
        } else {
            echo 'User not authenticated';
        }
    }

    public function bukakunci(Request $req)
    {
        $buka = 'BUKA';
        $tutup = 'TUTUP';
        $check = Customer::where('id', $req->custome)->first();
        if($check->status_buka == 'TUTUP'){
            $cust = Customer::where('id', $req->custome)->update([
                'status_buka' => $buka
            ]);  
            if($cust){
                return redirect()->back()->with('success', 'Berhasil Membuka Transaksi');
            }else{
                return redirect()->back()->with('fail', 'Gagal Menyimpan Data');
            }  
        }else{
            $cust = Customer::where('id', $req->custome)->update([
                'status_buka' => $tutup
            ]);
            if($cust){
                return redirect()->back()->with('success', 'Berhasil Menutup Transaksi');
            }else{
                return redirect()->back()->with('fail', 'Gagal Menyimpan Data');
            }
        }
    }

    public function getTopProducts(Request $req)
    {
        $pembayaran = Pembayaran::where('status_bayar', 'LUNAS')->get();
        $componentTotals = [];

        $invoiceIds = $pembayaran->pluck('invoice_penjualan_id')->toArray();
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
            $lokasi = $karyawan->lokasi->id;
            $lokasinama = Lokasi::where('id', $lokasi)->value('tipe_lokasi');
        } else {
            if($user->hasRole(['Purchasing'])) {
                $lokasinama = Lokasi::where('id', $karyawan->lokasi_id)->value('tipe_lokasi');
            }else{
                $lokasi = $req->query('lokasi_id');
                $lokasinama = Lokasi::where('id', $lokasi)->value('tipe_lokasi');
            }
        }
        if($lokasinama != 5) {
            if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
                $lokasi = $karyawan->lokasi->id;
                $penjualanList = Penjualan::where('lokasi_id', $lokasi)->whereIn('id', $invoiceIds)
                            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('created_at', '>=', $startOfMonth)
                                        ->orWhere('created_at', '<=', $endOfMonth);
                            })->get();
            } else {
                $lokasi = $req->query('lokasi_id');
                $penjualanList = Penjualan::where('lokasi_id', $lokasi)->whereIn('id', $invoiceIds)
                            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('created_at', '>=', $startOfMonth)
                                        ->orWhere('created_at', '<=', $endOfMonth);
                            })->get();
            }
            
            $invoiceNumbers = $penjualanList->pluck('no_invoice')->toArray();
    
            $produkterjual = Produk_Terjual::with('komponen')
                ->whereIn('no_invoice', $invoiceNumbers)
                ->get();
    
            foreach ($produkterjual as $pt) {
                foreach ($pt->komponen as $komponen) {
                    $namaKomponen = $komponen->nama_produk;
                    $jumlah = $komponen->jumlah ?? 1; 
                    $totaljumlah = $jumlah * $pt->jumlah;
                    
                    if (!isset($componentTotals[$namaKomponen])) {
                        $componentTotals[$namaKomponen] = 0;
                    }
                    $componentTotals[$namaKomponen] += $totaljumlah;
                }
            }
            
            arsort($componentTotals);
            
            $topProducts = array_slice($componentTotals, 0, 5, true);
    
            $labels = array_keys($topProducts);
            $data = array_values($topProducts);
    
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }else {
            if ($user->hasRole(['Purchasing'])) {
                $lokasi = $karyawan->lokasi->id;
                $pembelianList = Pembelian::where('status_dibuat', 'DIKONFIRMASI')
                            ->where('status_diperiksa', 'DIKONFIRMASI')
                            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('created_at', '>=', $startOfMonth)
                                        ->orWhere('created_at', '<=', $endOfMonth);
                            })->get();
            } else {
                $lokasi = $req->query('lokasi_id');
                $pembelianList = Pembelian::where('status_dibuat', 'DIKONFIRMASI')
                            ->where('status_diperiksa', 'DIKONFIRMASI')
                            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                                $query->where('created_at', '>=', $startOfMonth)
                                        ->orWhere('created_at', '<=', $endOfMonth);
                            })->get();
            }
            
            $invoiceNumbers = $pembelianList->pluck('id')->toArray();
    
            $produkbeli = Produkbeli::whereIn('pembelian_id', $invoiceNumbers)
                ->get();
    
            foreach ($produkbeli as $pt) {
                $idkomponen = $pt->produk_id;
                $jumlah = 1;
                $totaljumlah = $jumlah * $pt->jml_diterima;
                $namaKomponen = Produk::where('id', $idkomponen)->value('nama');
                if(!isset($componentTotals[$namaKomponen])) {
                    $componentTotals[$namaKomponen] = 0;
                }
                $componentTotals[$namaKomponen] += $totaljumlah;
            }
            
            arsort($componentTotals);
            
            $topProducts = array_slice($componentTotals, 0, 5, true);
    
            $labels = array_keys($topProducts);
            $data = array_values($topProducts);
    
            return response()->json([
                'labels' => $labels,
                'data' => $data
            ]);
        }
        
    }

    public function getTopMinusProduk(Request $req) 
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
            $lokasi = $karyawan;
            $lokasinama = Lokasi::where('id', $lokasi->lokasi_id)->value('tipe_lokasi');
        } else {
            if($user->hasRole(['Purchasing'])){
                $lokasinama = Lokasi::where('id', $karyawan->lokasi_id)->value('tipe_lokasi');
            }else{
                $lokasiId = $req->query('lokasi_id');
                $lokasinama = Lokasi::where('id', $lokasiId)->value('tipe_lokasi');
            }
            
            
        }
        if($lokasinama != 5) {
            if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
                $lokasi = $karyawan;
    
                $query = function ($query) {
                    $query->where('jumlah', '<', 0)
                        ->orWhereRaw('jumlah < min_stok + 100');
                };
        
                if ($lokasi->lokasi->tipe_lokasi == 1) {
                    $topMinusProducts = InventoryGallery::where('lokasi_id', $lokasi->lokasi_id)
                        ->where($query)
                        ->orderBy('jumlah', 'asc')
                        ->take(5)
                        ->get();
    
                    if ($topMinusProducts->isEmpty()) {
                        return response()->json(['message' => 'No products with low or negative stock found'], 204);
                    }
            
                    $productCodes = $topMinusProducts->pluck('kode_produk')->toArray();
                    $namaProduk = Produk::whereIn('kode', $productCodes)->pluck('nama', 'kode')->toArray();
                    
                } elseif ($lokasi->lokasi->tipe_lokasi == 2) {
                    $topMinusProducts = InventoryOutlet::where('lokasi_id', $lokasi->lokasi_id)
                        ->where($query)
                        ->orderBy('jumlah', 'asc')
                        ->take(5)
                        ->get();
    
                    if ($topMinusProducts->isEmpty()) {
                        return response()->json(['message' => 'No products with low or negative stock found'], 204);
                    }
            
                    $productCodes = $topMinusProducts->pluck('kode_produk')->toArray();
                    $namaProduk = Produk_Jual::whereIn('kode', $productCodes)->pluck('nama', 'kode')->toArray();
                } else {
                    return response()->json(['error' => 'Invalid location type'], 400);
                }
    
            } else {
                $lokasiId = $req->query('lokasi_id');
                $lokasi = Lokasi::find($lokasiId);
                
    
                $query = function ($query) {
                    $query->where('jumlah', '<', 0)
                        ->orWhereRaw('jumlah < min_stok + 100');
                };
        
                if ($lokasi->tipe_lokasi == 1) {
                    $topMinusProducts = InventoryGallery::where('lokasi_id', $lokasi->id)
                        ->where($query)
                        ->orderBy('jumlah', 'asc')
                        ->take(5)
                        ->get();
    
                    if ($topMinusProducts->isEmpty()) {
                        return response()->json(['message' => 'No products with low or negative stock found'], 204);
                    }
            
                    $productCodes = $topMinusProducts->pluck('kode_produk')->toArray();
                    $namaProduk = Produk::whereIn('kode', $productCodes)->pluck('nama', 'kode')->toArray();
                } elseif ($lokasi->tipe_lokasi == 2) {
                    $topMinusProducts = InventoryOutlet::where('lokasi_id', $lokasi->id)
                        ->where($query)
                        ->orderBy('jumlah', 'asc')
                        ->take(5)
                        ->get();
    
                    if ($topMinusProducts->isEmpty()) {
                        return response()->json(['message' => 'No products with low or negative stock found'], 204);
                    }
            
                    $productCodes = $topMinusProducts->pluck('kode_produk')->toArray();
                    $namaProduk = Produk_Jual::whereIn('kode', $productCodes)->pluck('nama', 'kode')->toArray();
                } else {
                    return response()->json(['error' => 'Invalid location type'], 400);
                }
            }
    
            
    
            $labels = [];
            $currentStock = [];
            $minStock = [];
    
            foreach ($topMinusProducts as $product) {
                $labels[] = $namaProduk[$product->kode_produk] ?? 'Unknown Product';
                $currentStock[] = $product->jumlah;
                $minStock[] = $product->min_stok ?? null;
            }
    
            $deficit = array_map(function ($min, $current) {
                return max($min - $current, 0);
            }, $minStock, $currentStock);
    
            $series = [
                [
                    'name' => 'Current Stock',
                    'data' => array_map('abs', $currentStock)
                ],
                [
                    'name' => 'Stock Deficit',
                    'data' => $deficit
                ]
            ];
    
            return response()->json([
                'labels' => $labels,
                'series' => $series
            ]);

        }else {
            if($user->hasRole(['Purchasing'])) {
    
                // $query = function ($query) {
                //     $query->where('jumlah', '>', 0);
                // };
                // dd($req->input('inven'));
                if("Greenhouse" == $req->input('inven')) {
                    $topMinusProducts = InventoryGreenHouse::orderBy('jumlah', 'asc')
                        ->take(5)
                        ->get();
                }else if("Inden" == $req->input('inven')){
                    if($req->input('dateInden')) {
                        $dateInden = $req->input('dateInden');
                        $formattedDate = $this->formatDateInden($dateInden);
                        // dd($formattedDate);
                        $topMinusProducts = InventoryInden::where('bulan_inden', $formattedDate)
                            ->orderBy('jumlah', 'asc')
                            ->take(5)
                            ->get();
                            // dd($topMinusProducts);
                    }else{
                        $topMinusProducts = InventoryInden::orderBy('jumlah', 'asc')
                            ->take(5)
                            ->get();
                    }
                    
                }else if("Gudang" == $req->input('inven')){
                    $topMinusProducts = InventoryGudang::orderBy('jumlah', 'asc')
                        ->take(5)
                        ->get();
                }

                if ($topMinusProducts->isEmpty()) {
                    return response()->json(['message' => 'No products with low or negative stock found'], 204);
                }

                $productCodes = $topMinusProducts->pluck('kode_produk')->toArray();
                $kondisiCodes = $topMinusProducts->pluck('kondisi_id')->toArray();
                $namaProduk = Produk::whereIn('kode', $productCodes)->pluck('nama', 'kode')->toArray();
                $kondisiProduk = Kondisi::whereIn('id', $kondisiCodes)->pluck('nama', 'id')->toArray();
                
            } else {
                $lokasiId = $req->query('lokasi_id');
                $lokasi = Lokasi::find($lokasiId);
    
                $query = function ($query) {
                    $query->where('jumlah', '<', 0);
                };
        
                if ($lokasi->tipe_lokasi == 5) {
                    if("Greenhouse" == $req->input('inven')) {
                        $topMinusProducts = InventoryGreenHouse::orderBy('jumlah', 'asc')
                            ->take(5)
                            ->get();
                    }else if("Inden" == $req->input('inven')){
                        if($req->input('dateInden')) {
                            $dateInden = $req->input('dateInden');
                            $formattedDate = $this->formatDateInden($dateInden);
                            // dd($formattedDate);
                            $topMinusProducts = InventoryInden::where('bulan_inden', $formattedDate)
                                ->orderBy('jumlah', 'asc')
                                ->take(5)
                                ->get();
                                // dd($topMinusProducts);
                        }else{
                            $topMinusProducts = InventoryInden::orderBy('jumlah', 'asc')
                                ->take(5)
                                ->get();
                        }
                        
                    }else if("Gudang" == $req->input('inven')){
                        $topMinusProducts = InventoryGudang::orderBy('jumlah', 'asc')
                            ->take(5)
                            ->get();
                    }
                    
    
                    if ($topMinusProducts->isEmpty()) {
                        return response()->json(['message' => 'No products with low or negative stock found'], 204);
                    }
            
                    $productCodes = $topMinusProducts->pluck('kode_produk')->toArray();
                    $kondisiCodes = $topMinusProducts->pluck('kondisi_id')->toArray();
                    $namaProduk = Produk::whereIn('kode', $productCodes)->pluck('nama', 'kode')->toArray();
                    $kondisiProduk = Kondisi::whereIn('id', $kondisiCodes)->pluck('nama', 'id')->toArray();
                } else {
                    return response()->json(['error' => 'Invalid location type'], 400);
                }
            }
    
            
            $labels = [];
            $currentStock = [];
            $minStock = [];

            if("Inden" == $req->input('inven')){
                foreach ($topMinusProducts as $product) {
                    $labels[] = $namaProduk[$product->kode_produk] ?? 'Unknown Product';
                    $currentStock[] = $product->jumlah;
                     $minStock[] = $product->min_stok ?? 0;
                }
            }else{
                foreach ($topMinusProducts as $product) {
                    $labels[] = $namaProduk[$product->kode_produk] .'-'. $kondisiProduk[$product->kondisi_id] ?? 'Unknown Product';
                    $currentStock[] = $product->jumlah;
                     $minStock[] = $product->min_stok ?? 0;
                }
            }
            
    
            $deficit = array_map(function ($min, $current) {
                return max($min - $current, 0);
            }, $minStock, $currentStock);
    
            $series = [
                [
                    'name' => 'Current Stock',
                    'data' => array_map('abs', $currentStock)
                ],
                [
                    'name' => 'Stock Deficit',
                    'data' => $deficit
                ]
            ];
    
            return response()->json([
                'labels' => $labels,
                'series' => $series
            ]);
        }
        
    }

    function formatDateInden($dateInden) {
        $date = DateTime::createFromFormat('Y-m', $dateInden);
    
        if ($date) {
            $monthNames = [
                'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
                'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
                'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
                'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
            ];
    
            $formattedMonth = $monthNames[$date->format('F')] ?? $date->format('F');
            return $formattedMonth . '-' . $date->format('Y');
        }
    
        return 'Invalid date';
    }
    


    public function getTopSales(Request $req)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
            $lokasi = $karyawan->lokasi_id;
            $penjualanList = Penjualan::where('lokasi_id', $lokasi)
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->where('created_at', '>=', $startOfMonth)
                        ->orWhere('created_at', '<=', $endOfMonth);
            })->get();
        } else {
            $lokasiId = $req->query('lokasi_id');
            $lokasi = Lokasi::find($lokasiId);
            $penjualanList = Penjualan::where('lokasi_id', $lokasiId)
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->where('created_at', '>=', $startOfMonth)
                        ->orWhere('created_at', '<=', $endOfMonth);
            })->get();
        }

        
        $salesCounts = [];
        foreach ($penjualanList as $penjualan) {
            $employeeId = $penjualan->employee_id;
            if (isset($salesCounts[$employeeId])) {
                $salesCounts[$employeeId]++;
            } else {
                $salesCounts[$employeeId] = 1;
            }
        }

        arsort($salesCounts);
        $topSalesCounts = $salesCounts;

        $labels = [];
        $data = [];
        foreach ($topSalesCounts as $employeeId => $count) {
            $sales = Karyawan::where('id', $employeeId)->first();
            $labels[] = $sales->nama;
            $data[] = $count;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }

    public function getLoyalty(Request $req) 
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
            $lokasi = $karyawan->lokasi_id;
            $penjualanList = Penjualan::where('lokasi_id', $lokasi)
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->where('created_at', '>=', $startOfMonth)
                        ->orWhere('created_at', '<=', $endOfMonth);
            })->get();
        } else {
            $lokasiId = $req->query('lokasi_id');
            $lokasi = Lokasi::find($lokasiId);
            $penjualanList = Penjualan::where('lokasi_id', $lokasiId)
            ->where(function($query) use ($startOfMonth, $endOfMonth) {
                $query->where('created_at', '>=', $startOfMonth)
                        ->orWhere('created_at', '<=', $endOfMonth);
            })->get();
        }
        
        $promoCounts = [];
        foreach ($penjualanList as $penjualan) {
            $promoId = $penjualan->promo_id;
            if ($promoId !== null) {
                if (isset($promoCounts[$promoId])) {
                    $promoCounts[$promoId]++;
                } else {
                    $promoCounts[$promoId] = 1;
                }
            }
        }

        arsort($promoCounts);
        $topPromoCounts = $promoCounts;

        $labels = [];
        $data = [];
        foreach ($topPromoCounts as $promoId => $count) {
            $promo = Promo::find($promoId);
            if ($promo) {
                $labels[] = $promo->diskon;
                $data[] = $count;
            }
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data
        ]);
    }


}
