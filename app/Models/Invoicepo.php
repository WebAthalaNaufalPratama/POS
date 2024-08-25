<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Invoicepo extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected $table = 'invoicepo';
    protected static $logAttributes = [
        'pembelian_id',
        'poinden_id',
        'tgl_inv',
        'no_inv',
        'pembuat',
        'status_dibuat',
        'pembuku',
        'status_dibuku',
        'tgl_dibuat',
        'tgl_dibukukan',
        'subtotal',
        'diskon',
        'persen_ppn',
        'ppn',
        'biaya_kirim',
        'total_tagihan',
        'dp',
        'sisa'
    ];

    public function pembelian(){
        return $this->belongsTo(Pembelian::class, 'pembelian_id', 'id');
    }
    
    public function poinden(){
        return $this->belongsTo(Poinden::class, 'poinden_id', 'id');
    }
    public function pembuat(){
        return $this->belongsTo(User::class, 'pembuat', 'id');
    }
    public function pembuku(){
        return $this->belongsTo(User::class, 'pembuku', 'id');
    }
    public function retur(){
        return $this->hasOne(Returpembelian::class, 'invoicepo_id', 'id');
    }
    public function pembayaran(){
        return $this->hasMany(Pembayaran::class, 'invoice_purchase_id');
    }
}
