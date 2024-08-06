<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class ProdukMutasiInden extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected $table = 'produk_mutasi_indens';
    protected static $logAttributes = [
        'mutasiinden_id',
        'inventoryinden_id',
        'jml_dikirim',
        'jml_diterima',
        'kondisi_id',
        'biaya_rawat',
        'totalharga'
    ];

    public function mutasiinden (){
        return $this->belongsTo(Mutasiindens::class, 'mutasiinden_id', 'id');
    }
    
    public function produk (){
        return $this->belongsTo(InventoryInden::class, 'inventoryinden_id', 'id');
    }
    public function kondisi (){
        return $this->belongsTo(Kondisi::class, 'kondisi_id', 'id');
    }
}
