<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promo extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }
    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
    public function free_produk()
    {
        return $this->belongsTo(Produk_Jual::class, 'diskon_free_produk', 'kode');
    }
    public function ketentuan_produk()
    {
        return $this->belongsTo(Produk_Jual::class, 'ketentuan_produk', 'kode');
    }
    public function ketentuan_tipe_produk()
    {
        return $this->belongsTo(Tipe_Produk::class, 'ketentuan_tipe_produk', 'id');
    }
    
}
