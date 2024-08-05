<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Produk_Terjual extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $table = 'produk_terjuals';
    protected $guarded = ['id'];
    protected static $logAttributes =[
        'produk_jual_id',
        'no_invoice',
        'no_do',
        'no_sewa',
        'no_mutasi',
        'no_form',
        'no_retur',
        'no_mutasigo',
        'no_mutasiog',
        'no_mutasigg',
        'no_mutasigag',
        'no_kembali_sewa',
        'harga',
        'alasan',
        'jumlah_dikirim',
        'jumlah_diterima',
        'kondisi_dikirim',
        'kondisi_diterima',
        'jumlah',
        'satuan',
        'keterangan',
        'jenis',
        'detail_lokasi',
        'jenis_diskon',
        'diskon',
        'harga_jual',
    ];

    public function komponen(){
        return $this->hasMany(Komponen_Produk_Terjual::class, 'produk_terjual_id', 'id');
    }

    public function produk(){
        return $this->belongsTo(Produk_Jual::class, 'produk_jual_id', 'id');
    }

    public function perangkai(){
        return $this->hasMany(FormPerangkai::class, 'no_form', 'no_form');
    }

    public function do_sewa(){
        return $this->belongsTo(DeliveryOrder::class, 'no_do', 'no_do');
    }

    public function sewa(){
        return $this->belongsTo(Kontrak::class, 'no_sewa', 'no_kontrak');
    }

    public function kembali_sewa(){
        return $this->belongsTo(KembaliSewa::class,'no_kembali_sewa', 'no_kembali');
    }

    public function penjualan(){
        return $this->belongsTo(Penjualan::class, 'no_invoice', 'no_invoice');
    }

    public function mutasi(){
        return $this->belongsTo(Mutasi::class, 'no_mutasigo', 'no_mutasi');
    }

    public function mutasiog(){
        return $this->belongsTo(Mutasi::class, 'no_mutasiog', 'no_mutasi');
    }

    public function mutasigg(){
        return $this->belongsTo(Mutasi::class, 'no_mutasigg', 'no_mutasi');
    }

    public function mutasigag(){
        return $this->belongsTo(Mutasi::class, 'no_mutasigag', 'no_mutasi');
    }

    public function do_penjualan(){
        return $this->belongsTo(DeliveryOrder::class, 'no_do', 'no_do');
    }

    public function retur_penjualan(){
        return $this->belongsTo(ReturPenjualan::class, 'no_retur', 'no_retur');
    }
}
