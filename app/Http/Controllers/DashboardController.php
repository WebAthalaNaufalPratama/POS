<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lokasi;
use App\Models\Produk_Terjual;
use App\models\Produk;
use App\Models\Penjualan;
use App\Models\Pembayaran;
use App\Models\Customer;
use App\Models\Produk_Jual;
use App\models\ReturPenjualan;
use App\Models\Promo;
use Carbon\Carbon;
use App\models\InventoryOutlet;
use App\Models\InventoryGallery;

class DashboardController extends Controller
{
    public function index(Request $req)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        
        if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
            $lokasiId = $karyawan->lokasi->id;
        } else {
            $lokasiId = $req->query('lokasi_id');
        }

        $jumlahpenjualan = Penjualan::where('lokasi_id', $lokasiId)->where('status', 'DIKONFIRMASI')
                            ->whereNotNull('tanggal_dibuat')
                            ->whereNotNull('tanggal_dibukukan')
                            ->whereNotNull('tanggal_audit')
                            ->whereNotNull('dibuat_id')
                            ->whereNotNull('dibukukan_id')
                            ->whereNotNull('auditor_id')
                            ->count();
        $batalpenjualan = Penjualan::where('lokasi_id', $lokasiId)->where('status', 'DIBATALKAN')
                            ->whereNotNull('tanggal_dibuat')
                            ->whereNotNull('dibuat_id')
                            ->count();
        $returpenjualan = ReturPenjualan::where('lokasi_id', $lokasiId)->where('status', 'DIKONFIRMASI')
                            ->whereNotNull('tanggal_pembuat')
                            ->whereNotNull('tanggal_diperiksa')
                            ->whereNotNull('tanggal_dibukukan')
                            ->whereNotNull('pembuat')
                            ->whereNotNull('pemeriksa')
                            ->whereNotNull('pembuku')
                            ->count();


        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
    
        $customerslama = Customer::where(function($query) use ($startOfMonth, $endOfMonth) {
            $query->where('tanggal_bergabung', '<', $startOfMonth)
                    ->orWhere('tanggal_bergabung', '>', $endOfMonth);
        })->get();
        $customersbaru = Customer::where(function($query) use ($startOfMonth, $endOfMonth) {
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
        $pembayaranList = Pembayaran::whereIn('invoice_penjualan_id', $penjualanIds)->get();

        $pemasukan = 0;
        foreach ($pembayaranList as $pembayaran) {
            $nominal = $pembayaran->nominal;
            $pemasukan += $nominal;
        }

        $lokasis = Lokasi::all();
        return view('dashboard.index', compact('lokasis', 'jumlahpenjualan', 'pemasukan', 'batalpenjualan', 'returpenjualan', 'penjualanbaru', 'penjualanlama'));
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
        if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
            $lokasi = $karyawan->lokasi->id;
            $penjualanList = Penjualan::where('lokasi_id', $lokasi)->whereIn('id', $invoiceIds)->get();
        } else {
            $lokasi = $req->query('lokasi_id');
            $penjualanList = Penjualan::where('lokasi_id', $lokasi)->whereIn('id', $invoiceIds)->get();
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
    }

    public function getTopMinusProduk(Request $req) 
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();

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
            } elseif ($lokasi->lokasi->tipe_lokasi == 2) {
                $topMinusProducts = InventoryOutlet::where('lokasi_id', $lokasi->lokasi_id)
                    ->where($query)
                    ->orderBy('jumlah', 'asc')
                    ->take(5)
                    ->get();
            } else {
                return response()->json(['error' => 'Invalid location type'], 400);
            }

            if ($topMinusProducts->isEmpty()) {
                return response()->json(['message' => 'No products with low or negative stock found'], 204);
            }
    
            $productCodes = $topMinusProducts->pluck('kode_produk')->toArray();
            $namaProduk = Produk::whereIn('kode', $productCodes)->pluck('nama', 'kode')->toArray();
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
            } elseif ($lokasi->tipe_lokasi == 2) {
                $topMinusProducts = InventoryOutlet::where('lokasi_id', $lokasi->id)
                    ->where($query)
                    ->orderBy('jumlah', 'asc')
                    ->take(5)
                    ->get();
            } else {
                return response()->json(['error' => 'Invalid location type'], 400);
            }

            if ($topMinusProducts->isEmpty()) {
                return response()->json(['message' => 'No products with low or negative stock found'], 204);
            }
    
            $productCodes = $topMinusProducts->pluck('kode_produk')->toArray();
            $namaProduk = Produk_Jual::whereIn('kode', $productCodes)->pluck('nama', 'kode')->toArray();
        }

        

        $labels = [];
        $currentStock = [];
        $minStock = [];

        foreach ($topMinusProducts as $product) {
            $labels[] = $namaProduk[$product->kode_produk] ?? 'Unknown Product';
            $currentStock[] = $product->jumlah;
            $minStock[] = $product->min_stok;
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

    public function getTopSales(Request $req)
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();

        if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
            $lokasi = $karyawan->lokasi_id;
            $penjualanList = Penjualan::where('lokasi_id', $lokasi)->get();
        } else {
            $lokasiId = $req->query('lokasi_id');
            $lokasi = Lokasi::find($lokasiId);
            $penjualanList = Penjualan::where('lokasi_id', $lokasiId)->get();
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

        if ($user->hasRole(['KasirAdmin', 'KasirOutlet', 'AdminGallery'])) {
            $lokasi = $karyawan->lokasi_id;
            $penjualanList = Penjualan::where('lokasi_id', $lokasi)->get();
        } else {
            $lokasiId = $req->query('lokasi_id');
            $lokasi = Lokasi::find($lokasiId);
            $penjualanList = Penjualan::where('lokasi_id', $lokasiId)->get();
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
