<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Mutasiindens extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'no_mutasi',
        'supplier_id',
        'lokasi_id',
        'tgl_kirim',
        'tgl_diterima',
        'biaya_pengiriman',
        'biaya_perawatan',
        'total_biaya',
        'sisa_bayar',
        'pembuat_id',
        'penerima_id',
        'pembuku_id',
        'pemeriksa_id',
        'tgl_dibuat',
        'tgl_diterima_ttd',
        'tgl_dibukukan',
        'tgl_diperiksa',
        'status_dibuat',
        'status_diterima',
        'status_dibukukan',
        'status_diperiksa',

    ];

    public function returinden (){
        return $this->hasOne(Returinden::class, 'mutasiinden_id', 'id');
    }
    
    public function supplier (){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function lokasi (){
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
    }
    public function produkmutasi(){
        return $this->hasMany(ProdukMutasiInden::class, 'mutasiinden_id', 'id');
    }
    // public function invoice (){
    //     return $this->hasOne(Invoicepo::class);
    // }
    public function pembuat(){
        return $this->belongsTo(User::class, 'pembuat_id', 'id');
    }
    public function penerima(){
        return $this->belongsTo(User::class, 'penerima_id', 'id');
    }
    public function pembuku(){
        return $this->belongsTo(User::class, 'pembuku_id', 'id');
    }
    public function pemeriksa(){
        return $this->belongsTo(User::class, 'pemeriksa_id', 'id');
    }

}
