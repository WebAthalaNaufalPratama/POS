<?php

namespace App\Http\Controllers;

use App\Exports\OngkirExport;
use App\Models\Lokasi;
use App\Models\Ongkir;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class OngkirController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Ongkir::with('lokasi');

            // filter
            if ($request->has('ongkir') && !empty($request->ongkir)) {
                $query->whereIn('id', $request->ongkir);
            }
        
            if ($request->has('lokasi') && $request->lokasi != '') {
                $query->where('lokasi_id', $request->lokasi);
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
                    $q->where('nama', 'like', "%$search%")
                    ->orWhere('biaya', 'like', "%$search%")
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
                $item->lokasi_value = $item->lokasi->nama;
                $item->biaya_format = formatRupiah($item->biaya);
                $item->canEdit = in_array('ongkir.index', $permission);
                $item->canDelete = in_array('ongkir.index', $permission);
                return $item;
            });

            return response()->json([
                'draw' => $request->input('draw'),
                'recordsTotal' => Ongkir::count(),
                'recordsFiltered' => $recordsFiltered,
                'data' => $data,
            ]);

        }
        $ongkirs = Ongkir::select('id', 'nama')->get();
        $lokasis = Lokasi::where('tipe_lokasi', 1)->get();
        return view('ongkir.index', compact('lokasis', 'ongkirs'));
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
            'nama' => 'required|unique:ongkirs,nama',
            'lokasi_id' => 'required|integer',
            'biaya' => 'required|integer',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // save data
        $check = Ongkir::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Ongkir  $ongkir
     * @return \Illuminate\Http\Response
     */
    public function show(Ongkir $ongkir)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Ongkir  $ongkir
     * @return \Illuminate\Http\Response
     */
    public function edit($ongkir)
    {
        $data = Ongkir::find($ongkir);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Ongkir  $ongkir
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $ongkir)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required|unique:ongkirs,nama,'.$ongkir,
            'lokasi_id' => 'required|integer',
            'biaya' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // update data
        $check = Ongkir::find($ongkir)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data berhsail diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Ongkir  $ongkir
     * @return \Illuminate\Http\Response
     */
    public function destroy($ongkir)
    {
        $data = Ongkir::find($ongkir);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }

    public function pdf(Request $request)
    {
        $query = Ongkir::query();
        // filter
        if ($request->has('ongkir') && !empty($request->ongkir)) {
            $query->whereIn('id', $request->ongkir);
        }
    
        if ($request->has('lokasi') && $request->lokasi != '') {
            $query->where('lokasi_id', $request->lokasi);
        }

        $data = $query->get()->map(function($item) {
            $item->lokasi_value = $item->lokasi->nama;
            return $item;
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        $pdf = Pdf::loadView('ongkir.pdf', compact('data'))->setPaper('a4', 'landscape');;
        return $pdf->stream('ongkir.pdf');
    }

    public function excel(Request $request)
    {
        $query = Ongkir::query();
        // filter
        if ($request->has('ongkir') && !empty($request->ongkir)) {
            $query->whereIn('id', $request->ongkir);
        }
    
        if ($request->has('lokasi') && $request->lokasi != '') {
            $query->where('lokasi_id', $request->lokasi);
        }

        $data = $query->get()->map(function($item) {
            $item->lokasi_value = $item->lokasi->nama;
            return $item;
        });

        if($data->isEmpty()) return redirect()->back()->with('fail', 'Data kosong');
        return Excel::download(new OngkirExport($data), 'ongkir.xlsx');
    }
}
