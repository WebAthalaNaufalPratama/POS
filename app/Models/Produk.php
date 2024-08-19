<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function tipe(){
        return $this->belongsTo(Tipe_Produk::class, 'tipe_produk', 'id');
    }
    public function produkbeli (){
        return $this->hasMany(Produkbeli::class);
    }
}
