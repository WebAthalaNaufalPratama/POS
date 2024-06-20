<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturPenjualan extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
    }

    public function produk(){
        return $this->hasMany(Produk_Terjual::class, 'no_do', 'no_do');
    }
    public function produk_retur(){
        return $this->hasMany(Produk_Terjual::class, 'no_retur', 'no_retur');
    }

    public function deliveryorder(){
        return $this->hasMany(DeliveryOrder::class, 'no_referensi', 'no_retur');
    }
    public function dibuat()
    {
        return $this->hasMany(User::class, 'id', 'pembuat');
    }

}
