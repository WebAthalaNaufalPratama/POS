<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryOutlet extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function produk()
    {
        return $this->belongsTo(Produk_Jual::class, 'kode_produk', 'kode');
    }
    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class);
    }
    public function outlet()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
    }
}
