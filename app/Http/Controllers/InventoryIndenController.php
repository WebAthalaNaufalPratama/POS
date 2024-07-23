<?php

namespace App\Http\Controllers;

use App\Models\InventoryInden;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;

class InventoryIndenController extends Controller
{
    public function index()
    {
    $caseStatement = "CASE 
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Januari' THEN '01'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Februari' THEN '02'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Maret' THEN '03'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'April' THEN '04'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Mei' THEN '05'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Juni' THEN '06'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Juli' THEN '07'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Agustus' THEN '08'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'September' THEN '09'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Oktober' THEN '10'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'November' THEN '11'
        WHEN SUBSTRING_INDEX(bulan_inden, '-', 1) = 'Desember' THEN '12'
        END";
    
    $rawOrderBy = "CONCAT(RIGHT(bulan_inden, 4), '-', $caseStatement)";

    $data = InventoryInden::orderByRaw($rawOrderBy . ' DESC')->get();
    
    return view('inven_inden.index', compact('data'));
    }


    public function create()
    {
        $produks = Produk::all();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        // return $suppliers;
        return view('inven_inden.create', compact('produks', 'suppliers'));
    }

    public function store(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kode_produk_inden' => 'required',
            'kode_produk' => 'required',
            'supplier_id' => 'required',
            'jumlah' => 'required',
            'bulan_inden' => 'required',
        ]);

        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);
        // dd($data);
        // check duplikasi
        $duplicate = InventoryInden::where('supplier_id', $data['supplier_id'])
        ->where('kode_produk_inden', $data['kode_produk_inden'])
        ->where('bulan_inden', $data['bulan_inden'])->first();
        if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryInden::create($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect()->back()->withInput()->with('success', 'Data tersimpan');
    }
    
    public function edit($inventoryinden)
    {
        $data = InventoryInden::find($inventoryinden);
        $produks = Produk::all();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        return view('inven_inden.edit', compact('data', 'produks', 'suppliers'));
    }
    public function update(Request $req, $inventoryinden)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'kode_produk_inden' => 'required',
            'kode_produk' => 'required',
            'supplier_id' => 'required',
            'jumlah' => 'required',
            'bulan_inden' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // check duplikasi
        // $duplicate = InventoryInden::where('kode_produk', $data['kode_produk'])->where('supplier_id', $data['supplier_id'])->where('bulan_inden', $data['bulan_inden'])->where('id', '!=', $inventoryinden)->first();
        // if($duplicate) return redirect()->back()->withInput()->with('fail', 'Produk sudah ada');

         // save data inven galeri
         $check = InventoryInden::find($inventoryinden)->update($data);
         if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');

         return redirect(route('inven_inden.index'))->with('success', 'Data tersimpan');
    }

    public function destroy($inventoryinden)
    {
        $data = InventoryInden::find($inventoryinden);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
