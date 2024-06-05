<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class PemakaianSendiri extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'lokasi_id',
        'produk_id',
        'kondisi_id',
        'karyawan_id',
        'jumlah',
        'alasan'
    ];

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function kondisi()
    {
        return $this->belongsTo(Kondisi::class);
    }

    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class);
    }
}
