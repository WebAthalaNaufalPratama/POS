<?php

namespace App\Http\Controllers;

use App\Exports\ProdukExport;
use App\Models\Produk;
use App\Models\Tipe_Produk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Produk::with('tipe');

            // filter
            if ($request->has('produk') && !empty($request->produk)) {
                $query->whereIn('id', $request->produk);
            }
        
            if ($request->has('tipe_produk') && $request->tipe_produk != '') {
                $query->where('tipe_produk', $request->tipe_produk);
            }

            if ($request->has('satuan') && $request->satuan != '') {
                $query->where('satuan', $request->satuan);
            }

            $start = $request->input('start');
            $length = $request->input('length');
            $order = $request->input('order')[0]['column'];
            $dir = $request->input('order')[0]['dir'];
            $columnName = $request->input('columns')[$order]['data'];

            // search
            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('kode', 'like', "%$search%")
                    ->orWhere('nama', 'like', "%$search%")
                    ->orWhere('satuan', 'like', "%$search%")
                    ->orWhere('deskripsi', 'like', "%$search%")
                    ->orWhereHas('tipe', function($c) use($search){
                        $c->where('nama', 'like', "%$search%");
                    });
                });
            }
    
            $query->orderBy($columnName, $dir);
            $recordsFiltered = $query->count();
            $rawData = $query->offset($start)->limit($length)->get();
    
            $currentPage = ($start / $length) + 1;
            $perPage = $length;
        
            $data = $rawData->map(function($item, $index) use ($currentPage, $perPage) {
                $permission = Auth::user()->getAllPermissions()->pluck('name')->toArray();
                $item->no = ($currentPage - 1) * $perPage + ($index + 1);
                $item->tipe_value = $item->tipe->nama;
                $item->canEdit = in_array('produks.index', $permission);
                $item->canDelete = in_array('produks.index', $permission);
                return $item;
            });

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => Produk::count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);

        }
        $produks = Produk::all();
        $tipe_produks = Tipe_Produk::where('kategori', 'master')->get();
        $satuans = $produks->pluck('satuan')->unique();
        return view('produks.index', compact('tipe_produks', 'produks', 'satuans'));
    }
 
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            'nama' => 'required|unique:produks,nama',
            'tipe_produk' => 'required|integer',
            'deskripsi' => 'required',
            'satuan' => 'nullable',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // penentuan kode produk
        $latestProduks = Produk::withTrashed()->orderBy('kode', 'desc')->get();
        if(count($latestProduks) < 1){
            $getKode = 'PRD-000001';
        } else {
            $lastProduk = $latestProduks->first();
            $kode = explode('-', $lastProduk->kode);
            $getKode = 'PRD-' . str_pad((int)$kode[1] + 1, 6, '0', STR_PAD_LEFT);
        }
        $data['kode'] = $getKode;

        // save data
        $check = Produk::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    public function store_produk_po(Request $req)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required',
            'tipe_produk' => 'required|integer',
            'deskripsi' => 'required',
        ]);
    
        if ($validator->fails()) {
            // Mengembalikan error dalam format JSON untuk AJAX
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ], 400);
        }
    
        $data = $req->except(['_token', '_method']);
    
        // Penentuan kode produk
        $latestProduks = Produk::withTrashed()->orderBy('kode', 'desc')->get();
        if(count($latestProduks) < 1){
            $getKode = 'PRD-000001';
        } else {
            $lastProduk = $latestProduks->first();
            $kode = explode('-', $lastProduk->kode);
            $getKode = 'PRD-' . str_pad((int)$kode[1] + 1, 6, '0', STR_PAD_LEFT);
        }
        $data['kode'] = $getKode;
    
        // Simpan data
        $produk = Produk::create($data);
    
        if (!$produk) {
            // Jika gagal menyimpan, kembalikan pesan error
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data'
            ], 500);
        }
    
        // Mengembalikan respons sukses dengan data produk yang baru dibuat
        return response()->json([
            'success' => true,
            'message' => 'Data tersimpan',
            'produk' => $produk
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(Produk $produk)
    {
        $data = Produk::find($produk);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function edit($produk)
    {
        $data = Produk::find($produk);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $produk)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required|unique:produks,nama,' .$produk,
            'tipe_produk' => 'required|integer',
            'deskripsi' => 'required',
            'satuan' => 'nullable',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // update data
        $check = Produk::find($produk)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data berhsail diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy($produk)
    {
        $data = Produk::find($produk);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function pdf(Request $request)
    {
        $query = Produk::query();
        // filter
        if ($request->has('produk') && !empty($request->produk)) {
            $query->whereIn('id', $request->produk);
        }
    
        if ($request->has('tipe_produk') && $request->tipe_produk != '') {
            $query->where('tipe_produk', $request->tipe_produk);
        }

        if ($request->has('satuan') && $request->satuan != '') {
            $query->where('satuan', $request->satuan);
        }

        $data = $query->get()->map(function($item) {
            $item->tipe_value = $item->tipe->nama;
            return $item;
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('produks.pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('produk.pdf');
    }

    public function excel(Request $request)
    {
        $query = Produk::query();
        // filter
        if ($request->has('produk') && !empty($request->produk)) {
            $query->whereIn('id', $request->produk);
        }
    
        if ($request->has('tipe_produk') && $request->tipe_produk != '') {
            $query->where('tipe_produk', $request->tipe_produk);
        }

        if ($request->has('satuan') && $request->satuan != '') {
            $query->where('satuan', $request->satuan);
        }

        $data = $query->get()->map(function($item) {
            $item->tipe_value = $item->tipe->nama;
            return $item;
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new ProdukExport($data), 'produk.xlsx');
    }
}
