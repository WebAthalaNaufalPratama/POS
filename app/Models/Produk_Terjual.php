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
        return $this->hasMany(Komponen_Produk_Terjual::class, 'produk_terjual
        _id', 'id');
    }
}
