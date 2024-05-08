<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mutasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function lokasi(){
        return $this->belongsTo(Lokasi::class, 'pengirim', 'id');
    }

    public function produkMutasi(){
        return $this->hasMany(ProdukMutasi::class, 'no_mutasi', 'no_mutasi');
    }
}
