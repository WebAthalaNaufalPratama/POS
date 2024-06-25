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

    public function lokasi_penerima(){
        return $this->belongsTo(Lokasi::class, 'penerima', 'id');
    }

    public function produkMutasi(){
        return $this->hasMany(Produk_Terjual::class, 'no_mutasigo', 'no_mutasi');
    }

    public function produkMutasiOutlet(){
        return $this->hasMany(Produk_Terjual::class, 'no_mutasiog', 'no_mutasi');
    }

    public function dibuat(){
        return $this->belongsTo(User::class, 'pembuat_id', 'id');
    }

    public function diterima(){
        return $this->belongsTo(User::class, 'penerima_id', 'id');
    }

    public function pengirim(){
        return $this->belongsTo(Lokasi::class, 'pengirim', 'id');
    }
}
