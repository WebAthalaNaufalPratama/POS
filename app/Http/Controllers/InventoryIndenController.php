<?php

namespace App\Http\Controllers;

use App\Models\InventoryInden;
use Illuminate\Http\Request;
use App\Models\Produk;
use App\Models\Kondisi;
use App\Models\Lokasi;
use App\Models\Supplier;
use App\Models\Mutasiindens;
use App\Models\Poinden;
use App\Models\Tipe_Produk;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Validator;

class InventoryIndenController extends Controller
{
    public function index(Request $req)
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

    $namaproduks = InventoryInden::with('produk')->get()->unique('kode_produk');
    $produks = InventoryInden::with('produk.tipe', 'supplier')->orderBy('bulan_inden')->orderBy('kode_produk')->get();
    $uniqueProduks = $produks->groupBy('kode_produk')->map(function ($items) {
        return [
            'kode_produk' => $items->first()->kode_produk,
            'nama_produk' => $items->first()->produk->nama
        ];
    })->values();
    $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
    $periodes = InventoryInden::get()->unique('bulan_inden');
    $tipe_produks = Tipe_Produk::where('kategori', 'Master')->get();

    // start datatable inventory
        if ($req->ajax() && $req->table == 'inventory') {
            $query = InventoryInden::with('produk', 'supplier')->orderBy('kode_produk', 'asc')->orderBy('bulan_inden', 'asc');
            
            if ($req->has('produk') && !empty($req->produk)) {
                $query->whereIn('kode_produk', $req->produk);
            }
            if ($req->filled('tipe_produk')) {
                $query->whereHas('produk.tipe', function ($q) use ($req) {
                    $q->whereIn('id', (array) $req->tipe_produk);
                });
            }
            if ($req->has('supplier') && !empty($req->supplier)) {
                $query->whereIn('supplier_id', $req->supplier);
            }
            if ($req->has('periode') && !empty($req->periode)) {
                $query->whereIn('bulan_inden', $req->periode);
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
                    ->orWhere('bulan_inden', 'like', "%$search%")
                    ->orWhere('kode_produk_inden', 'like', "%$search%")
                    ->orWhere('kode_produk', 'like', "%$search%")
                    ->orWhereHas('supplier', function($c) use($search){
                        $c->where('nama', 'like', "%$search%");
                    })
                    ->orWhereHas('produk', function($c) use($search){
                        $c->where('nama', 'like', "%$search%");
                    })
                    ->orWhereHas('produk.tipe', function($c) use($search){
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
                $item->tipe_produk = $item->produk->tipe->nama;
                return $item;
            });
            $total_jumlah = $data->sum('jumlah');

            return response()->json([
                'draw' => $req->input('draw'),
                'recordsTotal' => InventoryInden::count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
                'total_jumlah' => $total_jumlah,
            ]);

        }
    // end datatable inventory
    // $query = InventoryInden::orderByRaw($rawOrderBy . ' DESC');
    // if ($req->produk) {
    //     $query->where('kode_produk', $req->input('produk'));
    // }
    // if ($req->supplier) {
    //     $query->where('supplier_id', $req->input('supplier'));
    // }
    // if ($req->periode) {
    //     $query->where('bulan_inden', $req->input('periode'));
    // }
    // $data = $query->get();

    //log inventory
    $pomasukpo = Poinden::where('status_dibuat', 'DIKONFIRMASI')
                ->where('status_diperiksa', 'DIKONFIRMASI')
                ->with('produkbeli')->get(); 
 
    $keluarmutasi = Mutasiindens::where('status_dibuat', 'DIKONFIRMASI')
                ->where('status_diperiksa', 'DIKONFIRMASI')
                ->with('produkmutasi')->get();
                
    $riwayat = collect();

    if($keluarmutasi) {
        foreach ($keluarmutasi as $produk) {
            $produkActivity = Activity::where('subject_type', Mutasiindens::class)
                ->where('subject_id', $produk->id)
                ->orderBy('id', 'desc')
                ->first();

            if ($produkActivity) {
                $produkActivity->jenis = 'Produk Mutasi';
                $produkActivity->produkmutasi = $produk->produkmutasi;
                $riwayat->push($produkActivity);
            }
        }

    }

    if($pomasukpo) {
        foreach ($pomasukpo as $produk) {
            $produkActivity = Activity::where('subject_type', Poinden::class)
                ->where('subject_id', $produk->id)
                ->orderBy('id', 'desc')
                ->first();

            if ($produkActivity) {
                $produkActivity->jenis = 'Produk Beli';
                $produkActivity->produkbeli = $produk->produkbeli;
                $riwayat->push($produkActivity);
            }
        }

    }

    

    $riwayat = $riwayat->sortByDesc('id')->values();

    // dd($riwayat);
    
    return view('inven_inden.index', compact('namaproduks', 'suppliers', 'periodes', 'riwayat', 'produks', 'uniqueProduks', 'tipe_produks'));
    }


    public function create()
    {
        $bulan = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
    
        $currentYear = date('Y');
        $numberOfYears = 5;
    
        $periodes = [];

        for ($year = $currentYear; $year < $currentYear + $numberOfYears; $year++) {
            foreach ($bulan as $month) {
                $periodes[] = "$month-$year";
            }
        }
        $produks = Produk::all();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        return view('inven_inden.create', compact('produks', 'suppliers', 'periodes'));
    }

    public function store(Request $req)
    {
        // Validation rules for dynamic fields
        $validator = Validator::make($req->all(), [
            'kode_produk' => 'required|array',
            'bulan_inden' => 'required|array',
            'supplier_id' => 'required|array',
            'kode_produk_inden' => 'required|array',
            'jumlah' => 'required|array',
            'kode_produk.*' => 'required',
            'bulan_inden.*' => 'required',
            'supplier_id.*' => 'required|integer',
            'kode_produk_inden.*' => 'required|string',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('fail', $validator->errors()->all());
        }

        DB::beginTransaction();

        try {
            foreach ($req->kode_produk as $index => $kode_produk) {
                $bulan_inden = $req->bulan_inden[$index];
                $supplier_id = $req->supplier_id[$index];
                $kode_produk_inden = $req->kode_produk_inden[$index];
                $jumlah = $req->jumlah[$index];

                // Check for duplicates based on the product, supplier, and period
                $duplicate = InventoryInden::where('kode_produk', $kode_produk)
                    ->where('bulan_inden', $bulan_inden)
                    ->where('supplier_id', $supplier_id)
                    ->first();

                if ($duplicate) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('fail', "Produk {$kode_produk} dengan periode dan supplier tersebut sudah ada.");
                }

                // Create a new InventoryInden record
                InventoryInden::create([
                    'kode_produk' => $kode_produk,
                    'bulan_inden' => $bulan_inden,
                    'supplier_id' => $supplier_id,
                    'kode_produk_inden' => $kode_produk_inden,
                    'jumlah' => $jumlah,
                ]);
            }

            DB::commit();
            return redirect(route('inven_inden.index'))->with('success', 'Data berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('fail', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
        }
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

    public function show($inventoryInden)
    {
        $data = InventoryInden::find($inventoryInden);
        $produks = Produk::all();
        $suppliers = Supplier::where('tipe_supplier', 'inden')->get();
        return view('inven_inden.show', compact('data', 'produks', 'suppliers'));
    }

}
