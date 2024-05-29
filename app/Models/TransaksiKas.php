<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

class TransaksiKas extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;
    protected $guarded = ['id'];
    protected static $logAttributes = [
        'akun_id',
        'lokasi_id',
        'keterangan',
        'kuantitas',
        'harga_satuan',
        'harga_total',
        'tanggal_transaksi',
        'status',
        'bukti',
    ];

    public function akun()
    {
        return $this->belongsTo(Akun::class);
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class);
    }
}
