<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\Tipe_Produk;
use App\Models\Karyawan;
use App\Models\Lokasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if($user->hasRole(['KasirOutlet','KasirGallery', 'AdminGallery'])) {
            $karyawan = Karyawan::where('user_id', $user->id)->first();
            $customer = Customer::where('lokasi_id', $karyawan->lokasi_id)->get();
        }else{
            $customer = Customer::all();
        }
        $lokasis = Lokasi::whereIn('tipe_lokasi', [1, 2])->get();
        $thisLokasi = Auth::user()->karyawans ? Auth::user()->karyawans->lokasi_id : '';
        return view('customer.index', compact('customer', 'lokasis', 'thisLokasi'));
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
            'nama' => 'required',
            'tipe' => 'required|in:sewa,tradisional,premium',
            'handphone' => 'required|numeric|digits_between:11,13|unique:customers,handphone',
            'alamat' => 'required',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'tanggal_bergabung' => 'required|date|before_or_equal:today',
            'lokasi_id' => 'required|exists:lokasis,id',
            'poin_loyalty' => 'nullable|integer',
        ]);
        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);
        $data = $req->except(['_token', '_method', 'route']);
        $data['poin_loyalty'] = $req->input('poin_loyalty', 0);
        
        // save data
        $check = Customer::create($data);
        if(!$check) return redirect()->back()->withInput()->with('fail', 'Gagal menyimpan data');
        if($req->route){
            $route = explode(',', $req->route);
            if(count($route) == 1){
                return redirect()->route($route[0])->with('success', 'Customer ditambahkan');
            } else {
                return redirect()->route($route[0], [$route[1] => $route[2]])->with('success', 'Customer ditambahkan');
            }
        }
        return redirect()->back()->with('success', 'Data tersimpan');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        $data = Customer::find($customer);
        return response()->json($data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function edit($customer)
    {
        $data = Customer::find($customer);
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function update(Request $req, $customer)
    {
        $customer = Customer::find($customer);
        if (!$customer) {
            return redirect()->back()->with('fail', 'Data customer tidak ditemukan');
        }

        // Validasi data
        $validator = Validator::make($req->all(), [
            'nama' => 'required',
            'tipe' => 'required|in:sewa,tradisional,premium',
            'handphone' => 'required|numeric|digits_between:11,13|unique:customers,handphone,' . $customer->id,
            'alamat' => 'required',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'tanggal_bergabung' => 'required|date|before_or_equal:today',
            'lokasi_id' => 'required|exists:lokasis,id',
            'poin_loyalty' => 'nullable|integer',
        ]);

        $error = $validator->errors()->all();
        if ($validator->fails()) return redirect()->back()->withInput()->with('fail', $error);

        $data = $req->except(['_token', '_method']);
        $data['poin_loyalty'] = $req->input('poin_loyalty', 0);

        $check = $customer->update($data);
        if (!$check) {
            return redirect()->back()->withInput()->with('fail', 'Gagal memperbarui data');
        }

        return redirect()->back()->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Produk  $produk
     * @return \Illuminate\Http\Response
     */
    public function destroy($customer)
    {
        $data = Customer::find($customer);
        if(!$data) return response()->json(['msg' => 'Data tidak ditemukan'], 404);
        $check = $data->delete();
        if(!$check) return response()->json(['msg' => 'Gagal menghapus data'], 400);
        return response()->json(['msg' => 'Data berhasil dihapus']);
    }
}
