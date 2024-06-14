<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Produkretur extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected $table = 'produk_retur';
    protected static $logAttributes = [
        'returpembelian_id',
        'produk_id',
        'alasan',
        'jumlah',
        'harga',
        'diskon',
        'totharga',
      
    ];
    public function produk (){
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }
    public function retur (){
        return $this->belongsTo(Returpembelian::class, 'returpembelian_id', 'id');
    }
    public function produkbeli()
    {
        return $this->belongsTo(Produkbeli::class, 'produk_id');
    }
}
