<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Poinden extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected $table = 'poinden';
    protected static $logAttributes = [
        'no_po',
        'supplier_id',
        'bulan_inden',
        'pembuat',
        'pemeriksa',
        'tgl_dibuat',
        'tgl_diperiksa',
        'status_dibuat',
        'status_diperiksa',
    ];
    public function supplier(){
        return $this->belongsTo(Supplier::class, 'supplier_id', 'id');
    }
    public function pembuat(){
        return $this->belongsTo(User::class, 'pembuat', 'id');
    }
    public function pemeriksa(){
        return $this->belongsTo(User::class, 'pemeriksa', 'id');
    }
    public function produkbeli(){
        return $this->hasMany(produkbeli::class);
    }
    public function invoice(){
        return $this->hasOne(Invoicepo::class);
    }


}
