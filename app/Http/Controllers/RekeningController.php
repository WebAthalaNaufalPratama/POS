<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rekening;
use App\Models\Lokasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RekeningController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Rekening::with('lokasi');

            // filter
            // if ($request->has('produk') && !empty($request->produk)) {
            //     $query->whereIn('id', $request->produk);
            // }
        
            // if ($request->has('tipe_produk') && $request->tipe_produk != '') {
            //     $query->where('tipe_produk', $request->tipe_produk);
            // }

            // if ($request->has('satuan') && $request->satuan != '') {
            //     $query->where('satuan', $request->satuan);
            // }

            $start = $request->input('start');
            $length = $request->input('length');
            $order = $request->input('order')[0]['column'];
            $dir = $request->input('order')[0]['dir'];
            $columnName = $request->input('columns')[$order]['data'];

            // search
            $search = $request->input('search.value');
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('jenis', 'like', "%$search%")
                    ->orWhere('bank', 'like', "%$search%")
                    ->orWhere('nomor_rekening', 'like', "%$search%")
                    ->orWhere('nama_akun', 'like', "%$search%")
                    ->orWhere('saldo_awal', 'like', "%$search%")
                    ->orWhereHas('lokasi', function($c) use($search){
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
                $item->saldo_awal_format = formatRupiah($item->saldo_awal);
                $item->saldo_akhir_format = formatRupiah($item->saldo_akhir);
                $item->lokasi_value = $item->lokasi->nama;
                $item->canEdit = in_array('rekening.index', $permission);
                $item->canDelete = in_array('rekening.index', $permission);
                return $item;
            });

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => Rekening::count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);

        }
        $rekenings = Rekening::all();
        $lokasis = Lokasi::all();
        return view('rekening.index', compact('rekenings', 'lokasis'));
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
            'bank' => 'nullable',
            'nomor_rekening' => 'nullable|numeric|unique:rekenings,nomor_rekening',
            'nama_akun' => 'nullable',
            'lokasi_id' => 'required|exists:lokasis,id',
            'saldo_awal' => 'nullable'
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        if ($req->jenis === 'Cash' && Rekening::where('lokasi_id', $req->lokasi_id)->exists()) {
            return redirect()->back()->withInput()->with('fail', 'Data sudah ada');
        }
        $data = $req->except(['_token', '_method']);

        // save data
        $data['saldo_akhir'] = $data['saldo_awal'];
        $check = Rekening::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(Rekening $rekening)
    {
        $data = Rekening::find($rekening);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function edit($rekening)
    {
        $data = Rekening::find($rekening);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $rekening)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'bank' => 'nullable',
            'nomor_rekening' => 'nullable|numeric|unique:rekenings,nomor_rekening,'.$rekening,
            'nama_akun' => 'nullable',
            'lokasi_id' => 'required|exists:lokasis,id',
            'saldo_awal' => 'nullable'
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // update data
        $check = Rekening::find($rekening)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data berhsail diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy($rekening)
    {
        $data = Rekening::find($rekening);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
