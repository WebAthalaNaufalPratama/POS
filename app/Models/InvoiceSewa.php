<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class InvoiceSewa extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'no_invoice',
        'no_sewa',
        'tanggal_invoice',
        'jatuh_tempo',
        'catatan',
        'rekening_id',
        'total_tagihan',
        'dp',
        'sisa_bayar',
        'status',
        'sales',
        'pembuat',
        'penyetuju',
        'pemeriksa',
        'tanggal_sales',
        'tanggal_pembuat',
        'tanggal_penyetuju',
        'tanggal_pemeriksa',
    ];

    public function kontrak(){
        return $this->belongsTo(Kontrak::class, 'no_sewa', 'no_kontrak');
    }
    public function produk(){
        return $this->hasMany(Produk_Terjual::class, 'no_invoice', 'no_invoice');
    }
    public function data_sales(){
        return $this->belongsTo(Karyawan::class, 'sales', 'id');
    }
        
    public function data_pembuat(){
        return $this->belongsTo(User::class, 'pembuat', 'id');
    }
        
    public function data_penyetuju(){
        return $this->belongsTo(User::class, 'penyetuju', 'id');
    }
        
    public function data_pemeriksa(){
        return $this->belongsTo(User::class, 'pemeriksa', 'id');
    }

    public function pembayaran(){
        return $this->hasMany(Pembayaran::class, 'invoice_sewa_id', 'id');
    }

    public function rekening(){
        return $this->belongsTo(Rekening::class);
    }
}
