<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kondisi extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];

    public function komponen(){
        return $this->hasMany(Komponen_Produk_Jual::class, 'kondisi', 'id');
    }
    public function produkbeli (){
        return $this->hasMany(produkbeli::class);
    }

    public function kondisi_diterima()
    {
        return $this->hasMany(Kondisi::class, 'kondisi_diterima', 'id');
    }
}
