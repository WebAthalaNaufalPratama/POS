<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ongkir extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function lokasi(){
        return $this->belongsTo(Lokasi::class);
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}
