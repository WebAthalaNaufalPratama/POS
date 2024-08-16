<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class Returinden extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'mutasiinden_id',
        'no_retur',
        'refund',
        'total_akhir',
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

    public function mutasiinden (){
        return $this->belongsTo(Mutasiindens::class, 'mutasiinden_id', 'id');
    }
    public function produkreturinden (){
        return $this->hasMany(Produkreturinden::class, 'returinden_id', 'id');
    }
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
