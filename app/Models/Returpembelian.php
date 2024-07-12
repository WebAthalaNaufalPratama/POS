<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Returpembelian extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected $table = 'returpembelians';
    protected static $logAttributes = [
        'invoicepo_id',
        'tgl_retur',
        'komplain',
        'foto',
        'subtotal',
        'ongkir',
        'total',
        'pembuat',
        'status_dibuat',
        'pembuku',
        'status_dibuku',
        'tgl_dibuat',
        'tgl_dibuku',
    ];

    
    public function pembuat(){
        return $this->belongsTo(User::class, 'pembuat', 'id');
    }
    public function pembuku(){
        return $this->belongsTo(User::class, 'pembuku', 'id');
    }
    public function invoice(){
        return $this->belongsTo(Invoicepo::class, 'invoicepo_id', 'id');
    }
    public function produkretur (){
        return $this->hasMany(Produkretur::class, 'returpembelian_id', 'id');
    }
    public function dibuat(){
        return $this->belongsTo(User::class, 'pembuat', 'id');
    }
   
}
