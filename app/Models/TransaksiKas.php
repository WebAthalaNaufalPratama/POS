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
        'lokasi_penerima',
        'lokasi_pengirim',
        'rekening_penerima',
        'rekening_pengirim',
        'nominal',
        'tanggal',
        'file',
        'status',
        'keterangan',
    ];

    public function lok_penerima()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_penerima');
    }

    public function lok_pengirim()
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_pengirim');
    }

    public function rek_penerima()
    {
        return $this->belongsTo(Rekening::class, 'rekening_penerima');
    }

    public function rek_pengirim()
    {
        return $this->belongsTo(Rekening::class, 'rekening_pengirim');
    }
}
