<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $suppliers = Supplier::all();
        return view('supplier.index', compact('suppliers'));
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
            'nama' => 'required|unique:suppliers,nama',
            'pic' => 'required',
            'tipe_supplier' => 'required',
            'handphone' => 'required|unique:suppliers,handphone',
            'alamat' => 'required',
            'tanggal_bergabung' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // save data
        $check = Supplier::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    public function store_sup_po(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'pic' => 'nullable|string|max:255',
            'handphone' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'tanggal_bergabung' => 'required|date',
            'tipe_supplier' => 'required|string',
        ]);

        $supplier = Supplier::create($validatedData);

        // Return data supplier baru dalam format JSON
        return response()->json([
            'success' => true,
            'supplier' => $supplier
        ]);
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function show(Supplier $supplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function edit($supplier)
    {
        $data = Supplier::find($supplier);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $supplier)
    {
        // validasi
        $validator = Validator::make($req->all(), [
            'nama' => 'required|unique:suppliers,nama,'.$supplier,
            'pic' => 'required',
            'tipe_supplier' => 'required',
            'handphone' => 'required|unique:suppliers,handphone,'.$supplier,
            'alamat' => 'required',
            'tanggal_bergabung' => 'required',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method']);

        // update data
        $check = Supplier::find($supplier)->update($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        return redirect()->back()->with('success', 'Data berhsail diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Supplier  $supplier
     * @return \Illuminate\Http\Response
     */
    public function destroy($supplier)
    {
        $data = Supplier::find($supplier);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
