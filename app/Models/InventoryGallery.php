<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class InventoryGallery extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'kode_produk',
        'jumlah',
        'lokasi_id',
        'min_stok'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'kode_produk', 'kode');
    }
    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class);
    }
    public function gallery()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
    }
}
