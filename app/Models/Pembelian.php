<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Pembelian extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'no_po',
        'supplier_id',
        'lokasi_id',
        'tgl_kirim',
        'no_do_suplier',
        'file_do_suplier',
        'pembuat',
        'pemeriksa',
        'penerima',
        'tgl_dibuat',
        'tgl_diterima',
        'tgl_diperiksa',
        'status_dibuat',
        'status_diterima',
        'status_diperiksa',

    ];
    
    public function supplier (){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function lokasi (){
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
    }
    public function produkbeli (){
        return $this->hasMany(Produkbeli::class);
    }
    public function invoice (){
        return $this->hasOne(Invoicepo::class);
    }
    public function pembuat(){
        return $this->belongsTo(User::class, 'pembuat', 'id');
    }
    public function pemeriksa(){
        return $this->belongsTo(User::class, 'pemeriksa', 'id');
    }
    public function penerima(){
        return $this->belongsTo(User::class, 'penerima', 'id');
    }
    public function dibuat(){
        return $this->belongsTo(User::class, 'pembuat', 'id');
    }

}
