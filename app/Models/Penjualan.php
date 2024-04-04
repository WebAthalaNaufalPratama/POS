<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    use HasFactory;

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

}
