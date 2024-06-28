<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ReturPenjualan extends Model
{
    use HasFactory, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes =[
        'no_retur',
        'customer_id',
        'lokasi_id',
        'supplier_id',
        'bukti',
        'tanggal_invoice',
        'no_invoice',
        'tanggal_retur',
        'no_do',
        'komplain',
        'catatan_komplain',
        'pilih_pengiriman',
        'ongkir_id',
        'sub_total',
        'biaya_pengiriman',
        'total',
        'pembuat',
        'tanggal_pembuat',
        'dibukukan',
        'tanggal_dibukukan',
    ];
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
