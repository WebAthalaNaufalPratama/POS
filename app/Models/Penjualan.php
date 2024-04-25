<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_customer');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class,'lokasi_id');
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class,'employee_id');
    }

    public function rekening()
    {
        return $this->belongsTo(Rekening::class,'rekening_id');
    }

    public function promo()
    {
        return $this->belongsTo(Promo::class,'promo_id');
    }

    public function ongkir()
    {
        return $this->belongsTo(Ongkir::class,'ongkir_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
    public function produk(){
        return $this->hasMany(Produk_Terjual::class, 'no_invoice');
    }

}
