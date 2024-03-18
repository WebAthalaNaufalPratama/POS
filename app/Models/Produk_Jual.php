<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk_Jual extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'produk_juals';
    protected $guarded = ['id'];

    public function tipe(){
        return $this->belongsTo(Tipe_Produk::class, 'tipe_produk', 'id');
    }

    public function komponen(){
        return $this->hasMany(Komponen_Produk_Jual::class, 'produk_jual_id', 'id');
    }
}
