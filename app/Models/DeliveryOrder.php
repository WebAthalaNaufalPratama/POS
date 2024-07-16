<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class DeliveryOrder extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'no_do',
        'jenis_do',
        'tanggal_awal',
        'tanggal_akhir',
        'customer_id',
        'no_hp',
        'penerima',
        'alamat',
        'catatan',
        'status',
        'file',
        'driver',
        'pembuat',
        'penyetuju',
        'pemeriksa',
        'tanggal_driver',
        'tanggal_pembuat',
        'tanggal_penyetuju',
        'tanggal_pemeriksa',
    ];

    public function kontrak(){
        return $this->belongsTo(Kontrak::class, 'no_referensi', 'no_kontrak');
    }
    public function customer(){
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }
    public function produk(){
        return $this->hasMany(Produk_Terjual::class, 'no_do', 'no_do');
    }
    public function data_pembuat(){
        return $this->belongsTo(User::class, 'pembuat', 'id');
    }
    public function data_driver(){
        return $this->belongsTo(Karyawan::class, 'driver', 'id');
    }
    public function data_penyetuju(){
        return $this->belongsTo(Karyawan::class, 'penyetuju', 'id');
    }
    public function data_pemeriksa(){
        return $this->belongsTo(Karyawan::class, 'pemeriksa', 'id');
    }

    public function produk_retur(){
        return $this->hasMany(ProdukReturJual::class, 'no_retur', 'no_referensi');
    }

    public function dibuat()
    {
        return $this->hasMany(User::class, 'id', 'pembuat');
    }
    public function diperiksa()
    {
        return $this->hasOne(User::class, 'id', 'penyetuju');
    }
    public function dibuku()
    {
        return $this->hasOne(User::class, 'id', 'pemeriksa');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'no_referensi', 'no_invoice');
    }
}
