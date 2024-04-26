<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }  
    public function deliveryorder(){
        return $this->hasMany(DeliveryOrder::class, 'id', 'driver');
    }  
}
