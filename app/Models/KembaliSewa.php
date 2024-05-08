<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class KembaliSewa extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'no_sewa',
        'tanggal_kembali',
        'file',
        'status',
        'driver',
        'pembuat',
        'penyetuju',
        'pemeriksa',
        'tanggal_driver',
        'tanggal_pembuat',
        'tanggal_penyetuju',
        'tanggal_pemeriksa',
    ];

    public function data_driver(){
        return $this->belongsTo(Karyawan::class, 'driver', 'id');
    }

    public function produk(){
        return $this->hasMany(Produk_Terjual::class, 'no_kembali_sewa', 'no_kembali');
    }
}
