<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Penjualan extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded = ['id'];

    protected static $logAttributes =[
        'id_customer',
        'point_dipakai',
        'lokasi_id',
        'distribusi',
        'no_invoice',
        'tanggal_invoice',
        'jatuh_tempo',
        'employee_id',
        'status',
        'bukti_file',
        'notes',
        'cara_bayar',
        'rekening_id',
        'jumlahCash',
        'pilih_pengiriman',
        'ongkir_id',
        'alamat_tujuan',
        'biaya_ongkir',
        'sub_total',
        'promo_id',
        'total_promo',
        'jenis_ppn',
        'persen_ppn',
        'jumlah_ppn',
        'total_tagihan',
        'dp',
        'sisa_bayar',
        'dibuat_id',
        'tanggal_dibuat',
        'dibukukan_id',
        'tanggal_dibukukan',
        'auditor_id',
        'tanggal_audit'
    ];


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
        return $this->hasMany(Produk_Terjual::class, 'no_invoice', 'no_invoice');
    }

    public function deliveryorder(){
        return $this->hasMany(DeliveryOrder::class, 'no_referensi', 'no_invoice');
    }

    public function dibuat()
    {
        return $this->hasMany(User::class, 'id', 'dibuat_id');
    }

}
