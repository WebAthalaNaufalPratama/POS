<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Komponen_Produk_Jual;
use App\Models\Kondisi;
use App\Models\Produk;
use App\Models\Produk_Jual;
use App\Models\Tipe_Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Spatie\Activitylog\Models\Activity;
use App\Models\Customer;
use App\Models\Lokasi;
use App\Models\Karyawan;
use App\Models\Rekening;
use App\Models\Promo;
use App\Models\Ongkir;
use App\Models\Penjualan;
use App\Models\Produk_Terjual;
use App\Models\DeliveryOrder;
use App\Models\ProdukReturJual;
use App\Models\ReturPenjualan;
use App\Models\Supplier;
use App\Models\Komponen_Produk_Terjual;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ReturpenjualanController extends Controller
{
    public function index()
    {
        $returs = ReturPenjualan::orderBy('created_at', 'desc')->get();
        // dd($returs);
        return view('returpenjualan.index', compact('returs'));
    }

    public function create($penjualan)
    {
        $penjualans = Penjualan::with('produk', 'deliveryorder')->find($penjualan);
        // dd($penjualans);
        $user = Auth::user();
        $lokasis = Lokasi::find($user);
        $karyawans = Karyawan::all();
        $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_referensi)->get();
        // $customers = Customer::where('id', $penjualans->id_customer)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $produkjuals = Produk_Jual::all();
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        $returInvoice = ReturPenjualan::latest()->first();
        if ($returInvoice != null) {
            $substring = substr($returInvoice->no_retur, 11);
            $cekretur = substr($substring, 0, 3);
        } else {
            $cekretur = 0;
        }
        $suppliers = Supplier::all();
        $dopenjualans = DeliveryOrder::where('no_do', $penjualans->no_invoice)->get();
        // $produkjuals = Produk_Jual::all();
        $customers = Customer::all();
        $karyawans = Karyawan::all();
        $kondisis = Kondisi::all();
        $drivers = Karyawan::where('jabatan', 'driver')->get();

        return view('returpenjualan.create', compact('penjualans','suppliers','cekretur','drivers','dopenjualans', 'kondisis', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals', 'cekInvoice'));
    }

    public function store(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'no_retur' => 'required',
            'no_invoice' => 'required',
            'lokasi_id' => 'required',
            'bukti' => 'required',
            'tanggal_invoice' => 'required',
            'tanggal_retur' => 'required',
            'customer_id' => 'required',
            'supplier_id' => 'required',
            'no_do' => 'required',
            'komplain' => 'required',
            'catatan_komplain' => 'required',
            'sub_total' => 'required',
            'total' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        $data = $req->except(['_token', '_method']);
        // dd($data);

        if ($req->hasFile('bukti')) {
            $filePath = $this->uploadFile($req->file('bukti'));
            $data['bukti'] = $filePath;
        }
        if ($req->hasFile('file')) {
            $filePath = $this->uploadFileDO($req->file('file'));
            $data['file'] = $filePath;
        }

        $data['jenis_do'] = 'RETUR';
        $data['status'] = 'DIKIRIM';
        $data['pembuat'] = Auth::user()->id;
        $data['no_referensi'] = $req->no_retur;
        $data['tanggal_pembuat'] = now();
        $data['handphone'] = Customer::where('id', $req->customer_id)->value('handphone');
        // dd($data);
        if($req->komplain == 'retur'){
            $deliveryOrder = DeliveryOrder::create($data);
        } 
        
        // dd($deliveryOrder);
        $returPenjualan = ReturPenjualan::create($data);

        if (!$returPenjualan) {
            return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        }
        if($req->komplain == 'retur'){
            for ($i = 0; $i < count($data['nama_produk2']); $i++) {
                $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk2'][$i])->first();
                $produk_terjual = ProdukReturJual::create([
                    'produk_jual_id' => $getProdukJual->id,
                    'no_retur' => $returPenjualan->no_retur,
                    'jumlah' => $data['jumlah2'][$i],
                    'satuan' => $data['satuan2'][$i],
                    'jenis' => 'RETUR',
                    'keterangan' => $data['keterangan2'][$i]
                ]);
    
                if (!$produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                foreach ($getProdukJual->komponen as $komponen) {
                    $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                        'produk_terjual_id' => $produk_terjual->id,
                        'kode_produk' => $komponen->kode_produk,
                        'nama_produk' => $komponen->nama_produk,
                        'tipe_produk' => $komponen->tipe_produk,
                        'kondisi' => $komponen->kondisi,
                        'deskripsi' => $komponen->deskripsi,
                        'jumlah' => $komponen->jumlah,
                        'harga_satuan' => $komponen->harga_satuan,
                        'harga_total' => $komponen->harga_total
                    ]);
                    if (!$komponen_produk_terjual)  return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }
            }
        }

        for ($i = 0; $i < count($data['nama_produk']); $i++) {
            $getProdukJual = Produk_Jual::with('komponen')->where('kode', $data['nama_produk'][$i])->first();
            $produk_terjual = ProdukReturJual::create([
                'produk_jual_id' => $getProdukJual->id,
                'no_retur' => $returPenjualan->no_retur,
                'alasan' => $data['alasan'][$i],
                'jumlah' => $data['jumlah'][$i],
                'jenis_diskon' => $data['jenis_diskon'][$i],
                'diskon' => $data['diskon'][$i],
                'harga' => $data['harga'][$i],
                'total_harga' => $data['totalharga'][$i]
            ]);
    
            if (!$produk_terjual) {
                return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
            }
    
            foreach ($getProdukJual->komponen as $komponen) {
                $komponen_produk_terjual = Komponen_Produk_Terjual::create([
                    'produk_terjual_id' => $produk_terjual->id,
                    'kode_produk' => $komponen->kode_produk,
                    'nama_produk' => $komponen->nama_produk,
                    'tipe_produk' => $komponen->tipe_produk,
                    'kondisi' => $komponen->kondisi,
                    'deskripsi' => $komponen->deskripsi,
                    'jumlah' => $komponen->jumlah,
                    'harga_satuan' => $komponen->harga_satuan,
                    'harga_total' => $komponen->harga_total
                ]);
    
                if (!$komponen_produk_terjual) {
                    return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
                }
            }
        }
        return redirect(route('penjualan.index'))->with('success', 'Data tersimpan');
    }

private function uploadFile($file)
{
    $fileName = time() . '_' . $file->getClientOriginalName();
    return $file->storeAs('bukti_retur_penjualan', $fileName, 'public');
}
private function uploadFileDO($file)
{
    $fileName = time() . '_' . $file->getClientOriginalName();
    return $file->storeAs('bukti_DO_Retur', $fileName, 'public');
}

public function show($returpenjualan)
{
        $penjualans = ReturPenjualan::with('produk_retur', 'deliveryorder')->find($returpenjualan);
        $returpenjualans = ReturPenjualan::with('deliveryorder')->find($returpenjualan);
        // foreach($returpenjualans->deliveryorder as $delivery){
        //     dd($delivery->penerima);
        // }
        // dd($returpenjualans->deliveryorder);
        $user = Auth::user();
        $lokasis = Lokasi::find($user);
        $karyawans = Karyawan::all();
        $produks = ProdukReturjual::with('komponen', 'produk')->where('no_retur', $returpenjualans->no_retur)->get();
        // $customers = Customer::where('id', $penjualans->id_customer)->get();
        // $produks = Produk_Terjual::with('komponen', 'produk')->where('no_invoice', $penjualans->no_invoice)->get();
        $produkjuals = Produk_Jual::all();
        $produkreturjuals = Produk_Jual::all();
        // dd($produkjuals);
        $Invoice = DeliveryOrder::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekInvoice = substr($substring, 0, 3);
        } else {
            $cekInvoice = 0;
        }
        $Invoice = ReturPenjualan::latest()->first();
        if ($Invoice != null) {
            $substring = substr($Invoice->no_do, 11);
            $cekretur = substr($substring, 0, 3);
        } else {
            $cekretur = 0;
        }
        $suppliers = Supplier::all();
        $dopenjualans = DeliveryOrder::where('no_do', $returpenjualans->no_invoice)->get();
        // $produkjuals = Produk_Jual::all();
        $customers = Customer::all();
        $karyawans = Karyawan::all();
        $kondisis = Kondisi::all();
        $drivers = Karyawan::where('jabatan', 'driver')->get();

        return view('returpenjualan.show', compact('produkreturjuals','penjualans','returpenjualans','suppliers','cekretur','drivers','dopenjualans', 'kondisis', 'karyawans', 'lokasis', 'produks', 'customers', 'produks', 'produkjuals', 'cekInvoice'));
}

public function update(Request $req, $returpenjualan)
{
    if ($req->hasFile('bukti')) {
        $file = $req->file('bukti');
        $fileName = $req->no_retur . date('YmdHis') . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs('bukti_retur_penjualan', $fileName, 'public');
        $data['bukti'] = $filePath;

        $retur = ReturPenjualan::find($returpenjualan);
        $retur->bukti = $data['bukti'];
        $retur->update();
        return redirect()->back()->with('success', 'File tersimpan');
    } else {
        return redirect()->back()->with('fail', 'Gagal menyimpan file');
    }
}

}
