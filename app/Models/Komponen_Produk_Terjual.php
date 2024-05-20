<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Komponen_Produk_Terjual extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'komponen_produk_terjuals';
    protected $guarded = ['id'];

    public function produk_terjual(){
        return $this->belongsTo(Produk_Terjual::class, 'produk_terjual_id', 'id');
    }

    public function produk(){
        return $this->belongsTo(Produk::class, 'kode_produk', 'kode');
    }

    public function kondisi(){
        return $this->belongsTo(Kondisi::class, 'kondisi', 'id');
    }
}
