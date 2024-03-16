<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tipe_Produk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipe_produks';
    protected $guarded = ['id'];

    public function produk(){
        return $this->hasMany(Produk::class, 'id', 'tipe_produk');
    }
}
