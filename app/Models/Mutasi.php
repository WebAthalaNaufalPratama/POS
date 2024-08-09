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

    public function produkMutasiGG(){
        return $this->hasMany(Produk_Terjual::class, 'no_mutasigg', 'no_mutasi');
    }

    public function produkMutasiGAG(){
        return $this->hasMany(Produk_Terjual::class, 'no_mutasigag', 'no_mutasi');
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
    public function diperiksa(){
        return $this->belongsTo(User::class, 'diperiksa_id', 'id');
    }

    public function dibuku(){
        return $this->belongsTo(User::class, 'dibuku_id', 'id');
    }

    public function rekening() {
        return $this->belongsTo(Rekening::class, 'rekening_id');
    }
}
