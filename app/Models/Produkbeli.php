<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Produkbeli extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $table = 'produkbelis';
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'pembelian_id',
        'produk_id',
        'jml_dikirim',
        'jml_diterima',
        'kondisi_id',
        'poinden_id',
        'kode_produk_inden',
        'harga',
        'diskon',
        'totalharga',
    ];

    public function pembelian (){
        return $this->belongsTo(Pembelian::class, 'pembelian_id', 'id');
    }
    public function produk (){
        return $this->belongsTo(Produk::class, 'produk_id', 'id');
    }
    public function kondisi (){
        return $this->belongsTo(Kondisi::class, 'kondisi_id', 'id');
    }
    public function poinden (){
        return $this->belongsTo(Poinden::class, 'poinden_id', 'id');
    }
    public function produkretur()
    {
        return $this->hasOne(Produkretur::class, 'produkbeli_id', 'id');
    }
    
}
