<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ProdukMutasi extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function komponen(){
        return $this->hasMany(Komponen_Produk_Terjual::class, 'produk_terjual_id', 'id');
    }

    public function produk(){
        return $this->belongsTo(Produk_Jual::class, 'produk_jual_id', 'id');
    }
}
