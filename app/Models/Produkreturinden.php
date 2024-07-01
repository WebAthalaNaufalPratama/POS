<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Produkreturinden extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'returinden_id',
        'produk_mutasi_inden_id',
        'alasan',
        'jml_diretur',
        'harga_satuan',
        'totalharga'
    ];

    public function returinden (){
        return $this->belongsTo(Returinden::class, 'returinden_id', 'id');
    }
    public function produk (){
        return $this->belongsTo(ProdukMutasiInden::class, 'produk_mutasi_inden_id', 'id');
    }
}
