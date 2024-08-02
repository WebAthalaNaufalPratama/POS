<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Karyawan;
use App\Models\Penjualan;
use App\Models\Produk_Terjual;
use App\Models\Produk_Jual;
use App\Models\Tipe_Produk;
use App\Models\DeliveryOrder;
use App\Models\Komponen_Produk_Terjual;
use App\Models\Pembayaran;
use App\Models\Mutasi;
use App\Models\ReturPenjualan;

class LaporanController extends Controller
{
    public function kontrak_index()
    {
        return view('laporan.kontrak');
    }

    public function kontrak_pdf()
    {
        dd('pdf');
    }
    
    public function kontrak_excel()
    {
        dd('excel');
    }

    public function penjualanproduk_index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $penjualan = Penjualan::where('lokasi_pengirim', $karyawan->lokasi_id)
                    ->where('status', 'DIKONFIRMASI')
                    ->wherenotNull('dibuat_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('auditor_id')
                    ->wherenotNull('tanggal_dibuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_audit')
                    ->get();
        $arrinvoice = $penjualan->pluck('no_invoice')->toArray();
        $produkterjual = Produk_Terjual::where('no_invoice', $arrinvoice)->get();
        foreach($produkterjual as $pj) {
            $poju = Produk_Jual::where('id', $pj->produk_jual_id)->first();
            $tipe = Tipe_Produk::where('id', $poju->tipe_produk)->first();
        }
        return view('laporan.penjualanproduk', compact('poju', 'produkterjual', 'tipe'));        
    }
    public function penjualanproduk_pdf()
    {
        dd('pdf');
    }
    public function penjualanproduk_excel()
    {
        dd('excel');
    }
    public function pelanggan_index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $penjualan = Penjualan::where('lokasi_pengirim', $karyawan->lokasi_id)
                    ->where('status', 'DIKONFIRMASI')
                    ->wherenotNull('dibuat_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('auditor_id')
                    ->wherenotNull('tanggal_dibuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_audit')
                    ->get();
        
        return view('laporan.pelanggan', compact('penjualan'));
    }
    public function pelanggan_pdf()
    {
        dd('pdf');
    }
    public function pelanggan_excel()
    {
        dd('excel');
    }

    public function pembayaran_index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $penjualan = Penjualan::where('lokasi_pengirim', $karyawan->lokasi_id)
                    ->where('status', 'DIKONFIRMASI')
                    ->wherenotNull('dibuat_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('auditor_id')
                    ->wherenotNull('tanggal_dibuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_audit')
                    ->get();

        $duit = [];

        foreach($penjualan as $pj) {
            $pembayaran = Pembayaran::where('invoice_penjualan_id', $pj->id)->get();
            foreach($pembayaran as $bayar) {
                $carabayar = $bayar->rekening_id;
                if (isset($duit[$carabayar])) {
                    $duit[$carabayar] += $bayar->nominal;
                } else {
                    $duit[$carabayar] = $bayar->nominal;
                }
            }
        }

        return view('laporan.pembayaran', compact('penjualan', 'pembayaran', 'duit'));
    }

    public function pembayaran_pdf()
    {
        dd('pdf');
    }
    public function pembayaran_excel()
    {
        dd('excel');
    }

    public function dopenjualan_index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $dopenjualan = DeliveryOrder::where('lokasi_pengirim', $karyawan->lokasi_id)
                    ->where('status', 'DIKONFIRMASI')
                    ->wherenotNull('pembuat')
                    ->wherenotNull('penyetuju')
                    ->wherenotNull('pemeriksa')
                    ->wherenotNull('tanggal_pembuat')
                    ->wherenotNull('tanggal_penyetuju')
                    ->wherenotNull('tanggal_pemeriksa')
                    ->get();

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

        return view('laporan.dopenjualan', compact('combinedData'));
    }




    public function dopenjualan_pdf()
    {
        dd('pdf');
    }
    public function dopenjualan_excel()
    {
        dd('excel');
    }

    public function returpenjualan_index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $returpenjualan = ReturPenjualan::where('lokasi_id', $karyawan->lokasi_id)
                    ->where('status', 'DIKONFIRMASI')
                    ->wherenotNull('pembuat')
                    ->wherenotNull('pembuku')
                    ->wherenotNull('pemeriksa')
                    ->wherenotNull('tanggal_pembuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_diperiksa')
                    ->get();

        $produkterjual = collect(); 
        $penjualan = collect(); 

        foreach($returpenjualan as $retur) {
            $produkterjualItem = Produk_Terjual::where('no_retur', $retur->no_retur)->get();
            $penjualanItem = Penjualan::where('no_invoice', $retur->no_invoice)->get();
            
            $produkterjual = $produkterjual->merge($produkterjualItem);
            $penjualan = $penjualan->merge($penjualanItem);
        }

        return view('laporan.returpenjualan', compact('returpenjualan', 'produkterjual', 'penjualan'));
    }



    public function returpenjualan_pdf()
    {
        dd('pdf');
    }
    public function returpenjualan_excel()
    {
        dd('excel');
    }

    public function penjualan_index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();
        $penjualan = Penjualan::where('lokasi_id', $karyawan->lokasi_id)
                    ->where('status', 'DIKONFIRMASI')
                    ->wherenotNull('dibuat_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('auditor_id')
                    ->wherenotNull('tanggal_dibuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_audit')
                    ->get();

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

        return view('laporan.penjualan', compact('combinedData'));
    }



    public function penjualan_pdf()
    {
        dd('pdf');
    }
    public function penjualan_excel()
    {
        dd('excel');
    }
    public function mutasi_index()
    {
        $user = Auth::user();
        $karyawan = Karyawan::where('user_id', $user->id)->first();

        $penjualan = Mutasi::where('status', 'DIKONFIRMASI')
                    ->wherenotNull('pembuat_id')
                    ->wherenotNull('penerima_id')
                    ->wherenotNull('diperiksa_id')
                    ->wherenotNull('dibukukan_id')
                    ->wherenotNull('tanggal_pembuat')
                    ->wherenotNull('tanggal_dibukukan')
                    ->wherenotNull('tanggal_diperiksa')
                    ->wherenotNull('tanggal_penerima')
                    ->get();


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
                            'kondisi_diterima' => $komponen->kondisi_dit->nama ?? null,
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
        
        // dd($produkterjual);
        // dd($combinedData);


        return view('laporan.mutasi', compact('combinedData'));
    }

    public function mutasi_pdf()
    {
        dd('pdf');
    }
    public function mutasi_excel()
    {
        dd('excel');
    }

    
}
