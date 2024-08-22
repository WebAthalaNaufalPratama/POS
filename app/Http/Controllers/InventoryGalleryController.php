<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\InventoryGallery;
use App\Models\Karyawan;
use App\Models\KembaliSewa;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\PemakaianSendiri;
use App\Models\Produk;
use App\Models\Produk_Terjual;
use App\Models\Produkbeli;
use App\Models\ProdukMutasiInden;
use App\Models\Produkretur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class InventoryGalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        $produks = InventoryGallery::with('kondisi', 'produk', 'gallery')->when(Auth::user()->karyawans, function ($query) {
            return $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        })->orderBy('kode_produk')->orderBy('kondisi_id')->get();
        $namaproduks = InventoryGallery::with('produk')->get()->unique('kode_produk');
        $kondisis = Kondisi::all();
        $user = Auth::user();
        
        $galleries = Lokasi::where('tipe_lokasi', 1)->get();
        $karyawans = Karyawan::when(Auth::user()->karyawans, function ($query) {
            return $query->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
        })->get();
        $lokasis = Lokasi::where('tipe_lokasi', 1)->when(Auth::user()->karyawans, function ($query) {
            return $query->where('id', Auth::user()->karyawans->lokasi_id);
        })->get();

        // start datatable inventory
            if ($req->ajax() && $req->table == 'inventory') {
                if($user->hasRole(['Purchasing', 'Auditor', 'Finance'])) {
                    $query = InventoryGallery::with('produk', 'gallery', 'kondisi')->orderBy('kode_produk', 'asc')->orderBy('kondisi_id', 'asc');
                }else{
                    $query = InventoryGallery::with('produk', 'gallery', 'kondisi')->orderBy('kode_produk', 'asc')->orderBy('kondisi_id', 'asc')->when(Auth::user()->karyawans, function ($q) {
                        return $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                    });
                }
                
                if ($req->produk) {
                    $query->where('kode_produk', $req->input('produk'));
                }
                if ($req->kondisi) {
                    $query->where('kondisi_id', $req->input('kondisi'));
                }
                if ($req->gallery) {
                    $query->where('lokasi_id', $req->input('gallery'));
                }
            
                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $query->where(function($q) use ($search) {
                        $q->where('jumlah', 'like', "%$search%")
                        ->orWhere('kode_produk', 'like', "%$search%")
                        ->orWhereHas('gallery', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('produk', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('kondisi', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
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
                    $item->min_stok = $item->min_stok ?? 0;
                    return $item;
                });

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => InventoryGallery::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data,
                ]);

            }
        // end datatable inventory

        // start datatable pemakaian sendiri
            if ($req->ajax() && $req->table == 'pemakaian_sendiri') {
                $query2 = PemakaianSendiri::with(['karyawan', 'produk', 'lokasi', 'kondisi'])->orderBy('tanggal', 'desc')->orderByDesc('id');
                if ($req->produk2) {
                    $query2->where('produk_id', $req->input('produk2'));
                }
                if ($req->kondisi2) {
                    $query2->where('kondisi_id', $req->input('kondisi2'));
                }
                if ($req->gallery2) {
                    $query2->where('lokasi_id', $req->input('gallery2'));
                }
                if ($req->dateStart2) {
                    $query2->whereDate('tanggal', '>=', $req->input('dateStart2'));
                }
                if ($req->dateEnd2) {
                    $query2->whereDate('tanggal', '<=', $req->input('dateEnd2'));
                }
            
                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $query2->where(function($q) use ($search) {
                        $q->where('tanggal', 'like', "%$search%")
                        ->orWhere('jumlah', 'like', "%$search%")
                        ->orWhere('alasan', 'like', "%$search%")
                        ->orWhereHas('lokasi', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('karyawan', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('produk', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        })
                        ->orWhereHas('kondisi', function($c) use($search){
                            $c->where('nama', 'like', "%$search%");
                        });
                    });
                }
        
                $query2->orderBy($columnName, $dir);
                $recordsFiltered = $query2->count();
                $tempData2 = $query2->offset($start)->limit($length)->get();
        
                $currentPage = ($start / $length) + 1;
                $perPage = $length;
            
                $data2 = $tempData2->map(function($item, $index) use ($currentPage, $perPage) {
                    $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                    $item->nama_produk = $item->produk->nama;
                    $item->nama_kondisi = $item->kondisi->nama;
                    $item->nama_karyawan = $item->karyawan->nama;
                    $item->nama_gallery = $item->lokasi->nama;
                    $item->tanggal = $item->tanggal == null ? '' : tanggalindo($item->tanggal);
                    return $item;
                });

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => PemakaianSendiri::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data2,
                ]);

            }
        // end datatable pemakaian sendiri

        // start datatable log
            if ($req->ajax() && $req->table == 'log') {
                $isAdminGallery = Auth::user()->hasRole('AdminGallery');
                
                $mergedCollection = collect();

                // sewa start
                    $komponenDoSewa = Komponen_Produk_Terjual::with('data_kondisi', 'produk', 'produk_terjual', 'produk_terjual.do_sewa', 'produk_terjual.do_sewa.data_pembuat', 'produk_terjual.do_sewa.kontrak')->whereHas('produk_terjual', function($q) use($isAdminGallery){
                        return $q->where('jenis', null)->whereHas('do_sewa', function($p) use($isAdminGallery){
                            return $p->where('status', 'DIKONFIRMASI')->whereHas('kontrak', function($z) use($isAdminGallery){
                                if ($isAdminGallery) {
                                    $z->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                                }
                            });
                        });
                    })->get();
                    if($komponenDoSewa->isNotEmpty()){
                        $dataKomponen = $komponenDoSewa->map(function($komponen){
                            return [
                                'Id' => $komponen->produk_terjual->id,
                                'Pengubah' => optional($komponen->produk_terjual->do_sewa->data_pembuat)->name,
                                'No Referensi' => $komponen->produk_terjual->do_sewa->no_do ?? null,
                                'Kode Produk Jual' => $komponen->produk_terjual->produk->kode ?? null,
                                'Nama Produk Jual' => $komponen->produk_terjual->produk->nama ?? null,
                                'Kode Komponen' => $komponen->kode_produk ?? null,
                                'Nama Komponen' => $komponen->nama_produk ?? null,
                                'Kondisi' => $komponen->data_kondisi->nama ?? null,
                                'Masuk' => '-',
                                'Keluar' => $komponen->jumlah * $komponen->produk_terjual->jumlah,
                                'Waktu' => $komponen->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($dataKomponen);
                    }
                    $komponenKblSewa = Komponen_Produk_Terjual::with('data_kondisi', 'produk', 'produk_terjual', 'produk_terjual.kembali_sewa', 'produk_terjual.kembali_sewa.data_pembuat', 'produk_terjual.kembali_sewa.sewa')->whereHas('produk_terjual', function($q) use($isAdminGallery){
                        return $q->whereHas('kembali_sewa', function($p) use($isAdminGallery){
                            return $p->where('status', 'DIKONFIRMASI')->whereHas('sewa', function($z) use($isAdminGallery){
                                if ($isAdminGallery) {
                                    $z->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                                }
                            });
                        });
                    })->get();
                    if($komponenKblSewa->isNotEmpty()){
                        $dataKembaliSewa = $komponenKblSewa->map(function($komponen){
                            return [
                                'Id' => $komponen->produk_terjual->id,
                                'Pengubah' => optional($komponen->produk_terjual->kembali_sewa->data_pembuat)->name,
                                'No Referensi' => $komponen->produk_terjual->kembali_sewa->no_kembali ?? null,
                                'Kode Produk Jual' => $komponen->produk_terjual->produk->kode ?? null,
                                'Nama Produk Jual' => $komponen->produk_terjual->produk->nama ?? null,
                                'Kode Komponen' => $komponen->kode_produk ?? null,
                                'Nama Komponen' => $komponen->nama_produk ?? null,
                                'Kondisi' => $komponen->data_kondisi->nama ?? null,
                                'Masuk' => $komponen->jumlah * $komponen->produk_terjual->jumlah,
                                'Keluar' => '-',
                                'Waktu' => $komponen->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($dataKembaliSewa);
                    }
                // sewa end

                // penjualan start
                    $komponenPenjualanDiambil = Komponen_Produk_Terjual::with('data_kondisi', 'produk_terjual.penjualan.dibuat')->whereHas('produk_terjual', function($q) use($isAdminGallery){
                        return $q->whereHas('penjualan', function($p) use($isAdminGallery){
                            $p->where('distribusi', 'Diambil')->where('status', 'DIKONFIRMASI');
                            if ($isAdminGallery) {
                                $p->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                            }
                        });
                    })->get();
                    if($komponenPenjualanDiambil->isNotEmpty()){
                        $dataPenjualanDiambil = $komponenPenjualanDiambil->map(function($komponen){
                            return [
                                'Id' => $komponen->produk_terjual->id,
                                'Pengubah' => optional($komponen->produk_terjual->penjualan->dibuat[0])->name,
                                'No Referensi' => $komponen->produk_terjual->penjualan->no_invoice ?? null,
                                'Kode Produk Jual' => $komponen->produk_terjual->produk->kode ?? null,
                                'Nama Produk Jual' => $komponen->produk_terjual->produk->nama ?? null,
                                'Kode Komponen' => $komponen->kode_produk ?? null,
                                'Nama Komponen' => $komponen->nama_produk ?? null,
                                'Kondisi' => $komponen->data_kondisi->nama ?? null,
                                'Masuk' => '-',
                                'Keluar' => $komponen->jumlah * $komponen->produk_terjual->jumlah,
                                'Waktu' => $komponen->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($dataPenjualanDiambil);
                    }
                    $komponenPenjualanDikirim = Komponen_Produk_Terjual::with('data_kondisi', 'produk_terjual.do_penjualan.penjualan', 'produk_terjual.do_penjualan.dibuat')->whereHas('produk_terjual', function($q) use($isAdminGallery){
                        return $q->whereHas('do_penjualan', function($p) use($isAdminGallery){
                            $p->where('status', 'DIKONFIRMASI')->whereHas('penjualan', function($z) use($isAdminGallery){
                                $z->where('distribusi', 'Dikirim');
                                if ($isAdminGallery) {
                                    $z->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                                }
                            });
                        });
                    })->get();
                    if($komponenPenjualanDikirim->isNotEmpty()){
                        $dataPenjualanDikirim = $komponenPenjualanDikirim->map(function($komponen){
                            return [
                                'Id' => $komponen->produk_terjual->id,
                                'Pengubah' => optional($komponen->produk_terjual->do_penjualan->dibuat[0])->name,
                                'No Referensi' => $komponen->produk_terjual->do_penjualan->no_do ?? null,
                                'Kode Produk Jual' => $komponen->produk_terjual->produk->kode ?? null,
                                'Nama Produk Jual' => $komponen->produk_terjual->produk->nama ?? null,
                                'Kode Komponen' => $komponen->kode_produk ?? null,
                                'Nama Komponen' => $komponen->nama_produk ?? null,
                                'Kondisi' => $komponen->data_kondisi->nama ?? null,
                                'Masuk' => '-',
                                'Keluar' => $komponen->jumlah * $komponen->produk_terjual->jumlah,
                                'Waktu' => $komponen->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($dataPenjualanDikirim);
                    }

                    $komponenRetur = Komponen_Produk_Terjual::with('data_kondisi', 'produk_terjual.retur_penjualan', 'produk_terjual.retur_penjualan.dibuat')->whereHas('produk_terjual', function($q) use($isAdminGallery){
                        return $q->whereHas('retur_penjualan', function($p) use($isAdminGallery){
                            $p->where('status', 'DIKONFIRMASI');
                        });
                    })->get();
                    if($komponenRetur->isNotEmpty()){
                        $dataPenjualanretur = $komponenRetur->map(function($komponen){
                            return [
                                'Id' => $komponen->produk_terjual->id,
                                'Pengubah' => optional($komponen->produk_terjual->retur_penjualan->dibuat)->name,
                                'No Referensi' => $komponen->produk_terjual->retur_penjualan->no_retur ?? null,
                                'Kode Produk Jual' => $komponen->produk_terjual->produk->kode ?? null,
                                'Nama Produk Jual' => $komponen->produk_terjual->produk->nama ?? null,
                                'Kode Komponen' => $komponen->kode_produk ?? null,
                                'Nama Komponen' => $komponen->nama_produk ?? null,
                                'Kondisi' => $komponen->data_kondisi->nama ?? null,
                                'Masuk' => '-',
                                'Keluar' => $komponen->jumlah * $komponen->produk_terjual->jumlah,
                                'Waktu' => $komponen->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($dataPenjualanretur);
                    }
                // penjualan end

                // pembelian start (kurang retur inden)
                    $komponenPembelian = Produkbeli::whereHas('pembelian', function($q) use($isAdminGallery){
                        if ($isAdminGallery) {
                            $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                        }
                        $q->whereNotNull('jml_diterima')->where('status_diterima', 'DIKONFIRMASI');
                    })->get();
                    if($komponenPembelian->isNotEmpty()){
                        $dataPO = $komponenPembelian->map(function($produk){
                            return [
                                'Id' => $produk->id,
                                'Pengubah' => optional($produk->pembelian->dibuat)->name,
                                'No Referensi' => $produk->pembelian->no_po ?? null,
                                'Kode Produk Jual' => '-',
                                'Nama Produk Jual' => '-',
                                'Kode Komponen' => $produk->produk->kode ?? null,
                                'Nama Komponen' => $produk->produk->nama ?? null,
                                'Kondisi' => $produk->kondisi->nama ?? null,
                                'Masuk' => $produk->jml_diterima ?? '-',
                                'Keluar' => '-',
                                'Waktu' => $produk->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($dataPO);
                    }
                    $komponenReturPembelian = Produkretur::with('produkbeli.produk', 'produkbeli.kondisi')->whereHas('returbeli', function($q) use($isAdminGallery){
                        $q->whereHas('invoice', function($p) use($isAdminGallery){
                            $p->whereHas('pembelian', function($r) use($isAdminGallery){
                                if ($isAdminGallery) {
                                    $r->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                                }
                            });
                        });
                    })->get();
                    if($komponenReturPembelian->isNotEmpty()){
                        $dataReturPO = $komponenReturPembelian->map(function($produk){
                            return [
                                'Id' => $produk->id,
                                'Pengubah' => optional($produk->returbeli->dibuat)->name,
                                'No Referensi' => $produk->returbeli->no_retur ?? null,
                                'Kode Produk Jual' => '-',
                                'Nama Produk Jual' => '-',
                                'Kode Komponen' => $produk->produkbeli->produk->kode ?? null,
                                'Nama Komponen' => $produk->produkbeli->produk->nama ?? null,
                                'Kondisi' => $produk->produkbeli->kondisi->nama ?? null,
                                'Masuk' => '-',
                                'Keluar' => $produk->jumlah ?? '-',
                                'Waktu' => $produk->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($dataReturPO);
                    }
                    $komponenPembelianInden = ProdukMutasiInden::with('mutasiinden', 'produk.produk', 'kondisi')->whereNotNull('jml_diterima')->whereHas('mutasiinden', function($q) use($isAdminGallery){
                        if ($isAdminGallery) {
                            $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                        }
                        $q->where('status_diterima', 'DIKONFIRMASI');
                    })->get();
                    if($komponenPembelianInden->isNotEmpty()){
                        $dataPOInden = $komponenPembelianInden->map(function($produk){
                            return [
                                'Id' => $produk->id,
                                'Pengubah' => optional($produk->mutasiinden->pembuat)->name,
                                'No Referensi' => $produk->mutasiinden->no_mutasi ?? null,
                                'Kode Produk Jual' => '-',
                                'Nama Produk Jual' => '-',
                                'Kode Komponen' => $produk->produk->produk->kode ?? null,
                                'Nama Komponen' => $produk->produk->produk->nama ?? null,
                                'Kondisi' => $produk->kondisi->nama ?? null,
                                'Masuk' => $produk->jml_diterima ?? '-',
                                'Keluar' => '-',
                                'Waktu' => $produk->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($dataPOInden);
                    }
                // pembelian end

                // mutasi start
                    $komponenMutasiKeluar = Komponen_Produk_Terjual::with('data_kondisi', 'produk_terjual.mutasi.dibuat')->whereHas('produk_terjual', function($q) use($isAdminGallery){
                        return $q->whereHas('mutasi', function($p) use($isAdminGallery){
                            $p->where('status', 'DIKONFIRMASI');
                            if ($isAdminGallery) {
                                $p->where('pengirim', Auth::user()->karyawans->lokasi_id);
                            }
                        });
                    })->get();
                    if($komponenMutasiKeluar->isNotEmpty()){
                        $datamutasiKeluar = $komponenMutasiKeluar->map(function($komponen){
                            return [
                                'Id' => $komponen->produk_terjual->id,
                                'Pengubah' => optional($komponen->produk_terjual->mutasi->dibuat)->name,
                                'No Referensi' => $komponen->produk_terjual->mutasi->no_mutasi ?? null,
                                'Kode Produk Jual' => $komponen->produk_terjual->produk->kode ?? null,
                                'Nama Produk Jual' => $komponen->produk_terjual->produk->nama ?? null,
                                'Kode Komponen' => $komponen->kode_produk ?? null,
                                'Nama Komponen' => $komponen->nama_produk ?? null,
                                'Kondisi' => $komponen->data_kondisi->nama ?? null,
                                'Masuk' => '-',
                                'Keluar' => $komponen->jumlah * $komponen->produk_terjual->jumlah,
                                'Waktu' => $komponen->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($datamutasiKeluar);
                    }
                    $komponenMutasiMasuk = Komponen_Produk_Terjual::with('data_kondisi', 'produk_terjual.mutasi.dibuat')->whereHas('produk_terjual', function($q) use($isAdminGallery){
                        return $q->whereHas('mutasi', function($p) use($isAdminGallery){
                            $p->where('status', 'DIKONFIRMASI');
                            if ($isAdminGallery) {
                                $p->where('penerima', Auth::user()->karyawans->lokasi_id);
                            }
                        });
                    })->get();
                    if($komponenMutasiMasuk->isNotEmpty()){
                        $datamutasiMasuk = $komponenMutasiMasuk->map(function($komponen){
                            return [
                                'Id' => $komponen->produk_terjual->id,
                                'Pengubah' => optional($komponen->produk_terjual->mutasi->dibuat)->name,
                                'No Referensi' => $komponen->produk_terjual->mutasi->no_mutasi ?? null,
                                'Kode Produk Jual' => $komponen->produk_terjual->produk->kode ?? null,
                                'Nama Produk Jual' => $komponen->produk_terjual->produk->nama ?? null,
                                'Kode Komponen' => $komponen->kode_produk ?? null,
                                'Nama Komponen' => $komponen->nama_produk ?? null,
                                'Kondisi' => $komponen->data_kondisi->nama ?? null,
                                'Masuk' => $komponen->jumlah * $komponen->produk_terjual->jumlah,
                                'Keluar' => '-',
                                'Waktu' => $komponen->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($datamutasiMasuk);
                    }
                // mutasi end

                // pemakaioan sendiri start
                    $pemakaianSendiri = PemakaianSendiri::with('lokasi', 'produk', 'kondisi', 'karyawan')->where(function($q) use($isAdminGallery){
                        if ($isAdminGallery) {
                            $q->where('lokasi_id', Auth::user()->karyawans->lokasi_id);
                        }
                    })->get();
                    if($pemakaianSendiri->isNotEmpty()){
                        $dataPemakaianSendiri = $pemakaianSendiri->map(function($produk){
                            return [
                                'Id' => $produk->produk->id,
                                'Pengubah' => '-',
                                'No Referensi' => null,
                                'Kode Produk Jual' => null,
                                'Nama Produk Jual' => null,
                                'Kode Komponen' => $produk->produk->kode ?? null,
                                'Nama Komponen' => $produk->produk->nama ?? null,
                                'Kondisi' => $produk->kondisi->nama ?? null,
                                'Masuk' => '-',
                                'Keluar' => $produk->jumlah,
                                'Waktu' => $produk->updated_at
                            ];
                        });
                        $mergedCollection = $mergedCollection->merge($dataPemakaianSendiri);
                    }
                //  pemakaian sendiri end

                $start = $req->input('start');
                $length = $req->input('length');
                $order = $req->input('order')[0]['column'];
                $dir = $req->input('order')[0]['dir'];
                $columnName = $req->input('columns')[$order]['data'];

                // search
                $search = $req->input('search.value');
                if (!empty($search)) {
                    $mergedCollection = $mergedCollection->filter(function ($item) use ($search) {
                        return strpos(strtolower($item['Pengubah']), strtolower($search)) !== false ||
                            strpos(strtolower($item['No Referensi']), strtolower($search)) !== false ||
                            strpos(strtolower($item['Kode Produk Jual']), strtolower($search)) !== false ||
                            strpos(strtolower($item['Nama Produk Jual']), strtolower($search)) !== false ||
                            strpos(strtolower($item['Kode Komponen']), strtolower($search)) !== false ||
                            strpos(strtolower($item['Nama Komponen']), strtolower($search)) !== false ||
                            strpos(strtolower($item['Kondisi']), strtolower($search)) !== false ||
                            strpos(strtolower($item['Masuk']), strtolower($search)) !== false ||
                            strpos(strtolower($item['Keluar']), strtolower($search)) !== false ||
                            strpos(strtolower($item['Waktu']), strtolower($search)) !== false;
                    });
                }

                // Pengurutan
                if ($dir == 'asc') {
                    $mergedCollection = $mergedCollection->sortBy($columnName);
                } else {
                    $mergedCollection = $mergedCollection->sortByDesc($columnName);
                }

                // Pagination
                $recordsFiltered = $mergedCollection->count();
                $tempData3 = $mergedCollection->slice($start, $length)->values();
        
                $currentPage = ($start / $length) + 1;
                $perPage = $length;
            
                $data3 = $tempData3->map(function($item, $index) use ($currentPage, $perPage) {
                    $item['no'] = ($currentPage - 1) * $perPage + ($index + 1);
                    return $item;
                });

                return response()->json([
                    'draw' => $req->input('draw'),
                    'recordsTotal' => PemakaianSendiri::count(),
                    'recordsFiltered' => $recordsFiltered,
                    'data' => $data3,
                ]);

            }
        // end datatble log
        return view('inven_galeri.index', compact('produks', 'karyawans', 'lokasis', 'galleries', 'kondisis', 'namaproduks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 1)->when(Auth::user()->karyawans, function ($query) {
            return $query->where('id', Auth::user()->karyawans->lokasi_id);
        })->get();
        return view('inven_galeri.create', compact('produks', 'kondisi', 'gallery'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kode_produk' => 'required',
            'kondisi_id' => 'required|integer',
            'lokasi_id' => 'required',
            'jumlah' => 'required',
            'min_stok' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // check duplikasi
        $duplicate = InventoryGallery::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryGallery::create($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_galeri.index'))->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function show($inventoryGallery)
    {
        $data = InventoryGallery::find($inventoryGallery);
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 1)->get();
        return view('inven_galeri.show', compact('data', 'produks', 'kondisi', 'gallery'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function edit($inventoryGallery)
    {
        $data = InventoryGallery::find($inventoryGallery);
        $produks = Produk::all();
        $kondisi = Kondisi::all();
        $gallery = Lokasi::where('tipe_lokasi', 1)->when(Auth::user()->karyawans, function ($query) {
            return $query->where('id', Auth::user()->karyawans->lokasi_id);
        })->get();
        return view('inven_galeri.edit', compact('data', 'produks', 'kondisi', 'gallery'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $inventoryGallery)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kode_produk' => 'required',
            'kondisi_id' => 'required|integer',
            'lokasi_id' => 'required',
            'jumlah' => 'required',
            'min_stok' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // check duplikasi
        $duplicate = InventoryGallery::where('kode_produk', $data['kode_produk'])->where('kondisi_id', $data['kondisi_id'])->where('lokasi_id', $data['lokasi_id'])->where('id', '!=', $inventoryGallery)->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryGallery::find($inventoryGallery)->update($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_galeri.index'))->with('success', 'Data tersimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\InventoryGallery  $inventoryGallery
     * @return \Illuminate\Http\Response
     */
    public function destroy($inventoryGallery)
    {
        $data = InventoryGallery::find($inventoryGallery);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
