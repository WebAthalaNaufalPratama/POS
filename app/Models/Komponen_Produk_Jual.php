<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Komponen_Produk_Jual extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'komponen_produk_juals';
    protected $guarded = ['id'];
    
    public function kondisi(){
        return $this->belongsTo(Kondisi::class, 'kondisi', 'id');
    }

    public function dataKondisi(){
        return $this->belongsTo(Kondisi::class, 'kondisi', 'id');
    }

    public function produk_jual(){
        return $this->belongsTo(Produk_Jual::class, 'produk_jual_id', 'id');
    }

    public function tipe(){
        return $this->belongsTo(Tipe_Produk::class, 'tipe_produk', 'id');
    }
}
