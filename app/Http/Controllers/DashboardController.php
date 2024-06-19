<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Lokasi;

class DashboardController extends Controller
{
    public function index()
    {

        $lokasis = Lokasi::all();
        return view('dashboard.index', compact('lokasis'));
    }

    public function update_auditor(Request $req){

        $user = Auth::user();
        if ($user) {
            $karyawan = Karyawan::where('user_id', $user->id)->first();

            if ($karyawan) {
                $update = $karyawan->update([
                    'lokasi_id' => $req->lokasi_id,
                ]);
                if ($update) {
                    echo 'berhasil';
                } else {
                    echo 'gagal';
                }
            } else {
                $save = Karyawan::create([
                    'user_id' => $user->id,
                    'nama' => $user->name,
                    'jabatan' => 'auditor',
                    'lokasi_id' => $req->lokasi_id,
                    'handphone' => 0,
                    'alamat' => '-',
                ]);
                if ($save) {
                    echo 'berhasil';
                } else {
                    echo 'gagal';
                }
            }
        } else {
            echo 'User not authenticated';
        }
    }
}
