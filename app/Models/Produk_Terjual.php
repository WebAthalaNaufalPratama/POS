<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk_Terjual extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'produk_terjuals';
    protected $guarded = ['id'];

    public function komponen(){
        return $this->hasMany(Komponen_Produk_Terjual::class, 'produk_terjual_id', 'id');
    }

    public function produk(){
        return $this->belongsTo(Produk_Jual::class, 'produk_jual_id', 'id');
    }

    public function perangkai(){
        return $this->hasMany(FormPerangkai::class, 'no_form', 'no_form');
    }

    public function do_sewa(){
        return $this->belongsTo(DeliveryOrder::class, 'no_do', 'no_do');
    }

    public function sewa(){
        return $this->belongsTo(Kontrak::class, 'no_sewa', 'no_kontrak');
    }
}
