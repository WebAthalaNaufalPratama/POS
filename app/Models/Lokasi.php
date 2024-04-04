<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lokasi extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function tipe(){
        return $this->belongsTo(Tipe_Lokasi::class, 'tipe_lokasi', 'id');
    }

    public function ongkir(){
        return $this->hasMany(Ongkir::class);
    }

    public function karyawan()
    {
        return $this->hasMany(Karyawan::class);
    }

    public function rekening()
    {
        return $this->hasMany(Rekening::class);
    }
    
    public function aset()
    {
        return $this->hasMany(Aset::class);
    }

    public function promo()
    {
        return $this->hasMany(Promo::class);
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}
