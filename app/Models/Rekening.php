<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class,'lokasi_id');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

}
