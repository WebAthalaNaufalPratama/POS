<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class InventoryInden extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected $table = 'inventory_indens';
    protected static $logAttributes = [
        'supplier_id',
        'kode_produk_inden',
        'bulan_inden',
        'kode_produk',
        'jumlah'
    ];
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'kode_produk', 'kode');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

}
